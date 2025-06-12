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

$filtro_dia = $_GET['dia'] ?? '';
$filtro_instrutor = $_GET['instrutor'] ?? '';
$filtro_vagas = $_GET['vagas'] ?? '';
$filtro_tipo = $_GET['tipo'] ?? '';

try {
    // Buscar turmas e dados básicos
    $sql = "
    SELECT
        t.id,
        t.nome AS turma_nome,
        i.nome AS instrutor,
        d.nome AS dia,
        t.vagas,
        tt.nome AS tipo,
        p.nome AS periodo,
        (SELECT COUNT(*) FROM alunos_turmas at WHERE at.turma_id = t.id) AS total_alunos
    FROM turmas t
    LEFT JOIN instrutores i ON t.instrutor_id = i.id
    LEFT JOIN dias_semana d ON t.dia_semana_id = d.id
    LEFT JOIN periodos p ON t.periodo_id = p.id
    LEFT JOIN tipos_turma tt ON t.tipo_id = tt.id
    GROUP BY t.id
    ";
    $todas_turmas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // Dia da semana atual
    $dias_semana = [
        'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira',
        'Quinta-feira', 'Sexta-feira', 'Sábado'
    ];
    $dia_hoje_nome = $dias_semana[date('w')];
    $data_hoje = date('Y-m-d');

    // Buscar turmas com presença registrada hoje
    $presencasHoje = $pdo->query("
        SELECT DISTINCT turma_id
        FROM presencas
        WHERE data = '$data_hoje'
    ")->fetchAll(PDO::FETCH_COLUMN);

    $turmas = [];
    $total_turmas = $total_alunos = $total_vagas = $total_disponiveis = 0;

    foreach ($todas_turmas as $t) {
        $vagas = (int) $t['vagas'];
        $alunos = (int) $t['total_alunos'];
        $disponiveis = $vagas - $alunos;
        $t['disponiveis'] = $disponiveis;

        // É o dia da semana da turma?
        $t['hoje'] = ($t['dia'] === $dia_hoje_nome);

        // Já foi registrada presença hoje?
        $t['presenca_registrada'] = in_array($t['id'], $presencasHoje);

        // Aplicar filtros visuais
        if (
            ($filtro_dia && $t['dia'] !== $filtro_dia) ||
            ($filtro_instrutor && $t['instrutor'] !== $filtro_instrutor) ||
            ($filtro_tipo && mb_strtoupper($t['tipo'], 'UTF-8') !== mb_strtoupper($filtro_tipo, 'UTF-8')) ||
            ($filtro_vagas === 'disponivel' && $disponiveis <= 0) ||
            ($filtro_vagas === 'lotada' && $disponiveis > 0)
        ) continue;

        $turmas[] = $t;
        $total_turmas++;
        $total_alunos += $alunos;
        $total_vagas += $vagas;
        $total_disponiveis += $disponiveis;
    }

} catch (PDOException $e) {
    echo '<div style="margin: 2rem; color: red; font-weight: bold;">Erro ao carregar turmas: ' . $e->getMessage() . '</div>';
    $turmas = [];
    $total_turmas = $total_alunos = $total_vagas = $total_disponiveis = 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Título da página -->
  <title>Controle de Presença | Mapa de Turmas</title>

  <!-- Favicon da aplicação -->
  <link rel="icon" href="/assets/favicon.ico" type="image/x-icon">

  <!-- CSS Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="style.css"> <!-- Remova se não estiver usando -->

  <!-- jQuery (se necessário) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>

  <!-- JS do Bootstrap com Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>

  <!-- Scripts personalizados -->
  <script src="scripts.js" defer></script> <!-- Remova se não existir -->
</head>


<body class="bg-light">
<?php include 'nav.php'; ?>

<main class="container mt-4">

  <!-- Resumo Geral das Turmas -->
  <section class="row text-center mb-4" aria-label="Resumo geral das turmas">

    <?php
    $resumos = [
      [
        'id' => 'contador-turmas',
        'titulo' => 'Total de Turmas',
        'icone' => 'bi-collection-play',
        'cor' => 'success',
        'tooltip' => 'Total de turmas ativas no sistema'
      ],
      [
        'id' => 'contador-alunos',
        'titulo' => 'Total de Alunos',
        'icone' => 'bi-person-fill',
        'cor' => 'primary',
        'tooltip' => 'Soma dos alunos matriculados em todas as turmas'
      ],
      [
        'id' => 'contador-vagas',
        'titulo' => 'Total de Vagas',
        'icone' => 'bi-grid-3x3-gap-fill',
        'cor' => 'warning',
        'tooltip' => 'Número total de vagas disponíveis e ocupadas'
      ],
      [
        'id' => 'contador-disponiveis',
        'titulo' => 'Vagas Disponíveis',
        'icone' => 'bi-patch-check-fill',
        'cor' => 'danger',
        'tooltip' => 'Soma das vagas ainda disponíveis nas turmas'
      ]
    ];

    foreach ($resumos as $r):
    ?>
      <div class="col-md-3">
        <div class="card border-<?= $r['cor'] ?>">
          <div class="card-body">
            <h6 data-bs-toggle="tooltip" title="<?= $r['tooltip'] ?>">
              <i class="bi <?= $r['icone'] ?> text-<?= $r['cor'] ?>"></i> <?= $r['titulo'] ?>
            </h6>
            <h4 class="text-<?= $r['cor'] ?> fw-bold" id="<?= $r['id'] ?>">0</h4>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

  </section>

</main>



<!-- 📅 Data Atual do Registro de Presença -->
<div class="row justify-content-center mb-4">
  <div class="col-auto">
    <div class="alert alert-info d-flex align-items-center shadow-sm" role="alert" aria-label="Data de registro de presença">
      <i class="bi bi-calendar-check me-2 fs-5"></i>
      <strong>Registrando presença para: <?= date('d/m/Y') ?></strong>
    </div>
  </div>
</div>

<!-- 🎯 Legenda Visual para Status das Turmas -->
<div class="row justify-content-center mb-4" aria-label="Legenda de status das turmas">
  <div class="col-auto">
    <span class="badge bg-success px-3 py-2 shadow-sm">
      <i class="bi bi-check-circle-fill me-1"></i> Vagas disponíveis
    </span>
  </div>
  <div class="col-auto">
    <span class="badge bg-warning text-dark px-3 py-2 shadow-sm">
      <i class="bi bi-exclamation-triangle-fill me-1"></i> Poucas vagas
    </span>
  </div>
  <div class="col-auto">
    <span class="badge bg-danger px-3 py-2 shadow-sm">
      <i class="bi bi-x-circle-fill me-1"></i> Turma lotada
    </span>
  </div>
</div>


<script>
// Função para animar contadores numéricos com suavidade
function animarContador(id, valorFinal, duracao = 1500) {
  const el = document.getElementById(id);
  if (!el || isNaN(valorFinal)) return;

  let atual = 0;
  const frames = Math.ceil(duracao / 20);
  const incremento = valorFinal / frames;

  const intervalo = setInterval(() => {
    atual += incremento;
    if (atual >= valorFinal) {
      atual = valorFinal;
      clearInterval(intervalo);
    }
    el.textContent = Math.floor(atual);
  }, 20);
}

document.addEventListener("DOMContentLoaded", () => {
  // Animação dos contadores principais
  animarContador("contador-turmas", <?= (int)$total_turmas ?>);
  animarContador("contador-alunos", <?= (int)$total_alunos ?>);
  animarContador("contador-vagas", <?= (int)$total_vagas ?>);
  animarContador("contador-disponiveis", <?= (int)$total_disponiveis ?>);

  // Inicialização dos tooltips Bootstrap 5
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el);
  });
});
</script>


<?php if (count($turmas) > 0): ?>
  <div class="mb-3 text-end small text-muted" aria-label="Total de turmas encontradas">
    <span class="badge bg-secondary px-3 py-2 shadow-sm">
      <?= count($turmas) ?> turma(s) encontrada(s) com os filtros aplicados
    </span>
  </div>
<?php else: ?>
  <div class="alert alert-warning text-center shadow-sm" role="alert" aria-live="polite">
    <i class="bi bi-info-circle me-2"></i> Nenhuma turma encontrada com os filtros aplicados.
  </div>
<?php endif; ?>


 <!-- 🎯 Filtros de busca -->
<section class="card shadow-sm mb-4" aria-label="Filtros de turmas">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-funnel-fill me-2"></i>Filtros</h5>
  </div>
  <div class="card-body">
    <form class="row row-cols-1 row-cols-md-auto g-3 align-items-end" method="GET" id="filtro-form">

      <!-- Dia da Semana -->
      <div class="col">
        <label for="filtro-dia" class="form-label">Dia da Semana</label>
        <select name="dia" id="filtro-dia" class="form-select" onchange="document.getElementById('filtro-form').submit()">
          <option value="">Todos</option>
          <?php
          $dias = $pdo->query("SELECT nome FROM dias_semana")->fetchAll(PDO::FETCH_COLUMN);
          foreach ($dias as $dia): ?>
            <option value="<?= $dia ?>" <?= $filtro_dia === $dia ? 'selected' : '' ?>><?= $dia ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Instrutor -->
      <div class="col">
        <label for="filtro-instrutor" class="form-label">Instrutor</label>
        <select name="instrutor" id="filtro-instrutor" class="form-select" onchange="document.getElementById('filtro-form').submit()">
          <option value="">Todos</option>
          <?php
          $instrutores = $pdo->query("SELECT nome FROM instrutores")->fetchAll(PDO::FETCH_COLUMN);
          foreach ($instrutores as $inst): ?>
            <option value="<?= $inst ?>" <?= $filtro_instrutor === $inst ? 'selected' : '' ?>><?= $inst ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Tipo de Turma -->
      <div class="col">
        <label for="filtro-tipo" class="form-label">Tipo</label>
        <select name="tipo" id="filtro-tipo" class="form-select" onchange="document.getElementById('filtro-form').submit()">
          <option value="">Todos</option>
          <option value="DINÂMICO" <?= $filtro_tipo === 'DINÂMICO' ? 'selected' : '' ?>>DINÂMICO</option>
          <option value="MULTIMÍDIA" <?= $filtro_tipo === 'MULTIMÍDIA' ? 'selected' : '' ?>>MULTIMÍDIA</option>
        </select>
      </div>

      <!-- Status de Vagas -->
      <div class="col">
        <label for="filtro-vagas" class="form-label">Vagas</label>
        <select name="vagas" id="filtro-vagas" class="form-select" onchange="document.getElementById('filtro-form').submit()">
          <option value="">Todas</option>
          <option value="disponivel" <?= $filtro_vagas === 'disponivel' ? 'selected' : '' ?>>Com vagas</option>
          <option value="lotada" <?= $filtro_vagas === 'lotada' ? 'selected' : '' ?>>Sem vagas</option>
        </select>
      </div>

      <!-- Botão de limpar -->
      <div class="col">
        <button type="submit" class="btn btn-outline-secondary w-100" name="limpar" value="1"
          onclick="window.location.href='<?= strtok($_SERVER['REQUEST_URI'], '?') ?>'; return false;">
          <i class="bi bi-x-circle"></i> Limpar Filtros
        </button>
      </div>

    </form>
  </div>
</section>


<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="cards-container">
  <?php foreach ($turmas as $t): ?>
    <?php
      $cardClass = 'primary';
      if ($t['disponiveis'] <= 0) $cardClass = 'danger';
      elseif ($t['disponiveis'] <= 3) $cardClass = 'warning';
      else $cardClass = 'success';

      $turma_id = urlencode($t['id']);
    ?>
    <div class="col card-entry">
      <div class="card h-100 animate-entry border-3 border-<?= $cardClass ?>">
        <div class="card-body d-flex flex-column">

          <!-- Cabeçalho com botão de expandir -->
          <h5 class="card-title fw-bold text-white px-3 py-2 rounded bg-<?= $cardClass ?> d-flex justify-content-between align-items-center">
            Turma: <?= htmlspecialchars($t['turma_nome']) ?>
            <button type="button" class="btn btn-sm btn-light toggle-presenca" aria-expanded="false" title="Expandir/Recolher presença">
              <i class="bi bi-plus-lg" aria-hidden="true"></i>
            </button>
          </h5>

          <!-- Informações da turma -->
          <p class="card-text mt-3 mb-1"><strong class="text-secondary">Instrutor:</strong> <?= $t['instrutor'] ?? '-' ?></p>
          <p class="card-text mb-1"><strong class="text-secondary">Dia:</strong> <?= $t['dia'] ?? '-' ?></p>
          <p class="card-text mb-1"><strong class="text-secondary">Período:</strong> <?= $t['periodo'] ?? '-' ?></p>
          <p class="card-text mb-1"><strong class="text-secondary">Tipo:</strong> <?= $t['tipo'] ?? '-' ?></p>
          <p class="card-text mb-2">
            <strong class="text-secondary">Vagas:</strong> <?= $t['vagas'] ?> |
            <strong class="text-secondary">Ocupadas:</strong> <?= $t['total_alunos'] ?> |
            <strong class="<?= $t['disponiveis'] <= 0 ? 'text-danger' : 'text-success' ?>">
              Disponíveis: <?= $t['disponiveis'] ?>
            </strong>
          </p>

          <!-- Área de ações da turma -->
          <div class="mt-auto">
            <div class="area-presenca collapse">
              <div class="border rounded p-3 mb-3 bg-light">
                <h6 class="mb-3 text-center"><i class="bi bi-clipboard-check me-1"></i> Controle de Presença</h6>

                <div class="presenca-container" data-turma-id="<?= $t['id'] ?>">
                  <?php if ($t['hoje']): ?>
                    <div class="text-end mb-2">
                      <button class="btn btn-sm btn-outline-success marcar-todos" type="button">
                        <i class="bi bi-check2-all me-1"></i> Marcar todos como presentes
                      </button>
                    </div>
                  <?php endif; ?>

                  <div class="d-flex justify-content-center mb-2">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                      <span class="visually-hidden">Carregando...</span>
                    </div>
                  </div>
                </div>

                <div class="text-center mt-3">
                  <button class="btn btn-primary salvar-presenca"
                          data-turma-id="<?= $t['id'] ?>"
                          <?= $t['hoje'] ? '' : 'disabled' ?>>
                    <i class="bi bi-save me-1"></i> Salvar Presença
                  </button>
                </div>
              </div>
            </div>

            <!-- Botão de cadastro -->
            <a href="precadastro.php?turma_id=<?= $turma_id ?>"
               class="btn btn-<?= $t['disponiveis'] <= 0 ? 'secondary' : 'success' ?> w-100 mt-2"
               <?= $t['disponiveis'] <= 0 ? 'disabled' : '' ?>>
              <i class="bi bi-person-plus me-1"></i> Cadastrar aluno nesta turma
            </a>
          </div>

        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>


<!-- Paginador -->
<nav aria-label="Navegação de páginas" class="mt-4">
  <ul class="pagination justify-content-center" id="paginador"></ul>
</nav>

<style>
.animate-entry {
  animation: flipIn 0.8s ease forwards;
  opacity: 0;
  transform: rotateY(90deg);
}

@keyframes flipIn {
  to {
    opacity: 1;
    transform: rotateY(0);
  }
}

.aluno-presenca {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  margin: 4px 0;
  background: white;
  border-radius: 6px;
  border: 1px solid #dee2e6;
  transition: all 0.2s ease;
}

.aluno-presenca:hover {
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.aluno-nome {
  font-weight: 500;
  color: #495057;
  flex-grow: 1;
  margin-right: 10px;
}

.presenca-controles {
  display: flex;
  gap: 8px;
}

.btn-presenca {
  padding: 4px 12px;
  font-size: 12px;
  border-radius: 20px;
  transition: all 0.2s ease;
}

.btn-presente {
  background-color: #28a745;
  border-color: #28a745;
  color: white;
}

.btn-presente:hover {
  background-color: #218838;
  border-color: #1e7e34;
}

.btn-falta {
  background-color: #dc3545;
  border-color: #dc3545;
  color: white;
}

.btn-falta:hover {
  background-color: #c82333;
  border-color: #bd2130;
}

.btn-presenca:not(.active) {
  background-color: white;
  color: #6c757d;
  border-color: #dee2e6;
}

.btn-presenca.active {
  font-weight: 600;
  transform: scale(1.05);
}

.presenca-resumo {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 12px;
  background: #f8f9fa;
  border-radius: 6px;
  margin-bottom: 15px;
  font-size: 14px;
  font-weight: 500;
}

.contador-presentes {
  color: #28a745;
}

.contador-faltas {
  color: #dc3545;
}
</style>

<script>
// Carregar alunos e presença ao inicializar a página
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.presenca-container').forEach(container => {
        const turmaId = container.dataset.turmaId;
        carregarPresenca(turmaId);
    });
});

// Carrega alunos da turma e renderiza presença
function carregarPresenca(turmaId) {
    const container = document.querySelector(`[data-turma-id="${turmaId}"]`);

    fetch(`get_presenca_turma.php?turma_id=${turmaId}&data=${new Date().toISOString().split('T')[0]}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderizarPresenca(container, data.alunos);
                habilitarBotaoSalvar(turmaId);
            } else {
                container.innerHTML = `<div class="text-danger text-center">Erro: ${data.error}</div>`;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar presença:', error);
            container.innerHTML = '<div class="text-danger text-center">Erro ao carregar dados</div>';
        });
}

// Renderiza a lista de alunos e botões de presença
function renderizarPresenca(container, alunos) {
    if (alunos.length === 0) {
        container.innerHTML = '<div class="text-muted text-center">Nenhum aluno matriculado</div>';
        return;
    }

    let html = '<div class="presenca-resumo">';
    html += '<span class="contador-presentes">Presentes: <span class="count-presentes">0</span></span>';
    html += '<span class="contador-faltas">Faltas: <span class="count-faltas">0</span></span>';
    html += '</div>';

    alunos.forEach(aluno => {
        html += `
            <div class="aluno-presenca">
                <span class="aluno-nome">${aluno.nome}</span>
                <div class="presenca-controles">
                    <button class="btn btn-presenca btn-presente ${aluno.presente ? 'active' : ''}" 
                            onclick="marcarPresenca(${aluno.id}, true, this)">
                        <i class="bi bi-check-circle"></i> Presente
                    </button>
                    <button class="btn btn-presenca btn-falta ${!aluno.presente && aluno.presente !== null ? 'active' : ''}" 
                            onclick="marcarPresenca(${aluno.id}, false, this)">
                        <i class="bi bi-x-circle"></i> Falta
                    </button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    atualizarContadores(container);
}

// Controla seleção de presença por aluno
function marcarPresenca(alunoId, presente, botao) {
    const alunoDiv = botao.closest('.aluno-presenca');
    alunoDiv.querySelectorAll('.btn-presenca').forEach(btn => btn.classList.remove('active'));
    botao.classList.add('active');

    alunoDiv.dataset.presente = presente;
    alunoDiv.dataset.alunoId = alunoId;

    const container = botao.closest('.presenca-container');
    atualizarContadores(container);
}

// Atualiza contadores de presença/falta
function atualizarContadores(container) {
    const presentes = container.querySelectorAll('.aluno-presenca[data-presente="true"]').length;
    const faltas = container.querySelectorAll('.aluno-presenca[data-presente="false"]').length;

    container.querySelector('.count-presentes').textContent = presentes;
    container.querySelector('.count-faltas').textContent = faltas;
}

// Habilita botão salvar se presença ainda não estiver registrada
function habilitarBotaoSalvar(turmaId) {
    const botao = document.querySelector(`button.salvar-presenca[data-turma-id="${turmaId}"]`);
    const badge = botao.closest('.area-presenca').querySelector('.alert-success');
    if (botao && !badge) {
        botao.disabled = false;
    }
}

// Salva a presença da turma
function salvarPresenca(turmaId, botao) {
    const container = document.querySelector(`.presenca-container[data-turma-id="${turmaId}"]`);
    const alunosPresenca = container.querySelectorAll('.aluno-presenca[data-presente]');

    const dados = [];
    alunosPresenca.forEach(div => {
        const alunoId = div.dataset.alunoId;
        const presente = div.dataset.presente === 'true';
        if (alunoId) {
            dados.push({ aluno_id: alunoId, presente });
        }
    });

    if (dados.length === 0) {
        alert('Nenhuma presença foi marcada!');
        return;
    }

    botao.disabled = true;
    const textoOriginal = botao.innerHTML;
    botao.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';

    fetch('salvar_presenca.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            turma_id: turmaId,
            data: new Date().toISOString().split('T')[0],
            presencas: dados
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            botao.innerHTML = '<i class="bi bi-check-circle"></i> Salvo!';
            botao.classList.remove('btn-primary');
            botao.classList.add('btn-success');

            setTimeout(() => {
                botao.innerHTML = textoOriginal;
                botao.classList.remove('btn-success');
                botao.classList.add('btn-primary');
                botao.disabled = true;
            }, 2000);
        } else {
            throw new Error(data.error || 'Erro desconhecido');
        }
    })
    .catch(error => {
        console.error('Erro ao salvar presença:', error);
        alert('Erro ao salvar presença: ' + error.message);
        botao.innerHTML = textoOriginal;
        botao.disabled = false;
    });
}

// Ouve cliques em "Salvar Presença"
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('salvar-presenca')) {
        const turmaId = e.target.dataset.turmaId;
        salvarPresenca(turmaId, e.target);
    }
});

// Botão "Marcar Todos como Presentes"
document.addEventListener('click', function(e) {
    if (e.target.closest('.marcar-todos')) {
        const container = e.target.closest('.presenca-container');
        container.querySelectorAll('.aluno-presenca').forEach(div => {
            const btnPresente = div.querySelector('.btn-presente');
            if (btnPresente && !btnPresente.classList.contains('active')) {
                btnPresente.click();
            }
        });
    }
});
</script>


<!-- Scripts de funcionalidades -->
<script>
// === PAGINADOR ===
document.addEventListener('DOMContentLoaded', () => {
  const cards = document.querySelectorAll('.card-entry');
  const paginador = document.getElementById('paginador');
  const porPagina = 6;
  let paginaAtual = 1;
  const totalPaginas = Math.ceil(cards.length / porPagina);

  function atualizarPaginador() {
    paginador.innerHTML = '';
    for (let i = 1; i <= totalPaginas; i++) {
      const li = document.createElement('li');
      li.className = 'page-item' + (i === paginaAtual ? ' active' : '');
      const btn = document.createElement('button');
      btn.className = 'page-link';
      btn.textContent = i;
      btn.addEventListener('click', () => {
        paginaAtual = i;
        mostrarPagina();
        atualizarPaginador();
      });
      li.appendChild(btn);
      paginador.appendChild(li);
    }
  }

  function mostrarPagina() {
    cards.forEach((card, idx) => {
      card.style.display = (idx >= (paginaAtual - 1) * porPagina && idx < paginaAtual * porPagina) ? 'block' : 'none';
    });
  }

  mostrarPagina();
  atualizarPaginador();
});
</script>

<script>
// === BOTÃO DE EXPANSÃO DAS PRESENÇAS ===
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll('.toggle-presenca').forEach(btn => {
    btn.addEventListener('click', function () {
      const card = btn.closest('.card');
      const area = card.querySelector('.area-presenca');
      const icon = btn.querySelector('i');

      area.classList.toggle('show');
      icon.classList.toggle('bi-plus-lg');
      icon.classList.toggle('bi-dash-lg');
    });
  });
});
</script>

<!-- === MASCOTE FLUTUANTE DO MERAKI === -->
<a href="https://wa.me/14999012381?text=Preciso%20de%20ajuda%20no%20Merakinho" target="_blank" class="mascote-whatsapp">
  <img src="assets/mascote_meraki.webp" alt="Mascote Meraki" />
  <div class="balao-ajuda">Precisa de ajuda?</div>
</a>

<style>
.mascote-whatsapp {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 999;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-decoration: none;
}

.mascote-whatsapp img {
  width: 70px;
  height: auto;
  transition: transform 0.2s ease-in-out;
}

.mascote-whatsapp:hover img {
  transform: scale(1.1);
}

.balao-ajuda {
  background-color: #25D366;
  color: white;
  font-weight: 500;
  padding: 8px 14px;
  border-radius: 25px;
  font-size: 14px;
  margin-top: 6px;
  opacity: 0;
  transform: translateY(10px);
  transition: opacity 0.3s ease, transform 0.3s ease;
  pointer-events: none;
  white-space: nowrap;
}

.mascote-whatsapp:hover .balao-ajuda,
.mascote-whatsapp.show-bubble .balao-ajuda {
  opacity: 1;
  transform: translateY(0);
}
</style>

<script>
// === ANIMAÇÃO DO BALÃO DO MASCOTE ===
setTimeout(() => {
  document.querySelector('.mascote-whatsapp')?.classList.add('show-bubble');
}, 5000);
</script>
</body>
</html>
