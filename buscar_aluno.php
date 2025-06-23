<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// --- Funções de Segurança ---
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// --- Lógica de Ações (Trancar/Cancelar) ---
$mensagem = $_SESSION['mensagem'] ?? '';
$tipo_mensagem = $_SESSION['tipo_mensagem'] ?? 'success';
unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_status'])) {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['mensagem'] = 'Erro de segurança. Ação não permitida.';
        $_SESSION['tipo_mensagem'] = 'danger';
    } else {
        $aluno_id = filter_input(INPUT_POST, 'aluno_id', FILTER_VALIDATE_INT);
        $novo_status = in_array($_POST['acao_status'], ['Trancado', 'Cancelado']) ? $_POST['acao_status'] : null;

        if ($aluno_id && $novo_status) {
            $pdo->beginTransaction();
            try {
                $dataHoje = date('Y-m-d');
                $stmt = $pdo->prepare("UPDATE alunos SET status = ?, data_status = ? WHERE id = ?");
                $stmt->execute([$novo_status, $dataHoje, $aluno_id]);
                $stmt = $pdo->prepare("UPDATE alunos_turmas SET ativo = 0 WHERE aluno_id = ?");
                $stmt->execute([$aluno_id]);
                $stmt = $pdo->prepare("INSERT INTO log_status_aluno (aluno_id, status_novo, usuario_id) VALUES (?, ?, ?)");
                $stmt->execute([$aluno_id, $novo_status, $_SESSION['usuario_id']]);
                
                $pdo->commit();
                $_SESSION['mensagem'] = "Aluno marcado como '$novo_status' com sucesso!";
                $_SESSION['tipo_mensagem'] = 'success';
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['mensagem'] = "Erro ao alterar o status do aluno.";
                $_SESSION['tipo_mensagem'] = 'danger';
            }
        }
    }
    header('Location: buscar_aluno.php?' . http_build_query($_GET));
    exit;
}

// --- Lógica de Busca e Paginação ---
$csrf_token = generateCSRFToken();
$nome = filter_input(INPUT_GET, 'nome', FILTER_SANITIZE_STRING) ?? '';
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING) ?? 'Ativo';
$por_pagina = 9;
$pagina_atual = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?? 1;
$offset = ($pagina_atual - 1) * $por_pagina;

$params = [':status' => $status, ':nome' => "%$nome%"];
$where_clause = "WHERE a.status = :status AND a.nome LIKE :nome";

$stmt_total = $pdo->prepare("SELECT COUNT(a.id) FROM alunos a $where_clause");
$stmt_total->execute($params);
$total_alunos = $stmt_total->fetchColumn();
$total_paginas = ceil($total_alunos / $por_pagina);

// ATUALIZADO: Incluído foto_perfil na query
$query_alunos = "
    SELECT a.id, a.nome, a.data_nascimento, a.responsavel, a.telefone, a.status, a.foto_perfil, 
           GROUP_CONCAT(t.nome SEPARATOR ', ') as turmas
    FROM alunos a
    LEFT JOIN alunos_turmas at ON a.id = at.aluno_id AND at.ativo = 1
    LEFT JOIN turmas t ON at.turma_id = t.id
    $where_clause
    GROUP BY a.id
    ORDER BY a.nome
    LIMIT :limit OFFSET :offset
";
$stmt_alunos = $pdo->prepare($query_alunos);
$stmt_alunos->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
$stmt_alunos->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt_alunos->bindValue(':status', $status);
$stmt_alunos->bindValue(':nome', "%$nome%");
$stmt_alunos->execute();
$alunos = $stmt_alunos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Buscar Alunos | Sistema Meraki</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .student-card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: none;
            border-radius: 0.75rem;
        }
        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .student-avatar {
            width: 60px;
            height: 60px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4"><i class="fas fa-search me-2"></i>Buscar Alunos</h2>
        <a href="cadastro_aluno.php" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Adicionar Aluno</a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5"><input type="text" name="nome" class="form-control" placeholder="Buscar por nome..." value="<?php echo htmlspecialchars($nome); ?>"></div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="Ativo" <?php echo $status === 'Ativo' ? 'selected' : '' ?>>Ativos</option>
                        <option value="Trancado" <?php echo $status === 'Trancado' ? 'selected' : '' ?>>Trancados</option>
                        <option value="Cancelado" <?php echo $status === 'Cancelado' ? 'selected' : '' ?>>Cancelados</option>
                    </select>
                </div>
                <div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Filtrar</button></div>
            </form>
        </div>
    </div>

    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($mensagem); ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Exibindo <?php echo count($alunos); ?> de <?php echo $total_alunos; ?> aluno(s)</span>
    </div>

    <!-- Lista de Alunos em Cards -->
    <div class="row">
        <?php if (empty($alunos)): ?>
            <div class="col-12"><div class="alert alert-info text-center">Nenhum aluno encontrado.</div></div>
        <?php else: ?>
            <?php foreach ($alunos as $aluno): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card student-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <!-- ATUALIZADO: Mostra foto do perfil ou placeholder -->
                                <?php
                                    $foto_aluno = (!empty($aluno['foto_perfil']) && file_exists($aluno['foto_perfil'])) ? $aluno['foto_perfil'] : 'https://i.pravatar.cc/150?u=' . $aluno['id'];
                                ?>
                                <img src="<?php echo $foto_aluno; ?>" alt="Avatar" class="rounded-circle me-3 student-avatar">
                                <div>
                                    <h5 class="card-title mb-0 fs-6"><?php echo htmlspecialchars($aluno['nome']); ?></h5>
                                    <small class="text-muted">ID: <?php echo $aluno['id']; ?></small>
                                </div>
                            </div>
                            <div class="mt-auto pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php 
                                        $status_class = $aluno['status'] === 'Ativo' ? 'success' : ($aluno['status'] === 'Trancado' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo $status_class; ?>"><?php echo $aluno['status']; ?></span>
                                    <div class="btn-group">
                                        <a href="editar_aluno.php?id=<?php echo $aluno['id']; ?>" class="btn btn-sm btn-outline-primary" title="Editar Aluno"><i class="fas fa-pencil-alt"></i></a>
                                        <?php if ($aluno['status'] === 'Ativo'): ?>
                                            <button class="btn btn-sm btn-outline-warning" title="Trancar Aluno" data-bs-toggle="modal" data-bs-target="#statusModal" data-aluno-id="<?php echo $aluno['id']; ?>" data-aluno-nome="<?php echo htmlspecialchars($aluno['nome']); ?>" data-acao="Trancado"><i class="fas fa-lock"></i></button>
                                            <button class="btn btn-sm btn-outline-danger" title="Cancelar Matrícula" data-bs-toggle="modal" data-bs-target="#statusModal" data-aluno-id="<?php echo $aluno['id']; ?>" data-aluno-nome="<?php echo htmlspecialchars($aluno['nome']); ?>" data-acao="Cancelado"><i class="fas fa-user-times"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Paginação -->
    <nav class="mt-4"><ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?php echo $i === $pagina_atual ? 'active' : ''; ?>"><a class="page-link" href="?pagina=<?php echo $i; ?>&nome=<?php echo urlencode($nome); ?>&status=<?php echo urlencode($status); ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>
    </ul></nav>
</div>

<!-- Modal de Confirmação de Status -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Alteração de Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="aluno_id" id="modal_aluno_id">
                    <input type="hidden" name="acao_status" id="modal_acao_status">
                    <p>Você tem certeza que deseja <strong id="modal_acao_texto"></strong> a matrícula do aluno <strong id="modal_aluno_nome"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-danger" id="modal_confirm_button">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusModal = document.getElementById('statusModal');
    if (statusModal) {
        statusModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const modalConfirmBtn = statusModal.querySelector('#modal_confirm_button');
            const acao = button.getAttribute('data-acao');
            
            statusModal.querySelector('#modal_aluno_id').value = button.getAttribute('data-aluno-id');
            statusModal.querySelector('#modal_aluno_nome').textContent = button.getAttribute('data-aluno-nome');
            statusModal.querySelector('#modal_acao_status').value = acao;
            statusModal.querySelector('#modal_acao_texto').textContent = acao.toLowerCase();
            
            modalConfirmBtn.className = `btn ${acao === 'Trancado' ? 'btn-warning' : 'btn-danger'}`;
            modalConfirmBtn.textContent = `Sim, ${acao}`;
        });
    }
});
</script>
</body>
</html>
