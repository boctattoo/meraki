<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// Busca logs de alteração de status
$sql = "
SELECT 
    l.id,
    a.nome AS aluno_nome,
    l.status_novo,
    l.data_acao,
    u.nome AS usuario_nome
FROM log_status_aluno l
JOIN alunos a ON l.aluno_id = a.id
LEFT JOIN usuarios u ON l.usuario_id = u.id
ORDER BY l.data_acao DESC
LIMIT 100
";
$logs = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Histórico de Status dos Alunos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container mt-4">
  <h2 class="mb-4">Histórico de Alteração de Status</h2>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Aluno</th>
        <th>Novo Status</th>
        <th>Data/Hora</th>
        <th>Alterado por</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($logs as $log): ?>
        <tr>
          <td><?= $log['id'] ?></td>
          <td><?= htmlspecialchars($log['aluno_nome']) ?></td>
          <td>
            <span class="badge bg-<?= $log['status_novo'] === 'Ativo' ? 'success' : ($log['status_novo'] === 'Trancado' ? 'warning' : 'danger') ?>">
              <?= $log['status_novo'] ?>
            </span>
          </td>
          <td><?= date('d/m/Y H:i', strtotime($log['data_acao'])) ?></td>
          <td><?= htmlspecialchars($log['usuario_nome'] ?? 'Sistema') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
