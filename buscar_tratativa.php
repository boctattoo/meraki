<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Sessão expirada']);
    exit();
}

require 'conexao.php';

$tarefa_id = $_GET['tarefa_id'] ?? null;

if (!$tarefa_id) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID da tarefa não fornecido']);
    exit();
}

try {
    $sql = "SELECT 
                tt.id,
                tt.status_anterior,
                tt.status_novo,
                tt.tratativa,
                tt.data_tratativa,
                u.nome as nome_usuario
            FROM tarefas_tratativas tt
            JOIN usuarios u ON tt.usuario_id = u.id
            WHERE tt.tarefa_id = ?
            ORDER BY tt.data_tratativa DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tarefa_id]);
    $tratativas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar informações da tarefa
    $sql_tarefa = "SELECT titulo FROM tarefas WHERE id = ?";
    $stmt_tarefa = $pdo->prepare($sql_tarefa);
    $stmt_tarefa->execute([$tarefa_id]);
    $tarefa = $stmt_tarefa->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'sucesso' => true, 
        'tratativas' => $tratativas,
        'tarefa' => $tarefa
    ]);
    
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao buscar tratativas: ' . $e->getMessage()]);
}