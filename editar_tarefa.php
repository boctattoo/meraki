<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Sess�0�0o expirada']);
    exit();
}

require 'conexao.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id']) || !isset($input['titulo']) || empty(trim($input['titulo']))) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos ou t��tulo vazio']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Atualizar tarefa
    $sql = "UPDATE tarefas SET 
                titulo = ?, 
                descricao = ?, 
                data_evento = ?, 
                prioridade = ?, 
                etiqueta = ?
            WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        trim($input['titulo']),
        trim($input['descricao'] ?? ''),
        !empty($input['data_evento']) ? $input['data_evento'] : null,
        !empty($input['prioridade']) ? $input['prioridade'] : null,
        trim($input['etiqueta'] ?? ''),
        $input['id']
    ]);
    
    // Atualizar usu��rio atribu��do
    // Primeiro remover atribui�0�4�0�0o atual
    $sql_del = "DELETE FROM tarefas_usuarios WHERE tarefa_id = ?";
    $stmt_del = $pdo->prepare($sql_del);
    $stmt_del->execute([$input['id']]);
    
    // Se foi atribu��do a algu��m, inserir nova atribui�0�4�0�0o
    if (!empty($input['usuario_atribuido'])) {
        $sql_user = "INSERT INTO tarefas_usuarios (tarefa_id, usuario_id) VALUES (?, ?)";
        $stmt_user = $pdo->prepare($sql_user);
        $stmt_user->execute([$input['id'], $input['usuario_atribuido']]);
    }
    
    $pdo->commit();
    echo json_encode(['sucesso' => true]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao editar tarefa: ' . $e->getMessage()]);
}