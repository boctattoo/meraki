<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';
include 'nav.php';

function fetchAll($pdo, $tabela) {
    $permitidas = ['tipos_aluno', 'tipos_pgto', 'cursos', 'modalidades'];
    if (!in_array($tabela, $permitidas)) return [];
    return $pdo->query("SELECT id, nome FROM $tabela ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
}

$turmas = $pdo->query("SELECT t.id, t.nome, t.vagas, 
    (SELECT COUNT(*) FROM alunos_turmas at WHERE at.turma_id = t.id AND at.ativo = 1) as ocupadas
    FROM turmas t
    WHERE t.status = 'ativa'
    ORDER BY t.nome")->fetchAll(PDO::FETCH_ASSOC);

$tipos_aluno = fetchAll($pdo, 'tipos_aluno');
$tipos_pgto = fetchAll($pdo, 'tipos_pgto');
$cursos = fetchAll($pdo, 'cursos');
$modalidades = fetchAll($pdo, 'modalidades');

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $responsavel = trim($_POST['responsavel'] ?? '');
    $telefone_responsavel = trim($_POST['telefone_responsavel'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nascimento = $_POST['data_nascimento'] ?? null;
    $turma_id = $_POST['turma_id'] ?? null;
    $tipo_aluno_id = $_POST['tipo_aluno_id'] ?? null;
    $tipo_pgto_id = $_POST['tipo_pgto_id'] ?? null;
    $curso_id = $_POST['curso_id'] ?? null;
    $modalidade_id = $_POST['modalidade_id'] ?? null;

    if (empty($nome)) {
        $erro = 'Digite o nome do aluno.';
    } elseif (empty($turma_id) || empty($tipo_aluno_id) || empty($tipo_pgto_id) || empty($curso_id)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } else {
        $stmt = $pdo->prepare("SELECT vagas FROM turmas WHERE id = ?");
        $stmt->execute([$turma_id]);
        $vagas = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM alunos_turmas WHERE turma_id = ? AND ativo = 1");
        $stmt->execute([$turma_id]);
        $ocupadas = $stmt->fetchColumn();

        if ($vagas !== false && ($vagas - $ocupadas) <= 0) {
            $erro = 'Turma sem vaga!';
        }
    }

    if (!$erro) {
        $stmt = $pdo->prepare("INSERT INTO alunos 
            (nome, telefone, responsavel, telefone_responsavel, email, data_nascimento, turma_id, curso_id, modalidade_id, tipo_aluno_id, tipo_pgto_id, data_cadastro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $nome, $telefone, $responsavel, $telefone_responsavel, $email, $data_nascimento, $turma_id,
            $curso_id, $modalidade_id, $tipo_aluno_id, $tipo_pgto_id
        ]);
        $aluno_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, data_atribuicao, ativo) 
            VALUES (?, ?, CURDATE(), 1)");
        $stmt->execute([$aluno_id, $turma_id]);

        $mensagem = 'Aluno cadastrado com sucesso!';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Aluno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-section { margin-bottom: 2rem; }
  </style>
  <script>
    function atualizarDisponibilidade() {
        const select = document.getElementById('turma_id');
        const info = document.getElementById('disponibilidade');
        const dados = JSON.parse(select.dataset.turmas);
        const turmaSelecionada = select.value;
        const turma = dados.find(t => t.id == turmaSelecionada);

        if (turma) {
            const vagas_restantes = turma.vagas - turma.ocupadas;
            info.innerHTML = vagas_restantes > 0
                ? `<span class='text-success fw-bold'>Vagas disponíveis: ${vagas_restantes}</span>`
                : `<span class='text-danger fw-bold'>Turma sem vagas!</span>`;
        } else {
            info.innerHTML = '';
        }
    }
    window.onload = atualizarDisponibilidade;
  </script>
</head>
<body class="bg-light">
<div class="container mt-4">
  <h2 class="mb-4">📌 Cadastro de Aluno</h2>

  <?php if ($erro): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
  <?php elseif ($mensagem): ?>
    <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-4">

    <!-- DADOS PESSOAIS -->
    <div class="col-12 card card-section shadow-sm">
      <div class="card-header bg-primary text-white fw-bold">Dados Pessoais</div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome do Aluno <span class="text-danger">*</span></label>
          <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Telefone</label>
          <input type="text" name="telefone" class="form-control" placeholder="(99) 99999-9999">
        </div>
        <div class="col-md-3">
          <label class="form-label">E-mail</label>
          <input type="email" name="email" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Data de Nascimento</label>
          <input type="date" name="data_nascimento" class="form-control">
        </div>
      </div>
    </div>

    <!-- DADOS DO RESPONSÁVEL -->
    <div class="col-12 card card-section shadow-sm">
      <div class="card-header bg-secondary text-white fw-bold">Responsável</div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome do Responsável</label>
          <input type="text" name="responsavel" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Telefone do Responsável</label>
          <input type="text" name="telefone_responsavel" class="form-control">
        </div>
      </div>
    </div>

    <!-- VÍNCULOS -->
    <div class="col-12 card card-section shadow-sm">
      <div class="card-header bg-info text-white fw-bold">Vínculo com Curso e Turma</div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Curso <span class="text-danger">*</span></label>
          <select name="curso_id" class="form-select" required>
            <option value="">Selecione...</option>
            <?php foreach ($cursos as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Modalidade</label>
          <select name="modalidade_id" class="form-select">
            <option value="">Selecione...</option>
            <?php foreach ($modalidades as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tipo de Aluno <span class="text-danger">*</span></label>
          <select name="tipo_aluno_id" class="form-select" required>
            <option value="">Selecione...</option>
            <?php foreach ($tipos_aluno as $ta): ?>
              <option value="<?= $ta['id'] ?>"><?= htmlspecialchars($ta['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tipo de Pagamento <span class="text-danger">*</span></label>
          <select name="tipo_pgto_id" class="form-select" required>
            <option value="">Selecione...</option>
            <?php foreach ($tipos_pgto as $tp): ?>
              <option value="<?= $tp['id'] ?>"><?= htmlspecialchars($tp['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-12">
          <label class="form-label">Turma <span class="text-danger">*</span></label>
          <div id="disponibilidade" class="mb-1"></div>
          <select name="turma_id" id="turma_id" class="form-select" required onchange="atualizarDisponibilidade()" data-turmas='<?= json_encode($turmas) ?>'>
            <option value="">Selecione...</option>
            <?php foreach ($turmas as $t): ?>
              <option value="<?= $t['id'] ?>">
                <?= htmlspecialchars($t['nome']) ?>
                <?= ($t['vagas'] - $t['ocupadas'] <= 0) ? ' (Sem vagas)' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <div class="col-12 text-end">
      <button type="submit" class="btn btn-success btn-lg">💾 Cadastrar</button>
      <a href="index.php" class="btn btn-secondary btn-lg">Voltar</a>
    </div>
  </form>
</div>
</body>
</html>
