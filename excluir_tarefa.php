<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Sessão expirada']);
    exit();
}

require 'conexao.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID da tarefa não fornecido']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Verificar se a tarefa existe e pertence ao usuário ou se é admin
    $sql_check = "SELECT id, usuario_id FROM tarefas WHERE id = ?";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([$input['id']]);
    $tarefa = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if (!$tarefa) {
        throw new Exception('Tarefa não encontrada');
    }
    
    // Verificar permissão (apenas o criador ou admin pode excluir)
    $sql_user = "SELECT cargo FROM usuarios WHERE id = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$_SESSION['usuario_id']]);
    $user_cargo = $stmt_user->fetchColumn();
    
    if ($tarefa['usuario_id'] != $_SESSION['usuario_id'] && $user_cargo != 'admin') {
        throw new Exception('Sem permissão para excluir esta tarefa');
    }
    
    // Excluir tarefa (tratativas e usuários serão excluídos por CASCADE)
    $sql = "DELETE FROM tarefas WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$input['id']]);
    
    $pdo->commit();
    echo json_encode(['sucesso' => true]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao excluir tarefa: ' . $e->getMessage()]);
}