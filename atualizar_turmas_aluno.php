<?php
session_start();
require 'conexao.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

function buscarTurmasDoAluno($aluno_id, $pdo) {
    $stmt = $pdo->prepare("SELECT turma_id FROM alunos_turmas WHERE aluno_id = ? AND ativo = 1");
    $stmt->execute([$aluno_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Processamento do formulário de alocação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aluno_id = $_POST['aluno_id'] ?? null;
    $turmas = $_POST['turmas'] ?? [];

    if ($aluno_id && is_array($turmas)) {
        try {
            $pdo->beginTransaction();
            foreach ($turmas as $turma_id) {
                $stmt = $pdo->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, data_atribuicao, ativo) VALUES (?, ?, NOW(), 1)");
                $stmt->execute([$aluno_id, $turma_id]);
            }
            $pdo->commit();
            header('Location: ' . $_SERVER['PHP_SELF'] . '?sucesso=1');
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<div style='color: red; font-weight: bold;'>Erro ao salvar: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div style='color: red; font-weight: bold;'>Dados incompletos.</div>";
    }
}

$alunos = $pdo->query("SELECT id, nome FROM alunos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$todas_turmas = $pdo->query("SELECT id, nome FROM turmas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$alunos_filtrados = [];
foreach ($alunos as $aluno) {
    $turmas_do_aluno = buscarTurmasDoAluno($aluno['id'], $pdo);
    if (count($turmas_do_aluno) === 0) {
        $alunos_filtrados[] = $aluno;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alocar Alunos em Turmas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4">Alocar Alunos em Turmas</h2>

    <?php if (isset($_GET['sucesso'])): ?>
      <div class="alert alert-success">Turmas atualizadas com sucesso!</div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th>Aluno</th>
            <th>Selecionar Turmas</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($alunos_filtrados as $aluno): ?>
          <tr>
            <td><?= htmlspecialchars($aluno['nome']) ?></td>
            <td>
              <form action="" method="POST">
                <input type="hidden" name="aluno_id" value="<?= $aluno['id'] ?>">
                <select name="turmas[]" class="form-select" multiple size="5">
                  <?php foreach ($todas_turmas as $turma): ?>
                    <option value="<?= $turma['id'] ?>"><?= $turma['nome'] ?></option>
                  <?php endforeach; ?>
                </select>
            </td>
            <td>
                <button type="submit" class="btn btn-success mt-2">Salvar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
