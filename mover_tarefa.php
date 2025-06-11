<?php
// mover_tarefa.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não autenticado.']);
    exit();
}

require 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

$usuario_id = $_SESSION['usuario_id'];

// Obtém o JSON do corpo da requisição
$data = json_decode(file_get_contents('php://input'), true);

$tarefa_id = $data['id'] ?? null;
$novo_status = $data['status'] ?? null;
$tratativa_texto = trim($data['tratativa'] ?? '');

// Validação básica dos dados
if (!$tarefa_id || !in_array($novo_status, ['afazer', 'progresso', 'concluido']) || empty($tratativa_texto)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados inválidos.']);
    exit();
}

try {
    // 1. Obter o status atual da tarefa para registrar a tratativa
    $stmt_current_status = $pdo->prepare("SELECT status FROM tarefas WHERE id = ? AND usuario_id = ?");
    $stmt_current_status->execute([$tarefa_id, $usuario_id]);
    $tarefa = $stmt_current_status->fetch();

    if (!$tarefa) {
        echo json_encode(['sucesso' => false, 'erro' => 'Tarefa não encontrada ou não pertence ao usuário.']);
        exit();
    }
    $status_anterior = $tarefa['status'];

    // Inicia uma transação para garantir que ambas as operações sejam bem-sucedidas ou nenhuma seja
    $pdo->beginTransaction();

    // 2. Atualizar o status da tarefa
    $stmt_update = $pdo->prepare("UPDATE tarefas SET status = ? WHERE id = ? AND usuario_id = ?");
    $stmt_update->execute([$novo_status, $tarefa_id, $usuario_id]);

    // 3. Inserir o registro da tratativa
    $stmt_tratativa = $pdo->prepare("INSERT INTO tratativas (tarefa_id, usuario_id, status_anterior, status_novo, texto) VALUES (?, ?, ?, ?, ?)");
    $stmt_tratativa->execute([$tarefa_id, $usuario_id, $status_anterior, $novo_status, $tratativa_texto]);

    $pdo->commit(); // Confirma as alterações

    echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa movida e tratativa registrada com sucesso!']);

} catch (PDOException $e) {
    $pdo->rollBack(); // Desfaz as alterações em caso de erro
    // Em um ambiente de produção, registre o erro em um log
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao mover tarefa: ' . $e->getMessage()]);
}
?>