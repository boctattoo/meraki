<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// Buscar estatísticas das tarefas
try {
    // Contar tarefas por status
    $sql_stats = "SELECT 
                    status,
                    COUNT(*) as total
                  FROM tarefas 
                  GROUP BY status";
    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Tarefas do usuário atual
    $sql_minhas = "SELECT 
                     COUNT(*) as total,
                     SUM(CASE WHEN status = 'afazer' THEN 1 ELSE 0 END) as afazer,
                     SUM(CASE WHEN status = 'progresso' THEN 1 ELSE 0 END) as progresso,
                     SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as concluido
                   FROM tarefas t
                   LEFT JOIN tarefas_usuarios tu ON t.id = tu.tarefa_id
                   WHERE t.usuario_id = ? OR tu.usuario_id = ?";
    $stmt_minhas = $pdo->prepare($sql_minhas);
    $stmt_minhas->execute([$_SESSION['usuario_id'], $_SESSION['usuario_id']]);
    $minhas_tarefas = $stmt_minhas->fetch();
    
    // Tarefas recentes (últimas 5)
    $sql_recentes = "SELECT 
                       t.id,
                       t.titulo,
                       t.status,
                       t.prioridade,
                       t.data_criacao,
                       u.nome as criador
                     FROM tarefas t
                     JOIN usuarios u ON t.usuario_id = u.id
                     ORDER BY t.data_criacao DESC
                     LIMIT 5";
    $stmt_recentes = $pdo->prepare($sql_recentes);
    $stmt_recentes->execute();
    $tarefas_recentes = $stmt_recentes->fetchAll();
    
    // Tarefas com prazo próximo
    $sql_prazos = "SELECT 
                     t.id,
                     t.titulo,
                     t.data_evento,
                     t.status,
                     t.prioridade,
                     DATEDIFF(t.data_evento, CURDATE()) as dias_restantes
                   FROM tarefas t
                   WHERE t.data_evento IS NOT NULL 
                     AND t.status != 'concluido'
                     AND t.data_evento >= CURDATE()
                   ORDER BY t.data_evento ASC
                   LIMIT 5";
    $stmt_prazos = $pdo->prepare($sql_prazos);
    $stmt_prazos->execute();
    $prazos_proximos = $stmt_prazos->fetchAll();
    
} catch (Exception $e) {
    $erro_stats = "Erro ao carregar estatísticas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistema Meraki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0;
        }
        .recent-task {
            border-left: 4px solid #3b82f6;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
            transition: all 0.3s ease;
        }
        .recent-task:hover {
            background: #e9ecef;
            border-left-color: #1e40af;
        }
        .priority-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
        }
        .priority-alta { background-color: #dc3545; color: white; }
        .priority-media { background-color: #fd7e14; color: white; }
        .priority-baixa { background-color: #28a745; color: white; }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
        }
        .status-afazer { background-color: #6c757d; color: white; }
        .status-progresso { background-color: #fd7e14; color: white; }
        .status-concluido { background-color: #28a745; color: white; }
        .deadline-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        .deadline-danger {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .welcome-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="bg-light">

<?php include 'nav.php'; ?>

<div class="container-fluid mt-4">
    <!-- Header de Boas-vindas -->
    <div class="welcome-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <p class="mb-0 opacity-75">
                    Bem-vindo(a), <strong><?= htmlspecialchars($_SESSION['nome']) ?></strong>! 
                    Aqui está o resumo das suas atividades.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="kanban.php" class="btn btn-light">
                        <i class="fas fa-tasks"></i> Ir para Kanban
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($erro_stats)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($erro_stats) ?>
        </div>
    <?php endif; ?>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="stats-number"><?= $stats['afazer'] ?? 0 ?></h3>
                        <p class="mb-0">A Fazer</p>
                    </div>
                    <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #fd7e14 0%, #ff6b6b 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="stats-number"><?= $stats['progresso'] ?? 0 ?></h3>
                        <p class="mb-0">Em Progresso</p>
                    </div>
                    <i class="fas fa-hourglass-half fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="stats-number"><?= $stats['concluido'] ?? 0 ?></h3>
                        <p class="mb-0">Concluído</p>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="stats-number"><?= $minhas_tarefas['total'] ?? 0 ?></h3>
                        <p class="mb-0">Minhas Tarefas</p>
                    </div>
                    <i class="fas fa-user-check fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tarefas Recentes -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Tarefas Recentes</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($tarefas_recentes)): ?>
                        <p class="text-muted">Nenhuma tarefa encontrada.</p>
                    <?php else: ?>
                        <?php foreach ($tarefas_recentes as $tarefa): ?>
                            <div class="recent-task">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-1"><?= htmlspecialchars($tarefa['titulo']) ?></h6>
                                    <div class="d-flex gap-1">
                                        <?php if ($tarefa['prioridade']): ?>
                                            <span class="priority-badge priority-<?= strtolower(str_replace('é', 'e', $tarefa['prioridade'])) ?>">
                                                <?= htmlspecialchars($tarefa['prioridade']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="status-badge status-<?= $tarefa['status'] ?>">
                                            <?= ucfirst(str_replace(['afazer', 'progresso', 'concluido'], ['A Fazer', 'Em Progresso', 'Concluído'], $tarefa['status'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($tarefa['criador']) ?> • 
                                    <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($tarefa['data_criacao'])) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <a href="kanban.php" class="btn btn-outline-primary">
                            <i class="fas fa-tasks"></i> Ver Todas as Tarefas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prazos Próximos -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-calendar-exclamation"></i> Prazos Próximos</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($prazos_proximos)): ?>
                        <p class="text-muted">Nenhuma tarefa com prazo próximo.</p>
                    <?php else: ?>
                        <?php foreach ($prazos_proximos as $prazo): ?>
                            <?php 
                                $dias = $prazo['dias_restantes'];
                                $classe_urgencia = '';
                                if ($dias <= 1) {
                                    $classe_urgencia = 'deadline-danger';
                                } elseif ($dias <= 3) {
                                    $classe_urgencia = 'deadline-warning';
                                }
                            ?>
                            <div class="recent-task <?= $classe_urgencia ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-1"><?= htmlspecialchars($prazo['titulo']) ?></h6>
                                    <div class="d-flex gap-1">
                                        <?php if ($prazo['prioridade']): ?>
                                            <span class="priority-badge priority-<?= strtolower(str_replace('é', 'e', $prazo['prioridade'])) ?>">
                                                <?= htmlspecialchars($prazo['prioridade']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($prazo['data_evento'])) ?>
                                    </small>
                                    <small class="fw-bold <?= $dias <= 1 ? 'text-danger' : ($dias <= 3 ? 'text-warning' : 'text-success') ?>">
                                        <?php if ($dias == 0): ?>
                                            <i class="fas fa-exclamation-triangle"></i> Hoje!
                                        <?php elseif ($dias == 1): ?>
                                            <i class="fas fa-clock"></i> Amanhã
                                        <?php else: ?>
                                            <i class="fas fa-calendar-day"></i> <?= $dias ?> dias
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="text-center mt-3">
                        <a href="kanban.php" class="btn btn-outline-warning">
                            <i class="fas fa-calendar-check"></i> Gerenciar Prazos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo das Minhas Tarefas -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tasks"></i> Resumo das Minhas Tarefas</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h4 class="text-primary"><?= $minhas_tarefas['total'] ?? 0 ?></h4>
                                <p class="text-muted mb-0">Total</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h4 class="text-secondary"><?= $minhas_tarefas['afazer'] ?? 0 ?></h4>
                                <p class="text-muted mb-0">A Fazer</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h4 class="text-warning"><?= $minhas_tarefas['progresso'] ?? 0 ?></h4>
                                <p class="text-muted mb-0">Em Progresso</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h4 class="text-success"><?= $minhas_tarefas['concluido'] ?? 0 ?></h4>
                                <p class="text-muted mb-0">Concluído</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Atualizar dados a cada 30 segundos
setInterval(() => {
    location.reload();
}, 30000);
</script>

</body>
</html>