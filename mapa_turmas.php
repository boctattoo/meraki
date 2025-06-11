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

    $turmas = [];
    $total_turmas = $total_alunos = $total_vagas = $total_disponiveis = 0;

    foreach ($todas_turmas as $t) {
        $vagas = (int) $t['vagas'];
        $alunos = (int) $t['total_alunos'];
        $disponiveis = $vagas - $alunos;
        $t['disponiveis'] = $disponiveis;

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
  <title>Mapa de Turmas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<?php include 'nav.php'; ?>
<div class="container mt-4">
 <!-- Resumo com animação, ícones e tooltips -->
<div class="row text-center mb-4">
  <div class="col-md-3">
    <div class="card border-success">
      <div class="card-body">
        <h6 data-bs-toggle="tooltip" title="Total de turmas ativas no sistema">
          <i class="bi bi-collection-play text-success"></i> Total de Turmas
        </h6>
        <h4 class="text-success fw-bold" id="contador-turmas">0</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-primary">
      <div class="card-body">
        <h6 data-bs-toggle="tooltip" title="Soma dos alunos matriculados em todas as turmas">
          <i class="bi bi-person-fill text-primary"></i> Total de Alunos
        </h6>
        <h4 class="text-primary fw-bold" id="contador-alunos">0</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-warning">
      <div class="card-body">
        <h6 data-bs-toggle="tooltip" title="Número total de vagas disponíveis e ocupadas">
          <i class="bi bi-grid-3x3-gap-fill text-warning"></i> Total de Vagas
        </h6>
        <h4 class="text-warning fw-bold" id="contador-vagas">0</h4>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-danger">
      <div class="card-body">
        <h6 data-bs-toggle="tooltip" title="Soma das vagas ainda disponíveis nas turmas">
          <i class="bi bi-patch-check-fill text-danger"></i> Vagas Disponíveis
        </h6>
        <h4 class="text-danger fw-bold" id="contador-disponiveis">0</h4>
      </div>
    </div>
  </div>
</div>

<!-- Legenda visual de status -->
<div class="row justify-content-center mb-4">
  <div class="col-auto">
    <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Vagas disponíveis</span>
  </div>
  <div class="col-auto">
    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill"></i> Poucas vagas</span>
  </div>
  <div class="col-auto">
    <span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> Turma lotada</span>
  </div>
</div>

<script>
function animarContador(id, valorFinal, duracao = 1500) {
  const elemento = document.getElementById(id);
  if (!elemento) return;
  const inicio = 0;
  const incremento = valorFinal / (duracao / 20);
  let atual = inicio;

  const animacao = setInterval(() => {
    atual += incremento;
    if (atual >= valorFinal) {
      atual = valorFinal;
      clearInterval(animacao);
    }
    elemento.textContent = Math.floor(atual);
  }, 20);
}

document.addEventListener("DOMContentLoaded", function () {
  animarContador("contador-turmas", <?= $total_turmas ?>);
  animarContador("contador-alunos", <?= $total_alunos ?>);
  animarContador("contador-vagas", <?= $total_vagas ?>);
  animarContador("contador-disponiveis", <?= $total_disponiveis ?>);

  // Inicializa tooltips do Bootstrap
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>

<?php if (count($turmas) > 0): ?>
  <div class="mb-3 text-end text-muted small">
    <span class="badge bg-secondary">
      <?= count($turmas) ?> turma(s) encontrada(s) com os filtros aplicados
    </span>
  </div>
<?php else: ?>
  <div class="alert alert-warning text-center" role="alert">
    Nenhuma turma encontrada com os filtros aplicados.
  </div>
<?php endif; ?>

  <!-- Filtros -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Filtros</h5></div>
    <div class="card-body">
<?php if (count($turmas) > 0): ?>
  <div class="mb-3 text-end text-muted small">
    <span class="badge bg-secondary"><?= count($turmas) ?> turma(s) encontrada(s)</span>
  </div>
<?php else: ?>
  <div class="alert alert-warning text-center" role="alert">
    Nenhuma turma encontrada com os filtros aplicados.
  </div>
<?php endif; ?>

<form class="row row-cols-lg-auto g-3 align-items-center mb-4" method="GET" id="filtro-form">
  <div class="col-12">
    <label class="form-label">Dia da Semana</label>
    <select name="dia" class="form-select" onchange="document.getElementById('filtro-form').submit()">
      <option value="">Todos</option>
      <?php
      $dias = $pdo->query("SELECT nome FROM dias_semana")->fetchAll(PDO::FETCH_COLUMN);
      foreach ($dias as $dia): ?>
        <option value="<?= $dia ?>" <?= $filtro_dia === $dia ? 'selected' : '' ?>><?= $dia ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-12">
    <label class="form-label">Instrutor</label>
    <select name="instrutor" class="form-select" onchange="document.getElementById('filtro-form').submit()">
      <option value="">Todos</option>
      <?php
      $instrutores = $pdo->query("SELECT nome FROM instrutores")->fetchAll(PDO::FETCH_COLUMN);
      foreach ($instrutores as $inst): ?>
        <option value="<?= $inst ?>" <?= $filtro_instrutor === $inst ? 'selected' : '' ?>><?= $inst ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-12">
    <label class="form-label">Tipo</label>
    <select name="tipo" class="form-select" onchange="document.getElementById('filtro-form').submit()">
      <option value="">Todos</option>
      <option value="DINÂMICO" <?= $filtro_tipo === 'DINÂMICO' ? 'selected' : '' ?>>DINÂMICO</option>
      <option value="MULTIMÍDIA" <?= $filtro_tipo === 'MULTIMÍDIA' ? 'selected' : '' ?>>MULTIMÍDIA</option>
    </select>
  </div>
  <div class="col-12">
    <label class="form-label">Vagas</label>
    <select name="vagas" class="form-select" onchange="document.getElementById('filtro-form').submit()">
      <option value="">Todas</option>
      <option value="disponivel" <?= $filtro_vagas === 'disponivel' ? 'selected' : '' ?>>Com vagas</option>
      <option value="lotada" <?= $filtro_vagas === 'lotada' ? 'selected' : '' ?>>Sem vagas</option>
    </select>
  </div>
  <div class="col-12 align-self-end">
    <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>" class="btn btn-outline-secondary">Limpar Filtros</a>
  </div>
</form>
    </div>
  </div>

<!-- Cards -->
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
          <h5 class="card-title fw-bold text-white px-3 py-2 rounded bg-<?= $cardClass ?>">
            Turma: <?= htmlspecialchars($t['turma_nome']) ?>
          </h5>
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
          <div class="mt-auto">
            <button class="btn btn-sm btn-outline-dark expandir mb-2" data-id="<?= $t['id'] ?>">+</button>
            <div class="alunos-expandido mb-2"></div>
            <a href="precadastro.php?turma_id=<?= $turma_id ?>"
               class="btn btn-<?= $t['disponiveis'] <= 0 ? 'secondary' : 'success' ?> w-100"
               <?= $t['disponiveis'] <= 0 ? 'disabled' : '' ?>>
              Cadastrar aluno nesta turma
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
</style>

<script>
document.querySelectorAll('.expandir').forEach(btn => {
  btn.addEventListener('click', () => {
    const turmaId = btn.dataset.id;
    const container = btn.nextElementSibling;
    const isOpen = container.innerHTML.trim() !== '';

    if (isOpen) {
      container.innerHTML = '';
      btn.textContent = '+';
      return;
    }

    btn.disabled = true;
    btn.textContent = '...';

    fetch('get_alunos_turma.php?turma_id=' + turmaId)
      .then(res => res.text())
      .then(html => {
        container.innerHTML = '<div class="p-2 bg-light border rounded">' + html + '</div>';
        btn.textContent = '-';
        btn.disabled = false;
      })
      .catch(() => {
        container.innerHTML = '<div class="text-danger">Erro ao carregar alunos.</div>';
        btn.textContent = '!';
        btn.disabled = false;
      });
  });
});

// Paginador simples (exibe 6 cards por página)
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

      </table>
    </div>
  </div>
</div>
<!-- Mascote Meraki Flutuante -->
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
$(document).ready(function () {
  const tabela = $('#tabela-turmas').DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
    },
    order: [],
    columnDefs: [{ orderable: false, targets: 0 }]
  });

  $('#tabela-turmas tbody').on('click', 'button.expandir', function () {
    const tr = $(this).closest('tr');
    const row = tabela.row(tr);
    const turmaId = $(this).data('id');
    const btn = $(this);

    if (row.child.isShown()) {
      row.child.hide();
      tr.removeClass('shown');
      btn.text('+');
    } else {
      btn.prop('disabled', true).text('...');
      fetch('get_alunos_turma.php?turma_id=' + turmaId)
        .then(res => res.text())
        .then(html => {
          row.child('<div class="p-3 bg-light border rounded">' + html + '</div>').show();
          tr.addClass('shown');
          btn.text('-').prop('disabled', false);
        })
        .catch(() => {
          row.child('<div class="text-danger">Erro ao carregar alunos.</div>').show();
          tr.addClass('shown');
          btn.text('!').prop('disabled', false);
        });
    }
  });
});
</script>
<script>
setTimeout(() => {
  document.querySelector('.mascote-whatsapp')?.classList.add('show-bubble');
}, 5000);
</script>
</body>
</html>
