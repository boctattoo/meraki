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
$usuarios = $dados['usuarios'] ?? [];

if ($tarefa_id <= 0 || !is_array($usuarios) || empty($usuarios)) {
    respostaErro('Tarefa ou usuários inválidos.');
}

try {
    $pdo->beginTransaction();

    // Excluir vínculos anteriores (evita duplicação)
    $stmtDel = $pdo->prepare("DELETE FROM tarefas_usuarios WHERE tarefa_id = ?");
    $stmtDel->execute([$tarefa_id]);

    // Inserir novos vínculos
    $stmtIns = $pdo->prepare("INSERT INTO tarefas_usuarios (tarefa_id, usuario_id) VALUES (?, ?)");

    foreach ($usuarios as $user_id) {
        if (is_numeric($user_id)) {
            $stmtIns->execute([$tarefa_id, intval($user_id)]);
        }
    }

    $pdo->commit();
    echo json_encode(['sucesso' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    respostaErro('Erro ao atribuir usuários: ' . $e->getMessage());
}
