<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require 'conexao.php'; // Inclui a conexão com o banco

// Pega o nome do usuário da sessão ou define um padrão
$usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário Meraki';

// --- CONSULTAS PARA OS KPIs ---
try {
    // Total de alunos com status 'Ativo'
    $stmt_alunos = $pdo->query("SELECT COUNT(id) FROM alunos WHERE status = 'Ativo'");
    $total_alunos_ativos = $stmt_alunos->fetchColumn();

    // Total de turmas com status 'ativa'
    $stmt_turmas = $pdo->query("SELECT COUNT(id) FROM turmas WHERE status = 'ativa'");
    $total_turmas_ativas = $stmt_turmas->fetchColumn();

    // CPA (Controle de Presença do Aluno) do dia
    $stmt_cpa = $pdo->query(
        "SELECT 
            (SUM(CASE WHEN presente = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(id)) AS cpa_dia 
        FROM presencas 
        WHERE data = CURDATE()"
    );
    $cpa_dia = $stmt_cpa->fetchColumn();
    $cpa_dia = $cpa_dia ? round($cpa_dia, 1) : 0; // Arredonda e trata caso não haja presenças

} catch (Exception $e) {
    // Em caso de erro, define valores padrão para não quebrar a página
    $total_alunos_ativos = 'N/A';
    $total_turmas_ativas = 'N/A';
    $cpa_dia = 'N/A';
    // O ideal seria logar o erro: error_log($e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Meraki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .kpi-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
        }
        .kpi-icon {
            font-size: 2.5rem;
            opacity: 0.3;
        }
        .kpi-value {
            font-size: 2.2rem;
            font-weight: 700;
        }
        .access-card {
            text-decoration: none;
            color: #212529;
            display: block;
            padding: 1.5rem;
            border-radius: 0.75rem;
            background-color: #ffffff;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            height: 100%;
        }
        .access-card:hover {
            transform: scale(1.05);
            background-color: #e9ecef;
        }
        .access-card i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>

    <?php include 'nav.php'; // Inclui a barra de navegação ?>

    <div class="container py-4">
        <!-- Cabeçalho de Boas-vindas -->
        <div class="d-flex align-items-center mb-4">
            <img src="https://i.pravatar.cc/150?u=<?php echo $_SESSION['usuario_id']; ?>" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%;" class="me-3">
            <div>
                <h2 class="h4 mb-0">Bem-vindo(a), <?php echo htmlspecialchars($usuario_nome); ?>!</h2>
                <p class="text-muted mb-0">Aqui está um resumo das atividades da escola.</p>
            </div>
        </div>

        <!-- KPIs (Indicadores Chave) -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card kpi-card bg-primary text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="kpi-value"><?php echo $total_alunos_ativos; ?></div>
                            <div class="text-white-75">Alunos Ativos</div>
                        </div>
                        <i class="fas fa-users kpi-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card kpi-card bg-success text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="kpi-value"><?php echo $total_turmas_ativas; ?></div>
                            <div class="text-white-75">Turmas Ativas</div>
                        </div>
                        <i class="fas fa-chalkboard-teacher kpi-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card kpi-card bg-info text-white">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="kpi-value"><?php echo $cpa_dia; ?>%</div>
                            <div class="text-white-75">CPA do Dia</div>
                        </div>
                        <i class="fas fa-percentage kpi-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acesso Rápido -->
        <h3 class="h5 mb-3">Acesso Rápido</h3>
        <div class="row">
            <div class="col-md-3 col-6 mb-3">
                <a href="cadastro_aluno.php" class="access-card text-center">
                    <i class="fas fa-user-plus text-primary"></i>
                    <p class="mb-0 fw-bold">Novo Aluno</p>
                </a>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <a href="mapa_turmas.php" class="access-card text-center">
                    <i class="fas fa-map-marked-alt text-success"></i>
                    <p class="mb-0 fw-bold">Mapa de Turmas</p>
                </a>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <a href="lancar_presenca.php" class="access-card text-center">
                    <i class="fas fa-user-check text-info"></i>
                    <p class="mb-0 fw-bold">Lançar Presença</p>
                </a>
            </div>
            <div class="col-md-3 col-6 mb-3">
                 <a href="precadastro.php" class="access-card text-center">
                    <i class="fas fa-file-signature text-warning"></i>
                    <p class="mb-0 fw-bold">Novo Contrato</p>
                </a>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
