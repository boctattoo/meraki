<?php
require 'conexao.php';

echo "<h2>Diagnóstico de Ocupação das Turmas</h2>";
echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>ID</th><th>Nome da Turma</th><th>Vagas</th><th>Ocupadas</th><th>Status</th></tr>";

$turmas = $pdo->query("SELECT id, nome, vagas FROM turmas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

foreach ($turmas as $t) {
    $ocupadas = $pdo->query("SELECT COUNT(*) FROM alunos_turmas WHERE turma_id = {$t['id']} AND ativo = 1")->fetchColumn();
    $vagas = (int)$t['vagas'];
    $status = ($ocupadas >= $vagas) ? 'Sem Vagas' : 'Com Vagas';

    echo "<tr>
            <td>{$t['id']}</td>
            <td>{$t['nome']}</td>
            <td>{$vagas}</td>
            <td>{$ocupadas}</td>
            <td><strong>{$status}</strong></td>
          </tr>";
}

echo "</table>";
