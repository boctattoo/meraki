<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Sessão expirada']);
    exit();
}

require 'conexao.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['tarefa_id']) || !isset($input['novo_status'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos']);
    exit();
}

$statuses_validos = ['afazer', 'progresso', 'concluido'];
if (!in_array($input['novo_status'], $statuses_validos)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Status inválido']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Buscar status anterior
    $sql_status = "SELECT status FROM tarefas WHERE id = ?";
    $stmt_status = $pdo->prepare($sql_status);
    $stmt_status->execute([$input['tarefa_id']]);
    $status_anterior = $stmt_status->fetchColumn();
    
    if (!$status_anterior) {
        throw new Exception('Tarefa não encontrada');
    }
    
    // Atualizar status da tarefa
    $sql = "UPDATE tarefas SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$input['novo_status'], $input['tarefa_id']]);
    
    // Registrar tratativa se fornecida
    if (!empty($input['tratativa'])) {
        $sql_tratativa = "INSERT INTO tarefas_tratativas 
                         (tarefa_id, usuario_id, status_anterior, status_novo, tratativa) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmt_tratativa = $pdo->prepare($sql_tratativa);
        $stmt_tratativa->execute([
            $input['tarefa_id'],
            $_SESSION['usuario_id'],
            $status_anterior,
            $input['novo_status'],
            $input['tratativa']
        ]);
    }
    
    $pdo->commit();
    echo json_encode(['sucesso' => true]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao atualizar tarefa: ' . $e->getMessage()]);
}