<?php
require 'conexao.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$turma_id = (int)($input['turma_id'] ?? 0);
$data = $input['data'] ?? null;
$presencas = $input['presencas'] ?? [];

if (!$turma_id || !$data || !is_array($presencas)) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos.']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO presencas (aluno_id, turma_id, data, presente)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE presente = VALUES(presente)
    ");

    foreach ($presencas as $p) {
        $aluno_id = (int)$p['aluno_id'];
        $presente = (bool)$p['presente'];
        $stmt->execute([$aluno_id, $turma_id, $data, $presente]);
    }

    // Atualiza a data da última presença na turma
    $pdo->prepare("UPDATE turmas SET ultima_presenca_em = ? WHERE id = ?")->execute([$data, $turma_id]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
