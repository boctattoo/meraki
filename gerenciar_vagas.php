<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

try {
    require 'conexao.php';
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

$mensagem = '';

// Processar formulário de atualização de vagas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_vagas'])) {
    $turma_id = (int)$_POST['turma_id'];
    $vagas_total = (int)$_POST['vagas_total'];
    
    if ($turma_id > 0 && $vagas_total >= 0) {
        try {
            $stmt = $pdo->prepare("UPDATE turmas SET vagas_total = ? WHERE id = ?");
            $stmt->execute([$vagas_total, $turma_id]);
            $mensagem = "Vagas atualizadas com sucesso!";
        } catch (Exception $e) {
            $mensagem = "Erro ao atualizar vagas: " . $e->getMessage();
        }
    } else {
        $mensagem = "Dados inválidos.";
    }
}

// Buscar dados das turmas com ocupação
try {
    $sql = "
        SELECT 
            t.id,
            t.nome,
            t.vagas_total,
            COUNT(at.aluno_id) as alunos_matriculados,
            CASE 
                WHEN t.vagas_total > 0 THEN (t.vagas_total - COUNT(at.aluno_id))
                ELSE -1 
            END as vagas_restantes,
            CASE 
                WHEN t.vagas_total > 0 THEN ROUND((COUNT(at.aluno_id) / t.vagas_total) * 100, 1)
                ELSE NULL
            END as percentual_ocupacao,
            CASE 
                WHEN t.vagas_total = 0 THEN 'SEM_LIMITE'
                WHEN t.vagas_total > 0 AND COUNT(at.aluno_id) >= t.vagas_total THEN 'LOTADA'
                WHEN t.vagas_total > 0 AND (t.vagas_total - COUNT(at.aluno_id)) <= 2 THEN 'QUASE_LOTADA'
                WHEN t.vagas_total > 0 THEN 'DISPONIVEL'
                ELSE 'SEM_LIMITE'
            END as status_vagas
        FROM turmas t
        LEFT JOIN alunos_turmas at ON (t.id = at.turma_id AND at.ativo = 1)
        LEFT JOIN alunos a ON (a.id = at.aluno_id AND a.status IN ('Ativo', 'ativo'))
        GROUP BY t.id, t.nome, t.vagas_total
        ORDER BY t.nome
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $turmas = [];
    $mensagem = "Erro ao carregar turmas: " . $e->getMessage();
}

// Estatísticas gerais
$estatisticas = [
    'total_turmas' => count($turmas),
    'turmas_lotadas' => 0,
    'turmas_quase_lotadas' => 0,
    'turmas_disponiveis' => 0,
    'turmas_sem_limite' => 0
];

foreach ($turmas as $turma) {
    switch ($turma['status_vagas']) {
        case 'LOTADA':
            $estatisticas['turmas_lotadas']++;
            break;
        case 'QUASE_LOTADA':
            $estatisticas['turmas_quase_lotadas']++;
            break;
        case 'DISPONIVEL':
            $estatisticas['turmas_disponiveis']++;
            break;
        case 'SEM_LIMITE':
            $estatisticas['turmas_sem_limite']++;
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Vagas das Turmas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .status-sem-limite { background-color: #e3f2fd; }
        .status-disponivel { background-color: #e8f5e8; }
        .status-quase-lotada { background-color: #fff3e0; }
        .status-lotada { background-color: #ffebee; }
        .progress { height: 8px; }
        .modal-vagas .form-control { max-width: 120px; }
    </style>
</head>
<body>
<?php if (file_exists('nav.php')) include 'nav.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-people-fill"></i> 
                            Gerenciar Vagas das Turmas
                        </h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                <i class="bi bi-printer"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <?php if ($mensagem): ?>
                        <div class="alert <?= strpos($mensagem, 'sucesso') !== false ? 'alert-success' : 'alert-danger' ?>" role="alert">
                            <?= htmlspecialchars($mensagem) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Estatísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="bi bi-collection text-primary fs-1"></i>
                                    <h5 class="mt-2"><?= $estatisticas['total_turmas'] ?></h5>
                                    <small class="text-muted">Total de Turmas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle text-success fs-1"></i>
                                    <h5 class="mt-2"><?= $estatisticas['turmas_disponiveis'] ?></h5>
                                    <small class="text-muted">Turmas Disponíveis</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <i class="bi bi-exclamation-triangle text-warning fs-1"></i>
                                    <h5 class="mt-2"><?= $estatisticas['turmas_quase_lotadas'] ?></h5>
                                    <small class="text-muted">Quase Lotadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <i class="bi bi-x-circle text-danger fs-1"></i>
                                    <h5 class="mt-2"><?= $estatisticas['turmas_lotadas'] ?></h5>
                                    <small class="text-muted">Turmas Lotadas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Turmas -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Turma</th>
                                    <th>Limite</th>
                                    <th>Matriculados</th>
                                    <th>Disponíveis</th>
                                    <th>Ocupação</th>
                                    <th>Status</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($turmas as $turma): ?>
                                    <?php 
                                        $classe_status = '';
                                        $badge_status = '';
                                        $icone_status = '';
                                        
                                        switch ($turma['status_vagas']) {
                                            case 'SEM_LIMITE':
                                                $classe_status = 'status-sem-limite';
                                                $badge_status = 'bg-info';
                                                $icone_status = 'bi-infinity';
                                                break;
                                            case 'DISPONIVEL':
                                                $classe_status = 'status-disponivel';
                                                $badge_status = 'bg-success';
                                                $icone_status = 'bi-check-circle';
                                                break;
                                            case 'QUASE_LOTADA':
                                                $classe_status = 'status-quase-lotada';
                                                $badge_status = 'bg-warning';
                                                $icone_status = 'bi-exclamation-triangle';
                                                break;
                                            case 'LOTADA':
                                                $classe_status = 'status-lotada';
                                                $badge_status = 'bg-danger';
                                                $icone_status = 'bi-x-circle';
                                                break;
                                        }
                                    ?>
                                    <tr class="<?= $classe_status ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($turma['nome']) ?></strong>
                                        </td>
                                        <td>
                                            <?= $turma['vagas_total'] > 0 ? $turma['vagas_total'] : 'Ilimitado' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= $turma['alunos_matriculados'] ?></span>
                                        </td>
                                        <td>
                                            <?php if ($turma['vagas_total'] > 0): ?>
                                                <span class="badge <?= $turma['vagas_restantes'] <= 0 ? 'bg-danger' : ($turma['vagas_restantes'] <= 2 ? 'bg-warning' : 'bg-success') ?>">
                                                    <?= max(0, $turma['vagas_restantes']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-info">∞</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($turma['vagas_total'] > 0): ?>
                                                <div class="progress">
                                                    <div class="progress-bar <?= $turma['percentual_ocupacao'] >= 100 ? 'bg-danger' : ($turma['percentual_ocupacao'] >= 90 ? 'bg-warning' : 'bg-success') ?>" 
                                                         style="width: <?= min(100, $turma['percentual_ocupacao']) ?>%">
                                                    </div>
                                                </div>
                                                <small class="text-muted"><?= $turma['percentual_ocupacao'] ?>%</small>
                                            <?php else: ?>
                                                <small class="text-muted">Sem limite</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $badge_status ?>">
                                                <i class="bi <?= $icone_status ?>"></i>
                                                <?= ucfirst(str_replace('_', ' ', strtolower($turma['status_vagas']))) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="editarVagas(<?= $turma['id'] ?>, '<?= htmlspecialchars($turma['nome']) ?>', <?= $turma['vagas_total'] ?>)"
                                                    title="Editar vagas">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="buscar_aluno.php?turma=<?= $turma['id'] ?>" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Ver alunos">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (empty($turmas)): ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i>
                            Nenhuma turma encontrada.
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Vagas -->
<div class="modal fade" id="modalEditarVagas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square"></i> 
                        Editar Vagas da Turma
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-vagas">
                    <input type="hidden" name="turma_id" id="edit_turma_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Turma:</label>
                        <div class="form-control-plaintext fw-bold" id="edit_turma_nome"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_vagas_total" class="form-label">
                            Número de Vagas:
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="edit_vagas_total" 
                               name="vagas_total" 
                               min="0" 
                               max="999"
                               required>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i>
                            Digite 0 para vagas ilimitadas
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" name="atualizar_vagas" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editarVagas(id, nome, vagas) {
    document.getElementById('edit_turma_id').value = id;
    document.getElementById('edit_turma_nome').textContent = nome;
    document.getElementById('edit_vagas_total').value = vagas;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEditarVagas'));
    modal.show();
}

// Auto-refresh a cada 2 minutos para dados atualizados
setTimeout(function() {
    location.reload();
}, 120000);
</script>

</body>
</html>