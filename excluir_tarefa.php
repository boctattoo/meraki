<?php
// excluir_tarefa.php
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

if (!$id) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID da tarefa inválido.']);
    exit();
}

try {
    // Opcional: Se você tiver tratativas associadas, pode ser interessante excluí-las primeiro
    // $pdo->beginTransaction();
    // $stmt_delete_tratativas = $pdo->prepare("DELETE FROM tratativas WHERE tarefa_id = ?");
    // $stmt_delete_tratativas->execute([$id]);

    $stmt_delete_tarefa = $pdo->prepare("DELETE FROM tarefas WHERE id = ? AND usuario_id = ?");
    $stmt_delete_tarefa->execute([$id, $usuario_id]);

    if ($stmt_delete_tarefa->rowCount() > 0) {
        // $pdo->commit();
        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa excluída com sucesso!']);
    } else {
        // $pdo->rollBack();
        echo json_encode(['sucesso' => false, 'erro' => 'Tarefa não encontrada ou não pertence ao usuário.']);
    }

} catch (PDOException $e) {
    // $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao excluir tarefa: ' . $e->getMessage()]);
}
?>