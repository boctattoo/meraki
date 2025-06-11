<?php
session_start();
header('Content-Type: application/json');
require 'conexao.php';

function respostaErro($mensagem) {
    echo json_encode(['sucesso' => false, 'erro' => $mensagem]);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);

$tarefa_id = intval($dados['tarefa_id'] ?? 0);
$mensagem = trim($dados['mensagem'] ?? '');
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Validações
if ($tarefa_id <= 0 || $mensagem === '') {
    respostaErro('Tarefa ou mensagem inválida.');
}
if ($usuario_id === null) {
    respostaErro('Usuário não autenticado.');
}

try {
    $stmt = $pdo->prepare("INSERT INTO tarefas_tratativas (tarefa_id, usuario_id, mensagem) VALUES (?, ?, ?)");
    $stmt->execute([$tarefa_id, $usuario_id, $mensagem]);

    echo json_encode(['sucesso' => true]);
} catch (PDOException $e) {
    respostaErro('Erro ao registrar tratativa: ' . $e->getMessage());
}
