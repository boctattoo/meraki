<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não logado']);
    exit();
}

require 'conexao.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['id']) || empty($input['status'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados inválidos']);
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Atualiza o status da tarefa
    $sql = "UPDATE tarefas SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$input['status'], $input['id']]);
    
    // Registra a tratativa
    if (!empty($input['tratativa'])) {
        $sqlTratativa = "INSERT INTO tratativas (tarefa_id, status_anterior, status_novo, texto, usuario_id, data_registro) 
                        VALUES (?, ?, ?, ?, ?, NOW())";
        $stmtTratativa = $pdo->prepare($sqlTratativa);
        $stmtTratativa->execute([
            $input['id'],
            $input['status_anterior'] ?? '',
            $input['status'],
            $input['tratativa'],
            $_SESSION['usuario_id']
        ]);
    }
    
    $pdo->commit();
    echo json_encode(['sucesso' => true]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao mover tarefa: ' . $e->getMessage()]);
}
?>