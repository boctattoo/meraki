<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não logado']);
    exit();
}

require 'conexao.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['titulo'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Título é obrigatório']);
    exit();
}

try {
    $sql = "INSERT INTO tarefas (titulo, descricao, data_evento, prioridade, etiqueta, usuario_atribuido_id, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 'afazer', NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $input['titulo'],
        $input['descricao'] ?? '',
        $input['data_evento'] ?: null,
        $input['prioridade'] ?? '',
        $input['etiqueta'] ?? '',
        $input['usuario_atribuido'] ?: null
    ]);
    
    echo json_encode(['sucesso' => true]);
    
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao adicionar tarefa: ' . $e->getMessage()]);
}
?>