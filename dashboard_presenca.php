<?php
session_start();
// Garante que o usuário esteja logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require 'conexao.php'; // Inclui seu script de conexão

// --- INÍCIO DA LÓGICA DE BUSCA DE DADOS ---

$dados_dashboard = [];

try {
    // 1. KPIs do Dia (Presenças, Faltas, CPA do dia)
    $stmt_kpi_dia = $pdo->prepare(
        "SELECT
            COALESCE(SUM(CASE WHEN presente = 1 THEN 1 ELSE 0 END), 0) AS presentes_hoje,
            COALESCE(SUM(CASE WHEN presente = 0 THEN 1 ELSE 0 END), 0) AS faltas_hoje
        FROM presencas
        WHERE data = CURDATE()"
    );
    $stmt_kpi_dia->execute();
    $kpi_dia = $stmt_kpi_dia->fetch(PDO::FETCH_ASSOC);

    $total_chamada_hoje = $kpi_dia['presentes_hoje'] + $kpi_dia['faltas_hoje'];
    $dados_dashboard['presentes_hoje'] = $kpi_dia['presentes_hoje'];
    $dados_dashboard['faltas_hoje'] = $kpi_dia['faltas_hoje'];
    $dados_dashboard['cpa_hoje'] = ($total_chamada_hoje > 0) ? round(($kpi_dia['presentes_hoje'] * 100) / $total_chamada_hoje, 1) : 0;

    // 2. Gráfico de Presenças (Últimos 7 dias)
    $stmt_grafico = $pdo->prepare(
        "SELECT
            data,
            SUM(CASE WHEN presente = 1 THEN 1 ELSE 0 END) AS presentes,
            SUM(CASE WHEN presente = 0 THEN 1 ELSE 0 END) AS faltas
        FROM presencas
        WHERE data BETWEEN CURDATE() - INTERVAL 6 DAY AND CURDATE()
        GROUP BY data
        ORDER BY data ASC"
    );
    $stmt_grafico->execute();
    $dados_grafico = $stmt_grafico->fetchAll(PDO::FETCH_ASSOC);
    
    // Processa dados para o formato do Chart.js
    $labels_grafico = [];
    $data_presentes = [];
    $data_faltas = [];
    $periodo = new DatePeriod(new DateTime('-6 days'), new DateInterval('P1D'), new DateTime('+1 day'));
    
    $presencas_por_data = [];
    foreach($dados_grafico as $row) {
        $presencas_por_data[$row['data']] = $row;
    }

    foreach ($periodo as $dia) {
        $data_formatada = $dia->format('Y-m-d');
        $labels_grafico[] = $dia->format('d/m');
        $data_presentes[] = isset($presencas_por_data[$data_formatada]) ? $presencas_por_data[$data_formatada]['presentes'] : 0;
        $data_faltas[] = isset($presencas_por_data[$data_formatada]) ? $presencas_por_data[$data_formatada]['faltas'] : 0;
    }
    
    $dados_dashboard['grafico'] = [
        'labels' => $labels_grafico,
        'presentes' => $data_presentes,
        'faltas' => $data_faltas
    ];


    // 3. Alunos sem presença no mês
    $stmt_sem_presenca = $pdo->prepare(
        "SELECT a.nome AS nome_aluno, t.nome AS nome_turma, a.telefone
         FROM alunos a
         JOIN alunos_turmas at ON a.id = at.aluno_id
         JOIN turmas t ON at.turma_id = t.id
         WHERE a.status = 'Ativo' AND at.ativo = 1 AND a.id NOT IN (
             SELECT DISTINCT aluno_id FROM presencas
             WHERE MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())
         )
         ORDER BY a.nome"
    );
    $stmt_sem_presenca->execute();
    $dados_dashboard['alunos_sem_presenca'] = $stmt_sem_presenca->fetchAll(PDO::FETCH_ASSOC);

    // 4. Turmas sem chamada hoje (para o dia da semana atual)
    // WEEKDAY() -> Segunda=0, ..., Domingo=6 | dias_semana.id -> Segunda=1, ..., Domingo=7
    $dia_semana_mysql = "WEEKDAY(CURDATE()) + 1"; 
    $stmt_sem_chamada = $pdo->prepare(
        "SELECT t.nome AS nome_turma, i.nome AS nome_instrutor
         FROM turmas t
         LEFT JOIN instrutores i ON t.instrutor_id = i.id
         WHERE t.status = 'ativa' AND t.dia_semana_id = ($dia_semana_mysql)
           AND (t.ultima_presenca_em IS NULL OR t.ultima_presenca_em != CURDATE())
         ORDER BY t.nome"
    );
    $stmt_sem_chamada->execute();
    $dados_dashboard['turmas_sem_chamada'] = $stmt_sem_chamada->fetchAll(PDO::FETCH_ASSOC);


} catch (Exception $e) {
    die("Erro ao carregar o dashboard: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Presença | Meraki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .kpi-card {
            color: white;
            border-radius: 0.5rem;
        }
        .kpi-card .display-4 {
            font-weight: 700;
        }
        .bg-success-gradient { background: linear-gradient(45deg, #28a745, #218838); }
        .bg-danger-gradient { background: linear-gradient(45deg, #dc3545, #c82333); }
        .bg-info-gradient { background: linear-gradient(45deg, #17a2b8, #138496); }
    </style>
</head>
<body>

<?php 
if (file_exists('nav.php')) {
    include 'nav.php';
} 
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="fas fa-chart-line"></i> Dashboard de Presença</h2>

    <!-- KPIs do Dia -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card kpi-card bg-success-gradient text-center p-3">
                <div class="card-body">
                    <p class="card-title fs-5"><i class="fas fa-user-check"></i> Presentes Hoje</p>
                    <p class="display-4"><?php echo $dados_dashboard['presentes_hoje']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card kpi-card bg-danger-gradient text-center p-3">
                <div class="card-body">
                    <p class="card-title fs-5"><i class="fas fa-user-times"></i> Faltas Hoje</p>
                    <p class="display-4"><?php echo $dados_dashboard['faltas_hoje']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card kpi-card bg-info-gradient text-center p-3">
                <div class="card-body">
                    <p class="card-title fs-5"><i class="fas fa-percentage"></i> CPA do Dia</p>
                    <p class="display-4"><?php echo $dados_dashboard['cpa_hoje']; ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico -->
    <div class="card mb-4">
        <div class="card-header fw-bold">
            <i class="fas fa-chart-bar"></i> Frequência nos Últimos 7 Dias
        </div>
        <div class="card-body">
            <canvas id="graficoPresenca"></canvas>
        </div>
    </div>

    <!-- Tabelas de Listas Críticas -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header fw-bold bg-warning text-dark">
                    <i class="fas fa-user-clock"></i> Alunos Sem Presença no Mês
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($dados_dashboard['alunos_sem_presenca'])): ?>
                        <p class="p-3 text-center text-muted">Nenhum aluno encontrado.</p>
                    <?php else: ?>
                        <table class="table table-striped table-hover mb-0">
                            <tbody>
                                <?php foreach ($dados_dashboard['alunos_sem_presenca'] as $aluno): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($aluno['nome_aluno']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($aluno['nome_turma']); ?></small>
                                        </td>
                                        <td class="text-end">
                                            <a href="tel:<?php echo htmlspecialchars($aluno['telefone']); ?>" class="btn btn-sm btn-outline-success">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header fw-bold bg-danger text-white">
                    <i class="fas fa-calendar-times"></i> Turmas Sem Chamada Hoje
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                     <?php if (empty($dados_dashboard['turmas_sem_chamada'])): ?>
                        <p class="p-3 text-center text-muted">Nenhuma turma pendente.</p>
                    <?php else: ?>
                        <table class="table table-striped table-hover mb-0">
                           <tbody>
                                <?php foreach ($dados_dashboard['turmas_sem_chamada'] as $turma): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($turma['nome_turma']); ?></strong><br>
                                            <small class="text-muted">Instrutor(a): <?php echo htmlspecialchars($turma['nome_instrutor']); ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('graficoPresenca').getContext('2d');
    const graficoPresenca = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($dados_dashboard['grafico']['labels']); ?>,
            datasets: [
                {
                    label: 'Presentes',
                    data: <?php echo json_encode($dados_dashboard['grafico']['presentes']); ?>,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Faltas',
                    data: <?php echo json_encode($dados_dashboard['grafico']['faltas']); ?>,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                    text: 'Frequência nos Últimos 7 Dias'
                }
            }
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
