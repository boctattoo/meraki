<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Sessão expirada']);
    exit();
}

require 'conexao.php';

try {
    $sql = "SELECT id, nome FROM usuarios WHERE ativo = 1 ORDER BY nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['sucesso' => true, 'usuarios' => $usuarios]);
    
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao carregar usuários: ' . $e->getMessage()]);
}