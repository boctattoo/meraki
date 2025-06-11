<?php
// criar_tarefa.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não autenticado.']);
    exit();
}

require 'conexao.php';

$usuario_logado_id = $_SESSION['usuario_id']; // Renomeado para evitar conflito com 'usuario_atribuido'
$data = json_decode(file_get_contents('php://input'), true);

$titulo = trim($data['titulo'] ?? '');
$descricao = trim($data['descricao'] ?? '');
$data_evento = !empty($data['data_evento']) ? $data['data_evento'] : null;
$prioridade = trim($data['prioridade'] ?? '');
$etiqueta = trim($data['etiqueta'] ?? '');
$usuario_atribuido_id = $data['usuario_atribuido'] ?? null; // NOVO: Captura o ID do usuário atribuído

if (empty($titulo)) {
    echo json_encode(['sucesso' => false, 'erro' => 'O título da tarefa é obrigatório.']);
    exit();
}

try {
    // Inicia uma transação para garantir que ambas as operações (criar tarefa e atribuir usuário)
    // sejam bem-sucedidas ou que nenhuma seja.
    $pdo->beginTransaction();

    // 1. Inserir a nova tarefa na tabela 'tarefas'
    // A coluna 'usuario_id' na tabela 'tarefas' pode ser o criador da tarefa.
    // O 'usuario_atribuido_id' é para quem a tarefa está designada.
    $stmt = $pdo->prepare("INSERT INTO tarefas (usuario_id, titulo, descricao, data_evento, prioridade, etiqueta) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$usuario_logado_id, $titulo, $descricao, $data_evento, $prioridade, $etiqueta]);

    $nova_tarefa_id = $pdo->lastInsertId(); // Obtém o ID da tarefa recém-criada

    // 2. Se um usuário foi atribuído, insere o vínculo na tabela 'tarefas_usuarios'
    if (!empty($usuario_atribuido_id)) {
        // Validação adicional: Verifica se o usuario_atribuido_id existe na tabela de usuários (opcional, mas recomendado)
        $stmtCheckUser = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id = ?");
        $stmtCheckUser->execute([$usuario_atribuido_id]);
        if ($stmtCheckUser->fetchColumn() > 0) {
            $stmtAtribuir = $pdo->prepare("INSERT INTO tarefas_usuarios (tarefa_id, usuario_id) VALUES (?, ?)");
            $stmtAtribuir->execute([$nova_tarefa_id, $usuario_atribuido_id]);
        } else {
            // Logar ou lidar com o erro de usuário atribuído inválido, mas não falhar a criação da tarefa
            error_log("Tentativa de atribuir tarefa a usuário_id inválido: " . $usuario_atribuido_id);
            // Poderíamos até dar um rollback aqui se a atribuição for considerada crítica, mas geralmente a criação da tarefa é mais importante.
            // Por enquanto, apenas logamos e continuamos.
        }
    } else {
        // Se nenhum usuário foi atribuído, você pode optar por atribuir ao próprio criador por padrão,
        // ou simplesmente não criar um registro em tarefas_usuarios.
        // Pelo seu design atual com dropdown "Ninguém", vamos apenas não criar o registro.
    }


    // 3. Commit da transação se tudo correu bem
    $pdo->commit();

    echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa adicionada com sucesso!', 'id' => $nova_tarefa_id]);

} catch (PDOException $e) {
    // Em caso de erro, faz rollback da transação para desfazer qualquer alteração
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao adicionar tarefa: ' . $e->getMessage()]);
}
?>