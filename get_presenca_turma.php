<?php
require 'conexao.php';
header('Content-Type: application/json');

$turma_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
$data = $_GET['data'] ?? null;

if (!$turma_id || !$data) {
    echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos']);
    exit;
}

try {
    // Buscar alunos da turma
    $stmt = $pdo->prepare("
        SELECT a.id, a.nome, p.presente
        FROM alunos a
        JOIN alunos_turmas at ON at.aluno_id = a.id
        LEFT JOIN presencas p ON p.aluno_id = a.id AND p.turma_id = at.turma_id AND p.data = ?
        WHERE at.turma_id = ? AND at.ativo = 1
        ORDER BY a.nome
    ");
    $stmt->execute([$data, $turma_id]);
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'alunos' => $alunos
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
