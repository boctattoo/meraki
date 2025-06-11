<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';
include 'nav.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID de aluno não informado.");

function inputVal($array, $key) {
    return isset($array[$key]) ? htmlspecialchars($array[$key]) : '';
}

$stmt = $pdo->prepare("SELECT * FROM alunos WHERE id = ?");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$aluno) die("Aluno não encontrado.");

$stmt = $pdo->prepare("SELECT turma_id FROM alunos_turmas WHERE aluno_id = ?");
$stmt->execute([$id]);
$turmas_aluno = $stmt->fetchAll(PDO::FETCH_COLUMN);

$turmas = $pdo->query("SELECT id, nome FROM turmas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$modalidades = $pdo->query("SELECT id, nome FROM modalidades ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$tipos_aluno = $pdo->query("SELECT id, nome FROM tipos_aluno ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$tipos_pgto = $pdo->query("SELECT id, nome FROM tipos_pgto ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$erro = '';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_status = $_POST['status'] ?? 'Ativo';
    $data_status = ($novo_status !== $aluno['status']) ? date('Y-m-d') : $aluno['data_status'];

    $stmt = $pdo->prepare("UPDATE alunos SET nome=?, telefone=?, responsavel=?, telefone_responsavel=?, data_nascimento=?, email=?, curso_id=?, modalidade_id=?, tipo_aluno_id=?, tipo_pgto_id=?, status=?, data_status=? WHERE id=?");
    $ok = $stmt->execute([
        $_POST['nome'],
        $_POST['telefone'],
        $_POST['responsavel'],
        $_POST['telefone_responsavel'],
        $_POST['data_nascimento'] ?: null,
        $_POST['email'],
        $_POST['curso_id'],
        $_POST['modalidade_id'],
        $_POST['tipo_aluno_id'],
        $_POST['tipo_pgto_id'],
        $novo_status,
        $data_status,
        $id
    ]);

    if ($ok) {
        $pdo->prepare("DELETE FROM alunos_turmas WHERE aluno_id=?")->execute([$id]);
        if ($novo_status === 'Ativo') {
            foreach ($_POST['turmas_id'] ?? [] as $tid) {
                $pdo->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, ativo) VALUES (?, ?, 1)")->execute([$id, $tid]);
            }
        }
        $mensagem = "Dados atualizados com sucesso!";
        $stmt = $pdo->prepare("SELECT * FROM alunos WHERE id = ?");
        $stmt->execute([$id]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $pdo->prepare("SELECT turma_id FROM alunos_turmas WHERE aluno_id = ?");
        $stmt->execute([$id]);
        $turmas_aluno = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $erro = "Erro ao salvar.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Aluno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-section { margin-bottom: 2rem; }
  </style>
</head>
<body class="bg-light">
<div class="container mt-4">
  <h2 class="mb-4">✏️ Editar Aluno</h2>

  <?php if ($mensagem): ?><div class="alert alert-success"><?= $mensagem ?></div><?php endif; ?>
  <?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>

  <form method="POST" class="row g-4">
    <!-- DADOS PESSOAIS -->
    <div class="col-12 card card-section shadow-sm">
      <div class="card-header bg-primary text-white fw-bold">Dados Pessoais</div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome</label>
          <input type="text" name="nome" class="form-control" value="<?= inputVal($aluno, 'nome') ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Data de Nascimento</label>
          <input type="date" name="data_nascimento" class="form-control" value="<?= inputVal($aluno, 'data_nascimento') ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">Telefone</label>
          <input type="text" name="telefone" class="form-control" value="<?= inputVal($aluno, 'telefone') ?>" autocomplete="off">
        </div>
        <div class="col-md-6">
          <label class="form-label">E-mail</label>
          <input type="email" name="email" class="form-control" value="<?= inputVal($aluno, 'email') ?>" autocomplete="off">
        </div>
      </div>
    </div>

    <!-- DADOS DO RESPONSÁVEL -->
    <div class="col-12 card card-section shadow-sm">
      <div class="card-header bg-secondary text-white fw-bold">Responsável</div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome do Responsável</label>
          <input type="text" name="responsavel" class="form-control" value="<?= inputVal($aluno, 'responsavel') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Telefone do Responsável</label>
          <input type="text" name="telefone_responsavel" class="form-control" value="<?= inputVal($aluno, 'telefone_responsavel') ?>">
        </div>
      </div>
    </div>

    <!-- VÍNCULOS ACADÊMICOS -->
    <div class="col-12 card card-section shadow-sm">
      <div class="card-header bg-info text-white fw-bold">Vínculo Acadêmico</div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Curso</label>
          <select name="curso_id" class="form-select" required>
            <option value="">Selecione...</option>
            <?php foreach ($cursos as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $aluno['curso_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['nome'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Modalidade</label>
          <select name="modalidade_id" class="form-select">
            <option value="">Selecione...</option>
            <?php foreach ($modalidades as $m): ?>
              <option value="<?= $m['id'] ?>" <?= $aluno['modalidade_id'] == $m['id'] ? 'selected' : '' ?>><?= $m['nome'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tipo de Aluno</label>
          <select name="tipo_aluno_id" class="form-select">
            <option value="">Selecione...</option>
            <?php foreach ($tipos_aluno as $ta): ?>
              <option value="<?= $ta['id'] ?>" <?= $aluno['tipo_aluno_id'] == $ta['id'] ? 'selected' : '' ?>><?= $ta['nome'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tipo de Pagamento</label>
          <select name="tipo_pgto_id" class="form-select">
            <option value="">Selecione...</option>
            <?php foreach ($tipos_pgto as $tp): ?>
              <option value="<?= $tp['id'] ?>" <?= $aluno['tipo_pgto_id'] == $tp['id'] ? 'selected' : '' ?>><?= $tp['nome'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-12">
          <label class="form-label">Status</label><br>
          <?php foreach (["Ativo", "Trancado", "Cancelado"] as $status): ?>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status" id="status_<?= $status ?>" value="<?= $status ?>" <?= $aluno['status'] === $status ? 'checked' : '' ?>>
              <label class="form-check-label" for="status_<?= $status ?>"><?= $status ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- TURMAS -->
    <div class="col-12 card card-section shadow-sm">
      <div class="card-header bg-dark text-white fw-bold">Turmas</div>
      <div class="card-body">
        <label class="form-label">Selecione as turmas:</label>
        <select name="turmas_id[]" class="form-select" multiple>
          <?php foreach ($turmas as $t): ?>
            <option value="<?= $t['id'] ?>" <?= in_array($t['id'], $turmas_aluno) ? 'selected' : '' ?>><?= $t['nome'] ?></option>
          <?php endforeach; ?>
        </select>
        <small class="text-muted">Segure Ctrl ou Shift para selecionar mais de uma.</small>
      </div>
    </div>

    <!-- BOTÃO -->
    <div class="col-12 text-end">
      <button type="submit" class="btn btn-success btn-lg">📎 Salvar Alterações</button>
    </div>
  </form>
</div>
</body>
</html>
