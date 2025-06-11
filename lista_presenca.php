<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require 'conexao.php';

// Array com nomes dos dias e meses em PT-BR
$dias_pt = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
$meses_pt = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// Busca turmas para dropdown
$turmas = $pdo->query("SELECT t.id, t.nome, d.nome AS dia_semana FROM turmas t LEFT JOIN dias_semana d ON t.dia_semana_id = d.id ORDER BY t.nome")->fetchAll(PDO::FETCH_ASSOC);

$alunos = [];
$datas_colunas = [];
$diagnostico = [];

$mes_atual = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$ano_atual = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');
$turma_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : null;

if ($turma_id) {
    // Busca alunos da turma
    $stmt = $pdo->prepare("SELECT a.nome FROM alunos a INNER JOIN alunos_turmas at ON a.id = at.aluno_id WHERE at.turma_id = ? ORDER BY a.nome");
    $stmt->execute([$turma_id]);
    $alunos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Descobre o dia da semana da turma
    $stmt_dia = $pdo->prepare("SELECT d.nome FROM turmas t LEFT JOIN dias_semana d ON t.dia_semana_id = d.id WHERE t.id = ?");
    $stmt_dia->execute([$turma_id]);
    $dia_semana_db = $stmt_dia->fetchColumn();

    // Diagnóstico: mostrar informações relevantes se der erro
    if (!$dia_semana_db) {
        $diagnostico[] = "Atenção: Esta turma não possui 'dia_semana_id' preenchido corretamente na tabela 'turmas' OU não há registro correspondente na tabela 'dias_semana'.";
    }

    // Normaliza o nome do dia para garantir case-insensitive
    $dia_semana_db = ucfirst(mb_strtolower($dia_semana_db ?? '', 'UTF-8'));

    $index_dia = array_search($dia_semana_db, $dias_pt);
    if ($dia_semana_db && $index_dia === false) {
        $diagnostico[] = "O nome do dia da semana '{$dia_semana_db}' não foi encontrado no array padrão do sistema. Corrija para: ".implode(", ",$dias_pt);
    }

    // Gera as datas do mês selecionado no dia da semana correto
    if ($dia_semana_db && $index_dia !== false) {
        $hoje = new DateTime("$ano_atual-$mes_atual-01");
        $fim = new DateTime("$ano_atual-$mes_atual-" . $hoje->format('t'));
        while ($hoje <= $fim) {
            if ((int)$hoje->format('w') === $index_dia) {
                $datas_colunas[] = $hoje->format('d/m/Y');
            }
            $hoje->modify('+1 day');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Lista de Presença</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  @page {
    size: A4 landscape;
    margin: 1cm;
  }

  @media print {
    html, body {
      width: 297mm;
      height: 210mm;
      margin: 0 !important;
      padding: 0 !important;
      font-size: 10px;
      background: #fff !important;
    }

    nav, .no-print, .btn, .form-label, select, input, form {
      display: none !important;
    }

    .container {
      width: 100%;
      margin: 0 !important;
      padding: 0 !important;
    }

    table {
      font-size: 10px;
      width: 100%;
      table-layout: fixed;
      border-collapse: collapse;
    }

    thead th {
      font-weight: bold;
    }

    th, td {
      padding: 1px !important;
      word-wrap: break-word;
      text-align: center;
      vertical-align: middle;
      border: 1px solid #000;
    }

    .assinatura {
      height: 25px !important;
    }

    .logo-microlins {
      display: block !important;
      max-height: 45px !important;
      margin-bottom: 5px;
    }

    .linha-cabecalho {
      font-size: 12px !important;
      margin-bottom: 2px !important;
      padding-bottom: 2px !important;
      border-bottom: 2px solid #999;
    }

    .print-area {
      margin-bottom: 3px !important;
    }

    .row.mt-5 {
      margin-top: 1rem !important;
    }
  }

  table th, table td {
    text-align: center;
    vertical-align: middle;
  }

  .assinatura {
    height: 30px;
  }

  .logo-microlins {
    max-height: 70px;
    display: block;
    margin: 0 auto 10px auto;
  }

  .linha-cabecalho {
    border-bottom: 2px solid #999;
    padding-bottom: 5px;
    margin-bottom: 5px;
  }

  /* 🧠 ESSENCIAL: reduz largura do nome para liberar espaço p/ colunas */
  th:first-child,
  td:first-child {
    width: 120px !important;
  }
</style>



</head>
<body class="bg-light">
<?php include 'nav.php'; ?>
<div class="container mt-4">

  <!-- Botão imprimir -->
  <div class="mb-3 no-print text-end">
    <button class="btn btn-outline-primary" onclick="window.print()">
      🖨️ Imprimir Lista
    </button>
  </div>

  <div class="text-center mb-3 print-area">
    <!-- Logo aparece na impressão! -->
    <img src="images/logo.png" alt="Logo Microlins" class="logo-microlins mb-2"><br>
    <span class="fw-bold linha-cabecalho" style="font-size:18px;">LISTA DE PRESENÇA</span>
  </div>

  <form method="GET" class="row g-3 mb-4 align-items-end no-print">
    <div class="col-md-5">
      <label for="turma_id" class="form-label">Selecione a Turma:</label>
      <select name="turma_id" id="turma_id" class="form-select" onchange="this.form.submit()">
        <option value="">-- Escolha --</option>
        <?php foreach ($turmas as $turma): ?>
          <option value="<?= $turma['id'] ?>" <?= ($turma_id && $turma_id == $turma['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($turma['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="mes" class="form-label">Mês:</label>
      <select name="mes" id="mes" class="form-select" onchange="this.form.submit()">
        <?php for ($m=1;$m<=12;$m++): ?>
          <option value="<?= $m ?>" <?= $m == $mes_atual ? 'selected' : '' ?>>
            <?= $meses_pt[$m] ?>
          </option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label for="ano" class="form-label">Ano:</label>
      <input type="number" name="ano" id="ano" value="<?= $ano_atual ?>" class="form-control" onchange="this.form.submit()">
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary" type="submit">Exibir Lista</button>
    </div>
  </form>

  <?php
  // Mostra diagnóstico se houver
  if ($diagnostico) {
    echo '<div class="alert alert-danger"><ul style="margin-bottom:0">';
    foreach ($diagnostico as $d) echo "<li>$d</li>";
    echo '</ul></div>';
  }
  ?>

  <?php if ($alunos && $datas_colunas): ?>
  <div class="text-center mb-2">
    <div style="font-size:15px; font-weight:bold;">
      Turma: <?= htmlspecialchars($turmas[array_search($turma_id, array_column($turmas, 'id'))]['nome'] ?? '') ?>
      &nbsp;|&nbsp; Mês: <?= $meses_pt[$mes_atual] ?> / <?= $ano_atual ?>
    </div>
    <div style="font-size:14px;">
      <em>Assinatura obrigatória dos alunos em cada aula</em>
    </div>
  </div>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th rowspan="2" style="min-width:200px;">Nome do Aluno</th>
        <?php foreach ($datas_colunas as $data): ?>
          <th colspan="2">Data: <?= $data ?></th>
        <?php endforeach; ?>
      </tr>
      <tr>
        <?php foreach ($datas_colunas as $data): ?>
          <th>1ª Aula</th><th>2ª Aula</th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($alunos as $nome): ?>
        <tr>
          <td><?= htmlspecialchars($nome) ?></td>
          <?php foreach ($datas_colunas as $data): ?>
            <td class="assinatura"></td>
            <td class="assinatura"></td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="row mt-5">
    <div class="col-6 text-center">
      <p><strong>Ass. do Instrutor:</strong></p>
      <br><br>
      <div style="border-top: 1px solid #222; width: 80%; margin: 0 auto;"></div>
    </div>
    <div class="col-6 text-center">
      <p><strong>Ass. da Coordenação:</strong></p>
      <br><br>
      <div style="border-top: 1px solid #222; width: 80%; margin: 0 auto;"></div>
    </div>
  </div>
  <?php elseif ($turma_id): ?>
    <div class="alert alert-warning mt-4">Não há alunos nesta turma ou não há datas no mês selecionado.</div>
  <?php endif; ?>
</div>
</body>
</html>
