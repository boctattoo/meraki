<?php
// editar_tarefa.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não autenticado.']);
    exit();
}

require 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'] ?? null;
$titulo = trim($data['titulo'] ?? '');
$descricao = trim($data['descricao'] ?? '');
$data_evento = !empty($data['data_evento']) ? $data['data_evento'] : null;
$prioridade = trim($data['prioridade'] ?? '');
$etiqueta = trim($data['etiqueta'] ?? '');

if (!$id || empty($titulo)) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID da tarefa ou título inválido.']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE tarefas SET titulo = ?, descricao = ?, data_evento = ?, prioridade = ?, etiqueta = ? WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$titulo, $descricao, $data_evento, $prioridade, $etiqueta, $id, $usuario_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa atualizada com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'erro' => 'Tarefa não encontrada ou não pertence ao usuário, ou nenhum dado foi alterado.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao atualizar tarefa: ' . $e->getMessage()]);
}
?>