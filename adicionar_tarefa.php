<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Sessão expirada']);
    exit();
}

require 'conexao.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['titulo']) || empty(trim($input['titulo']))) {
    echo json_encode(['sucesso' => false, 'erro' => 'Título é obrigatório']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Inserir tarefa
    $sql = "INSERT INTO tarefas (usuario_id, titulo, descricao, data_evento, prioridade, etiqueta, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'afazer')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_SESSION['usuario_id'],
        trim($input['titulo']),
        trim($input['descricao'] ?? ''),
        !empty($input['data_evento']) ? $input['data_evento'] : null,
        !empty($input['prioridade']) ? $input['prioridade'] : null,
        trim($input['etiqueta'] ?? '')
    ]);
    
    $tarefa_id = $pdo->lastInsertId();
    
    // Se foi atribuído a alguém, inserir na tabela de usuários
    if (!empty($input['usuario_atribuido'])) {
        $sql_user = "INSERT INTO tarefas_usuarios (tarefa_id, usuario_id) VALUES (?, ?)";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([$tarefa_id, $input['usuario_atribuido']]);
    }
    
    $pdo->commit();
    echo json_encode(['sucesso' => true, 'id' => $tarefa_id]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao adicionar tarefa: ' . $e->getMessage()]);
}