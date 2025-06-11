<?php
require 'conexao.php';

$aluno_id = $_POST['aluno_id'] ?? null;
$turma_atual = $_POST['turma_atual'] ?? null;
$nova_turma = $_POST['nova_turma_id'] ?? null;

if (!$aluno_id || !$nova_turma || !$turma_atual) {
    echo "<script>alert('Dados incompletos para alteração de turma.'); history.back();</script>";
    exit;
}

try {
    // remove da turma atual
    $stmt = $pdo->prepare("DELETE FROM alunos_turmas WHERE aluno_id = :aluno AND turma_id = :turma");
    $stmt->execute(['aluno' => $aluno_id, 'turma' => $turma_atual]);

    // insere na nova turma (se não existir)
    $stmt = $pdo->prepare("INSERT IGNORE INTO alunos_turmas (aluno_id, turma_id) VALUES (:aluno, :turma)");
    $stmt->execute(['aluno' => $aluno_id, 'turma' => $nova_turma]);

    echo "<script>location.href='mapa_turmas.php';</script>";
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro ao mover aluno: " . $e->getMessage() . "</div>";
    exit;
}
