<?php
require 'conexao.php';

$turma_id = $_GET['turma_id'] ?? null;

if (!$turma_id || !is_numeric($turma_id)) {
    echo '<div class="alert alert-danger">ID da turma nè´™o informado ou invè´°lido.</div>';
    exit;
}

try {
    // Busca os alunos da turma
    $stmt = $pdo->prepare("SELECT a.id, a.nome 
                           FROM alunos a
                           INNER JOIN alunos_turmas at ON a.id = at.aluno_id
                           WHERE at.turma_id = :turma_id
                           ORDER BY a.nome");
    $stmt->execute(['turma_id' => $turma_id]);
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($alunos)) {
        echo '<div class="text-muted">Nenhum aluno nesta turma.</div>';
        exit;
    }

    // Busca todas as turmas para o dropdown
    $turmas = $pdo->query("SELECT id, nome FROM turmas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

    echo '<table class="table table-sm align-middle">';
    echo '<thead><tr><th>Aluno</th><th>Mudar para</th></tr></thead><tbody>';
    foreach ($alunos as $a) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($a['nome']) . '</td>';
        echo '<td>';
        echo '<form method="POST" action="mudar_turma.php" class="d-flex gap-2">';
        echo '<input type="hidden" name="aluno_id" value="' . $a['id'] . '">';
        echo '<input type="hidden" name="turma_atual" value="' . $turma_id . '">';
        echo '<select name="nova_turma_id" class="form-select form-select-sm" required>';
        echo '<option value="">Selecione...</option>';
        foreach ($turmas as $t) {
            $disabled = ($t['id'] == $turma_id) ? 'disabled' : '';
            echo "<option value='{$t['id']}' $disabled>{$t['nome']}</option>";
        }
        echo '</select>';
        echo '<button type="submit" class="btn btn-sm btn-outline-primary">Mover</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Erro ao carregar alunos: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
