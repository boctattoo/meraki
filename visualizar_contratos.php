<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// Filtros
$nome = $_GET['nome'] ?? '';
$curso = $_GET['curso'] ?? '';
$data_inicio = $_GET['inicio'] ?? '';
$data_fim = $_GET['fim'] ?? '';
$erro_consolida = '';
$ok_consolida = '';

// CONSOLIDAÇÃO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consolidar_id'])) {
    $contrato_id = (int)$_POST['consolidar_id'];
    $stmt = $pdo->prepare("SELECT * FROM contratos WHERE id = ?");
    $stmt->execute([$contrato_id]);
    $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($contrato && empty($contrato['consolidado'])) {
        // Cria o aluno com status Ativo
        $stmt = $pdo->prepare("INSERT INTO alunos (nome, data_nascimento, responsavel, telefone, email, data_cadastro, status)
            VALUES (?, ?, ?, ?, ?, NOW(), 'Ativo')");
        $data_nascimento = $contrato['data_nascimento_aluno'] ?? null;
        $stmt->execute([
            $contrato['nome_aluno'],
            $data_nascimento,
            $contrato['nome_responsavel'],
            $contrato['telefone_aluno'],
            null
        ]);
        $aluno_id = $pdo->lastInsertId();

        // Vincula turma
        $turma_id = $contrato['turma_id'] ?? null;
        if ($turma_id) {
            $stmt = $pdo->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, ativo) VALUES (?, ?, 1)");
            $stmt->execute([$aluno_id, $turma_id]);
        }

        // Marca contrato como consolidado
        $stmt = $pdo->prepare("UPDATE contratos SET consolidado = 1, aluno_id = ? WHERE id = ?");
        $stmt->execute([$aluno_id, $contrato_id]);
        $ok_consolida = "Contrato #$contrato_id consolidado com sucesso!";
    } else {
        $erro_consolida = "Erro ao consolidar: contrato já consolidado ou não existe.";
    }
}

$sql = "SELECT contratos.*, GROUP_CONCAT(cursos.nome SEPARATOR ', ') AS cursos FROM contratos
LEFT JOIN contratos_cursos ON contratos.id = contratos_cursos.contrato_id
LEFT JOIN cursos ON contratos_cursos.curso_id = cursos.id
WHERE contratos.consolidado = 0";
$params = [];
if ($nome) {
    $sql .= " AND contratos.nome_aluno LIKE :nome";
    $params[':nome'] = "%$nome%";
}
if ($curso) {
    $sql .= " AND cursos.nome LIKE :curso";
    $params[':curso'] = "%$curso%";
}
if ($data_inicio && $data_fim) {
    $sql .= " AND DATE(contratos.criado_em) BETWEEN :inicio AND :fim";
    $params[':inicio'] = date('Y-m-d', strtotime(str_replace('/', '-', $data_inicio)));
    $params[':fim'] = date('Y-m-d', strtotime(str_replace('/', '-', $data_fim)));
}
$sql .= " GROUP BY contratos.id ORDER BY contratos.criado_em DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Contratos Registrados</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f1f1f1; }
    .card { background: white; border-radius: 10px; padding: 15px; margin-bottom: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.07);}
    .detalhes { display: none; margin-top: 10px; }
    .campo strong { color: #444; }
    .copiar-btn {
      display: inline-block; margin-left: 8px; background: #eee; border: 1px solid #bbb;
      padding: 2px 7px; border-radius: 4px; font-size: 12px; cursor: pointer;
    }
    .copiar-btn:hover { background: #dcdcdc; }
    .btn-consolidar {
      background: #28a745; color: #fff; padding: 7px 16px; border: none; border-radius: 5px;
      margin-top: 10px; cursor: pointer; font-weight: bold;
    }
    .btn-consolidar:hover { background: #208c3c; }
    .msg-ok { background: #d4edda; color: #155724; padding: 10px 15px; border-radius: 6px; margin-bottom: 12px;}
    .msg-erro { background: #f8d7da; color: #721c24; padding: 10px 15px; border-radius: 6px; margin-bottom: 12px;}
    @media (max-width: 768px) {
      .form-filtros input { width: 100%; margin-bottom: 8px;}
    }
  </style>
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container">
  <h2 class="mb-3">Contratos Registrados</h2>
  <?php if ($ok_consolida): ?><div class="msg-ok"><?= $ok_consolida ?></div><?php endif; ?>
  <?php if ($erro_consolida): ?><div class="msg-erro"><?= $erro_consolida ?></div><?php endif; ?>

  <form method="GET" class="form-filtros mb-3 bg-white p-3 rounded shadow-sm">
    <div class="row g-2">
      <div class="col-md-3">
        <input type="text" name="nome" class="form-control" placeholder="Nome do Aluno" value="<?= htmlspecialchars($nome) ?>">
      </div>
      <div class="col-md-3">
        <input type="text" name="curso" class="form-control" placeholder="Curso" value="<?= htmlspecialchars($curso) ?>">
      </div>
      <div class="col-md-3">
        <input type="text" name="inicio" class="form-control" placeholder="Data Início (dd/mm/aaaa)" value="<?= htmlspecialchars($data_inicio) ?>">
      </div>
      <div class="col-md-3">
        <input type="text" name="fim" class="form-control" placeholder="Data Fim (dd/mm/aaaa)" value="<?= htmlspecialchars($data_fim) ?>">
      </div>
      <div class="col-md-12">
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </div>
    </div>
  </form>

  <?php foreach ($contratos as $contrato): ?>
    <div class="card" onclick="toggleDetalhes(this)">
      <div><strong>#<?= $contrato['id'] ?> - <?= $contrato['nome_aluno'] ?> | <?= $contrato['cursos'] ?></strong></div>
      <small><?= date('d/m/Y H:i', strtotime($contrato['criado_em'])) ?></small>
      <div class="detalhes">
        <?php foreach ($contrato as $campo => $valor): ?>
          <?php if (in_array($campo, ['id','criado_em','consolidado','aluno_id'])) continue; ?>
          <div class="campo">
            <strong><?= ucfirst(str_replace('_', ' ', $campo)) ?>:</strong>
            <span><?= nl2br(htmlspecialchars($valor)) ?></span>
            <div class="copiar-btn" onclick="copiarTexto(event, '<?= addslashes($valor) ?>')">Copiar</div>
          </div>
        <?php endforeach; ?>
        <form method="POST" style="margin-top: 12px;">
          <input type="hidden" name="consolidar_id" value="<?= $contrato['id'] ?>">
          <button type="submit" class="btn-consolidar" onclick="return confirm('Consolidar este contrato?');">Consolidar</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (count($contratos) === 0): ?>
    <p>Nenhum contrato encontrado.</p>
  <?php endif; ?>
</div>

<script>
  function toggleDetalhes(card) {
    const detalhes = card.querySelector('.detalhes');
    detalhes.style.display = (detalhes.style.display === 'block') ? 'none' : 'block';
  }
  function copiarTexto(event, texto) {
    event.stopPropagation();
    navigator.clipboard.writeText(texto).then(() => {
      const btn = event.target;
      btn.innerText = 'Copiado!';
      setTimeout(() => btn.innerText = 'Copiar', 1200);
    });
  }
  document.querySelectorAll('input[name="inicio"], input[name="fim"]').forEach(input => {
    input.addEventListener('input', () => {
      input.value = input.value
        .replace(/\D/g, '')
        .replace(/(\d{2})(\d)/, '$1/$2')
        .replace(/(\d{2})\/(\d{2})(\d)/, '$1/$2/$3')
        .substring(0, 10);
    });
  });
</script>
</body>
</html>
