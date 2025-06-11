<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// Buscar turmas e cursos
$cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$turmas = $pdo->query("SELECT id, nome, vagas FROM turmas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Verificar turmas sem vagas
$turmas_sem_vaga = [];
foreach ($turmas as $t) {
    $ocupadas = $pdo->query("SELECT COUNT(*) FROM alunos_turmas WHERE turma_id = {$t['id']} AND ativo = 1")->fetchColumn();
    if ($ocupadas >= $t['vagas']) {
        $turmas_sem_vaga[] = $t['id'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pré-Cadastro - Microlins Bauru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f4f4f4; min-height: 100vh; }
    .main-form { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.06); }
    h2, h3 { text-align: center; color: #1a237e; }
    @media (max-width: 600px) {
      .main-form { padding: 16px; }
      h2, h3 { font-size: 20px; }
    }
    label { font-weight: 500; color: #333; }
    .section { margin-bottom: 32px; }
    button[type="submit"] { font-size: 18px; padding: 14px 0; }
    .form-section-title { font-size: 1.08em; margin-bottom: 10px; color: #495057;}
    .form-control, select.form-select { margin-bottom: 12px; }
    .float-btn { position: fixed; right: 20px; bottom: 20px; z-index: 1000; }
  </style>
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container">
  <form class="main-form shadow" action="salvar_contrato.php" method="POST" autocomplete="off">
    <h2 class="mb-3">Pré-Cadastro / Contrato de Prestação de Serviços</h2>

    <!-- 1. Identificação do Aluno/Empresa -->
    <div class="section">
      <h5 class="form-section-title">1. Identificação do Aluno/Empresa</h5>
      <div class="row">
        <div class="col-md-8 mb-3">
          <label>Nome do Aluno/Empresa:</label>
          <input type="text" name="nome_aluno" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
          <label>Data de Nascimento:</label>
          <input type="text" name="data_nascimento_aluno" class="form-control" placeholder="dd/mm/aaaa" required>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3">
          <label>Estado Civil:</label>
          <input type="text" name="estado_civil" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
          <label>Profissão:</label>
          <input type="text" name="profissao_aluno" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
          <label>Sexo:</label>
          <select name="sexo" class="form-select">
            <option value="">Selecione</option>
            <option>Masculino</option>
            <option>Feminino</option>
          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7 mb-3">
          <label>Endereço:</label>
          <input type="text" name="endereco_aluno" class="form-control">
        </div>
        <div class="col-md-2 mb-3">
          <label>CEP:</label>
          <input type="text" name="cep_aluno" class="form-control">
        </div>
        <div class="col-md-3 mb-3">
          <label>Cidade:</label>
          <input type="text" name="cidade_aluno" class="form-control">
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3">
          <label>Telefone:</label>
          <input type="text" name="telefone_aluno" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
          <label>CPF/CNPJ:</label>
          <input type="text" name="cpf_cnpj_aluno" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
          <label>Responsável:</label>
          <input type="text" name="nome_responsavel" class="form-control">
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label>Telefone Responsável:</label>
          <input type="text" name="telefone_responsavel" class="form-control">
        </div>
      </div>
    </div>

    <!-- 2. Identificação do Pagador -->
    <div class="section">
      <h5 class="form-section-title">2. Identificação do Pagador</h5>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label>Nome do Pagador:</label>
          <input type="text" name="nome_pagador" class="form-control">
        </div>
        <div class="col-md-3 mb-3">
          <label>Data de Nascimento:</label>
          <input type="text" name="data_nascimento_pagador" class="form-control" placeholder="dd/mm/aaaa">
        </div>
        <div class="col-md-3 mb-3">
          <label>Profissão:</label>
          <input type="text" name="profissao_pagador" class="form-control">
        </div>
      </div>
      <div class="row">
        <div class="col-md-5 mb-3">
          <label>Endereço:</label>
          <input type="text" name="endereco_pagador" class="form-control">
        </div>
        <div class="col-md-3 mb-3">
          <label>Bairro:</label>
          <input type="text" name="bairro_pagador" class="form-control">
        </div>
        <div class="col-md-2 mb-3">
          <label>Cidade:</label>
          <input type="text" name="cidade_pagador" class="form-control">
        </div>
        <div class="col-md-2 mb-3">
          <label>CPF/CNPJ:</label>
          <input type="text" name="cpf_cnpj_pagador" class="form-control">
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3">
          <label>Telefone:</label>
          <input type="text" name="telefone_pagador" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
          <label>Celular:</label>
          <input type="text" name="celular_pagador" class="form-control">
        </div>
      </div>
    </div>
    
   <!-- 3. Curso, Prazo e Duração -->
<div class="section">
  <h5 class="form-section-title">3. Curso, Prazo e Duração</h5>
  <div id="cursos-container">
    <div class="curso-item row g-2 align-items-end mb-2">
      <div class="col-md-8">
        <label>Nome do Curso:</label>
        <select name="cursos[]" class="form-select curso-select" required>
          <option value="">Selecione...</option>
          <?php foreach ($cursos as $c): ?>
            <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <button type="button" class="btn btn-success add-curso w-100">+ Adicionar Curso</button>
      </div>
    </div>
  </div>

  <!-- Exibe aviso se turma sem vaga foi enviada -->
  <?php if (isset($_GET['erro_turma']) && $_GET['erro_turma'] === '1'): ?>
    <div class="alert alert-danger mt-2">A turma selecionada está sem vagas disponíveis. Por favor, escolha outra turma.</div>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-3">
      <label>Turma:</label>
      <select name="turma" class="form-select" id="turma-select" required>
        <option value="">Selecione...</option>
       <?php foreach ($turmas as $t): ?>
  <?php
    $ocupadas = $pdo->query("SELECT COUNT(*) FROM alunos_turmas WHERE turma_id = {$t['id']} AND ativo = 1")->fetchColumn();
    $vagas = (int) $t['vagas']; // Use a coluna correta
    $sem_vagas = $ocupadas >= $vagas;
    $disabled = $sem_vagas ? 'disabled' : '';
    $nome_exibido = htmlspecialchars($t['nome']) . ($sem_vagas ? ' (Sem vagas)' : " ({$ocupadas}/{$vagas})");
  ?>
  <option value="<?= $t['id'] ?>" <?= $disabled ?>><?= $nome_exibido ?></option>
<?php endforeach; ?>

      </select>
    </div>
    <div class="col-md-3">
      <label>Duração do Curso:</label>
      <input type="text" name="duracao" class="form-control" id="duracao" readonly>
    </div>
    <div class="col-md-2">
      <label>Carga Horária:</label>
      <input type="text" name="carga_horaria" class="form-control" id="carga-horaria" readonly>
    </div>
    <div class="col-md-4">
      <label>Início das Aulas:</label>
      <input type="text" name="inicio_aulas" class="form-control" id="inicio-aulas" readonly>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <label>Término das Aulas:</label>
      <input type="text" name="termino_aulas" class="form-control" id="termino-aulas" readonly>
    </div>
    <div class="col-md-4">
      <label>Dias da Semana:</label>
      <input type="text" name="dias_semana" class="form-control" id="dias-semana" readonly>
    </div>
    <div class="col-md-4">
      <label>Horário Escolhido:</label>
      <input type="text" name="horario" class="form-control" id="horario" readonly>
    </div>
  </div>
</div>


    <!-- 4. Valor e Condições de Pagamento -->
<div class="section">
  <h5 class="form-section-title">4. Valor e Condições de Pagamento</h5>
  <div class="row">
    <div class="col-md-4 mb-3">
      <label>Entrada (R$):</label>
      <input type="text" name="entrada" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
      <label>Valor da Parcela Integral (R$):</label>
      <input type="text" name="parcela_integral" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
      <label>Desconto de Pontualidade (R$):</label>
      <input type="text" name="desconto_pontualidade" class="form-control">
    </div>
  </div>
  <div class="row">
    <div class="col-md-4 mb-3">
      <label>Parcela com Desconto até Vencimento (R$):</label>
      <input type="text" name="parcela_com_desconto" class="form-control" readonly>
    </div>
    <div class="col-md-4 mb-3">
      <label>Parcela do Material (R$):</label>
      <input type="text" name="parcela_material" class="form-control">
    </div>
    <div class="col-md-2 mb-3">
      <label>Quantidade de Meses:</label>
      <input type="number" name="qtd_meses" class="form-control" id="qtd-meses" readonly>
    </div>
    <div class="col-md-2 mb-3">
      <label>Data de Vencimento:</label>
      <input type="text" name="data_vencimento" class="form-control" value="10" readonly>
    </div>
  </div>
</div>




  

    <button type="submit" class="btn btn-primary w-100">Salvar Contrato</button>
    <div class="mt-3 text-center">
      <a href="visualizar_contratos.php" class="btn btn-outline-secondary btn-sm">Ver Contratos Registrados</a>
    </div>
  </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.querySelector('form');
      const turmaSelect = document.querySelector('#turma');
      const turmas = JSON.parse(document.querySelector('#turma').dataset.turmas);

      form.addEventListener('submit', function (e) {
        const turmaId = turmaSelect.value;
        const turma = turmas.find(t => t.id == turmaId);
        if (turma && turma.ocupadas >= turma.vagas) {
          e.preventDefault();
          alert("A turma selecionada está sem vagas disponíveis.");
        }
      });
    });


document.addEventListener('DOMContentLoaded', function () {
  const cursoContainer = document.getElementById('cursos-container');
  const turmaSelect = document.getElementById('turma-select');
  const inputInicio = document.getElementById('inicio-aulas');
  const inputTermino = document.getElementById('termino-aulas');
  const inputCarga = document.getElementById('carga-horaria');
  const inputDiasSemana = document.getElementById('dias-semana');
  const inputHorario = document.getElementById('horario');
  const inputDuracao = document.getElementById('duracao');

  cursoContainer.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-curso')) {
      const clone = cursoContainer.querySelector('.curso-item').cloneNode(true);
      clone.querySelector('.curso-select').value = '';
      clone.querySelector('.add-curso')?.remove();
      cursoContainer.appendChild(clone);
    }
  });

  turmaSelect.addEventListener('change', atualizarInformacoes);
  cursoContainer.addEventListener('change', function (e) {
    if (e.target.classList.contains('curso-select')) {
      atualizarInformacoes();
    }
  });

  function atualizarInformacoes() {
    const cursoSelects = document.querySelectorAll('.curso-select');
    const ids = Array.from(cursoSelects).map(sel => sel.value).filter(Boolean);
    const idTurma = turmaSelect.value;
    if (!ids.length || !idTurma) return;

    fetch('get_turma.php?id=' + idTurma)
      .then(resp => resp.json())
      .then(dataTurma => {
        inputDiasSemana.value = dataTurma.dia_semana;
        inputHorario.value = dataTurma.periodo;
        const dataInicio = proximaDataSemana(dataTurma.dia_semana);
        inputInicio.value = dataInicio;

        Promise.all(ids.map(id => fetch('get_carga_total.php?id=' + id).then(r => r.json())))
          .then(resultados => {
            const totalHoras = resultados.reduce((acc, cur) => acc + parseInt(cur.carga_horaria), 0);
            const acrescimo = ids.length;
            const duracaoMeses = Math.ceil(totalHoras / 8) + acrescimo;
            inputCarga.value = totalHoras;
            inputDuracao.value = duracaoMeses + ' meses';
            const dataFinal = somaMeses(dataInicio, duracaoMeses);
            inputTermino.value = dataFinal;
          });
      });
  }

  function proximaDataSemana(diaSemana) {
    const diasMap = { 'domingo':0, 'segunda':1, 'terça':2, 'terca':2, 'quarta':3, 'quinta':4, 'sexta':5, 'sábado':6, 'sabado':6 };
    const hoje = new Date();
    const hojeNum = hoje.getDay();
    const alvoNum = diasMap[diaSemana.toLowerCase()];
    let diff = alvoNum - hojeNum;
    if (diff < 0) diff += 7;
    const proxima = new Date(hoje);
    proxima.setDate(hoje.getDate() + diff);
    return proxima.toLocaleDateString('pt-BR');
  }

  function somaMeses(dataStr, meses) {
    let [dia, mes, ano] = dataStr.split('/').map(x => parseInt(x));
    let data = new Date(ano, mes-1, dia);
    data.setMonth(data.getMonth() + meses);
    return data.toLocaleDateString('pt-BR');
  }
});

  document.addEventListener('DOMContentLoaded', function () {
    const parcelaIntegral = document.querySelector('[name="parcela_integral"]');
    const descontoPontualidade = document.querySelector('[name="desconto_pontualidade"]');
    const parcelaComDesconto = document.querySelector('[name="parcela_com_desconto"]');

    function calcularDesconto() {
      const valorParcela = parseFloat((parcelaIntegral?.value || '0').replace(',', '.'));
      const valorDesconto = parseFloat((descontoPontualidade?.value || '0').replace(',', '.'));
      const valorFinal = Math.max(valorParcela - valorDesconto, 0);
      if (parcelaComDesconto) parcelaComDesconto.value = valorFinal.toFixed(2);
    }

    parcelaIntegral?.addEventListener('input', calcularDesconto);
    descontoPontualidade?.addEventListener('input', calcularDesconto);

    const duracao = document.getElementById('duracao');
    const qtdMeses = document.getElementById('qtd-meses');

    function atualizarQtdMeses() {
      const texto = duracao?.value || duracao?.innerText || '';
      const match = texto.match(/\d+/);
      if (qtdMeses) qtdMeses.value = match ? parseInt(match[0]) : '';
    }

    if (duracao && qtdMeses) {
      const observer = new MutationObserver(atualizarQtdMeses);
      observer.observe(duracao, { childList: true, characterData: true, subtree: true });
      duracao.addEventListener('input', atualizarQtdMeses);
      atualizarQtdMeses(); // inicial
    }
  });

</script>
</body>
</html>
