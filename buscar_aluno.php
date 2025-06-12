<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// Captura mensagem de sessão
$mensagem = $_SESSION['mensagem_sucesso'] ?? '';
unset($_SESSION['mensagem_sucesso']);

// Ação de trancar ou cancelar aluno
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_status'])) {
    $aluno_id = (int)$_POST['aluno_id'];
    $novo_status = $_POST['acao_status'];

    if (in_array($novo_status, ['Trancado', 'Cancelado'])) {
        $dataHoje = date('Y-m-d');

        $stmt = $pdo->prepare("UPDATE alunos SET status = ?, data_status = ? WHERE id = ?");
        $stmt->execute([$novo_status, $dataHoje, $aluno_id]);

        $stmt = $pdo->prepare("DELETE FROM alunos_turmas WHERE aluno_id = ?");
        $stmt->execute([$aluno_id]);

        $stmt = $pdo->prepare("INSERT INTO log_status_aluno (aluno_id, status_novo, usuario_id) VALUES (?, ?, ?)");
        $stmt->execute([$aluno_id, $novo_status, $_SESSION['usuario_id']]);

        $_SESSION['mensagem_sucesso'] = "Aluno marcado como $novo_status e removido das turmas com sucesso!";
        header('Location: buscar.php');
        exit;
    } else {
        $mensagem = "Status inválido.";
    }
}

// Filtros
$nome = $_GET['nome'] ?? '';
$status = $_GET['status'] ?? 'Ativo';
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $por_pagina;

$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM alunos WHERE status = :status AND nome LIKE :nome");
$stmt_total->execute([':status' => $status, ':nome' => "%$nome%"]);
$total = $stmt_total->fetchColumn();
$total_paginas = ceil($total / $por_pagina);

$stmt = $pdo->prepare("SELECT * FROM alunos WHERE status = :status AND nome LIKE :nome ORDER BY nome LIMIT $por_pagina OFFSET $offset");
$stmt->execute([':status' => $status, ':nome' => "%$nome%"]);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Buscar/Editar Alunos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container mt-4">
  <h2 class="mb-3">Lista de Alunos</h2>

  <?php if (!empty($mensagem)): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast show align-items-center text-bg-success border-0">
      <div class="d-flex">
        <div class="toast-body"><?= $mensagem ?></div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
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
      <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filtrar</button>
    </div>
  </form>

  <div class="mb-3">
    <span class="badge bg-info"><?= $total ?> aluno(s) encontrado(s)</span>
  </div>

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
            </span>
            <?php if ($aluno['status'] !== 'Ativo' && $aluno['data_status']): ?>
              <br>
              <small data-bs-toggle="tooltip" title="Data de alteração: <?= date('d/m/Y', strtotime($aluno['data_status'])) ?>">
                <?= date('d/m/Y', strtotime($aluno['data_status'])) ?>
              </small>
            <?php endif; ?>
          </td>
          <td class="d-flex gap-1">
            <a href="editar_aluno.php?id=<?= $aluno['id'] ?>" class="btn btn-sm btn-primary" title="Editar aluno">
              <i class="bi bi-pencil-square"></i>
            </a>
            <?php if ($aluno['status'] === 'Ativo'): ?>
              <form method="POST" onsubmit="return confirm('Deseja realmente alterar o status deste aluno?')">
                <input type="hidden" name="aluno_id" value="<?= $aluno['id'] ?>">
                <div class="btn-group btn-group-sm">
                  <button type="submit" name="acao_status" value="Trancado" class="btn btn-warning" title="Trancar">
                    <i class="bi bi-lock-fill"></i>
                  </button>
                  <button type="submit" name="acao_status" value="Cancelado" class="btn btn-danger" title="Cancelar">
                    <i class="bi bi-x-circle-fill"></i>
                  </button>
                </div>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <nav aria-label="Navegação de páginas">
    <ul class="pagination">
      <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
        <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
          <a class="page-link" href="?pagina=<?= $i ?>&nome=<?= urlencode($nome) ?>&status=<?= urlencode($status) ?>">
            <?= $i ?>
          </a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(t => new bootstrap.Tooltip(t));
</script>
</body>
</html>
