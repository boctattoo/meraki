<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// Mensagem de retorno
$mensagem = '';

// Ação de trancar ou cancelar aluno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_status'])) {
    $aluno_id = (int)$_POST['aluno_id'];
    $novo_status = $_POST['acao_status'];

    if (in_array($novo_status, ['Trancado', 'Cancelado'])) {
        $dataHoje = date('Y-m-d');

        // Atualiza status e data
        $stmt = $pdo->prepare("UPDATE alunos SET status = ?, data_status = ? WHERE id = ?");
        $stmt->execute([$novo_status, $dataHoje, $aluno_id]);

        // Libera as vagas
        $stmt = $pdo->prepare("DELETE FROM alunos_turmas WHERE aluno_id = ?");
        $stmt->execute([$aluno_id]);

        // Log da ação
        $stmt = $pdo->prepare("INSERT INTO log_status_aluno (aluno_id, status_novo, usuario_id) VALUES (?, ?, ?)");
        $stmt->execute([$aluno_id, $novo_status, $_SESSION['usuario_id']]);

        echo "<script>alert('Aluno marcado como $novo_status e removido das turmas com sucesso!'); window.location.href='buscar.php';</script>";
        exit;
    } else {
        $mensagem = "Status inválido. Nenhuma ação foi realizada.";
    }
}

// Filtros
$nome = $_GET['nome'] ?? '';
$status = $_GET['status'] ?? 'Ativo';

// Paginação
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $por_pagina;

// Total de registros
$sql_total = "SELECT COUNT(*) FROM alunos WHERE status = :status AND nome LIKE :nome";
$stmt_total = $pdo->prepare($sql_total);
$stmt_total->execute([':status' => $status, ':nome' => "%$nome%"]);
$total = $stmt_total->fetchColumn();
$total_paginas = ceil($total / $por_pagina);

// Busca alunos
$sql = "SELECT * FROM alunos WHERE status = :status AND nome LIKE :nome ORDER BY nome LIMIT $por_pagina OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute([':status' => $status, ':nome' => "%$nome%"]);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Buscar/Editar Alunos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container mt-4">
  <h2 class="mb-3">Lista de Alunos</h2>

  <?php if ($mensagem): ?>
    <div class="alert alert-warning"><?= $mensagem ?></div>
  <?php endif; ?>

  <form method="GET" class="row g-2 mb-4">
    <div class="col-md-4">
      <input type="text" name="nome" class="form-control" placeholder="Buscar por nome" value="<?= htmlspecialchars($nome) ?>">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="Ativo" <?= $status === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
        <option value="Trancado" <?= $status === 'Trancado' ? 'selected' : '' ?>>Trancado</option>
        <option value="Cancelado" <?= $status === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
      </select>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>
  </form>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Data de Nascimento</th>
        <th>Responsável</th>
        <th>Telefone</th>
        <th>Status</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($alunos as $aluno): ?>
        <tr>
          <td><?= $aluno['id'] ?></td>
          <td><?= htmlspecialchars($aluno['nome']) ?></td>
          <td><?= date('d/m/Y', strtotime($aluno['data_nascimento'])) ?></td>
          <td><?= htmlspecialchars($aluno['responsavel']) ?></td>
          <td><?= htmlspecialchars($aluno['telefone']) ?></td>
          <td>
            <span class="badge bg-<?= $aluno['status'] === 'Ativo' ? 'success' : ($aluno['status'] === 'Trancado' ? 'warning' : 'danger') ?>">
              <?= $aluno['status'] ?>
            </span><br>
            <?php if ($aluno['status'] !== 'Ativo' && $aluno['data_status']): ?>
              <small><?= date('d/m/Y', strtotime($aluno['data_status'])) ?></small>
            <?php endif; ?>
          </td>
          <td class="d-flex gap-1">
            <a href="editar_aluno.php?id=<?= $aluno['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
            <?php if ($aluno['status'] === 'Ativo'): ?>
              <form method="POST" onsubmit="return confirm('Deseja realmente alterar o status deste aluno?')">
                <input type="hidden" name="aluno_id" value="<?= $aluno['id'] ?>">
                <div class="btn-group btn-group-sm">
                  <button type="submit" name="acao_status" value="Trancado" class="btn btn-warning">Trancar</button>
                  <button type="submit" name="acao_status" value="Cancelado" class="btn btn-danger">Cancelar</button>
                </div>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Paginação -->
  <nav aria-label="Navegação de páginas">
    <ul class="pagination">
      <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
        <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
          <a class="page-link" href="?pagina=<?= $i ?>&nome=<?= urlencode($nome) ?>&status=<?= urlencode($status) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>
</body>
</html>
