<?php
session_start();
header('Content-Type: application/json');
require 'conexao.php';

$tarefa_id = intval($_GET['tarefa_id'] ?? 0);
if (!$tarefa_id) {
    echo json_encode([]);
    exit;
}
$stmt = $pdo->prepare("SELECT t.*, u.nome AS usuario_nome FROM tratativas t LEFT JOIN usuarios u ON t.usuario_id = u.id WHERE t.tarefa_id = ? ORDER BY t.data_tratativa DESC");
$stmt->execute([$tarefa_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
