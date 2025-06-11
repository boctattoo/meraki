<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não autenticado.']);
    exit();
}

require 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

$response = ['sucesso' => false, 'usuarios' => [], 'erro' => ''];

try {
    // Prepara e executa a consulta SQL para buscar todos os usuários
    // Assumimos que a tabela de usuários tem pelo menos 'id' e 'nome'
    $stmt = $pdo->prepare("SELECT id, nome FROM usuarios ORDER BY nome ASC");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['sucesso'] = true;
    $response['usuarios'] = $usuarios;

} catch (PDOException $e) {
    $response['erro'] = 'Erro ao carregar usuários: ' . $e->getMessage();
}

echo json_encode($response);
?>