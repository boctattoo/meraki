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

// ==================== CONFIGURAÇÕES E CONSTANTES ====================
const ITEMS_PER_PAGE = 6;
const DIAS_SEMANA = [
    'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira',
    'Quinta-feira', 'Sexta-feira', 'Sábado'
];

// ==================== FUNÇÕES AUXILIARES CORRIGIDAS ====================

/**
 * Mapeia nomes alternativos de dias para os corretos do banco
 */
function mapearDiaSemana(string $dia): string {
    $diaNormalizado = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $dia));
    
    $mapeamento = [
        'terca' => 'Terça',
        'tercas' => 'Terça',
        'sabado' => 'Sábado', 
        'sabados' => 'Sábado',
        'quarta' => 'Quarta',
        'quartas' => 'Quarta',
        'quinta' => 'Quinta',
        'quintas' => 'Quinta',
        'segunda' => 'Segunda',
        'segundas' => 'Segunda',
        'sexta' => 'Sexta',
        'sextas' => 'Sexta',
        'domingo' => 'Domingo',
        'domingos' => 'Domingo',
        'terça' => 'Terça',
        'terças' => 'Terça',
        'sábado' => 'Sábado',
        'sábados' => 'Sábado',
        'Segunda' => 'Segunda',
        'Terça' => 'Terça',
        'Quarta' => 'Quarta', 
        'Quinta' => 'Quinta',
        'Sexta' => 'Sexta',
        'Sábado' => 'Sábado',
        'Domingo' => 'Domingo'
    ];
    
    if (isset($mapeamento[$diaNormalizado])) {
        return $mapeamento[$diaNormalizado];
    }
    
    if (isset($mapeamento[$dia])) {
        return $mapeamento[$dia];
    }
    
    return $dia;
}

/**
 * Valida e sanitiza parâmetros de filtro
 */
function sanitizeFiltros(): array {
    $filtros = [
        'dia' => trim(filter_input(INPUT_GET, 'dia', FILTER_UNSAFE_RAW) ?: ''),
        'instrutor' => trim(filter_input(INPUT_GET, 'instrutor', FILTER_UNSAFE_RAW) ?: ''),
        'vagas' => trim(filter_input(INPUT_GET, 'vagas', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: ''),
        'tipo' => trim(filter_input(INPUT_GET, 'tipo', FILTER_UNSAFE_RAW) ?: ''),
        'limpar' => filter_input(INPUT_GET, 'limpar', FILTER_VALIDATE_BOOLEAN)
    ];
    
    $filtros['dia'] = html_entity_decode(urldecode($filtros['dia']), ENT_QUOTES, 'UTF-8');
    $filtros['instrutor'] = html_entity_decode(urldecode($filtros['instrutor']), ENT_QUOTES, 'UTF-8');
    $filtros['tipo'] = html_entity_decode(urldecode($filtros['tipo']), ENT_QUOTES, 'UTF-8');
    
    return $filtros;
}

/**
 * Carrega dados das turmas com filtros aplicados
 */
function carregarTurmas(PDO $pdo, array $filtros): array {
    $sql = "
        SELECT
            t.id,
            t.nome AS turma_nome,
            COALESCE(i.nome, 'Não definido') AS instrutor,
            COALESCE(d.nome, 'Não definido') AS dia,
            COALESCE(t.vagas_total, 0) AS vagas,
            COALESCE(tt.nome, 'Não definido') AS tipo,
            COALESCE(p.nome, 'Não definido') AS periodo,
            COUNT(at.aluno_id) AS total_alunos,
            t.ultima_presenca_em,
            CASE 
                WHEN t.vagas_total = 0 THEN 'SEM_LIMITE'
                WHEN t.vagas_total > 0 AND COUNT(at.aluno_id) >= t.vagas_total THEN 'LOTADA'
                WHEN t.vagas_total > 0 AND (t.vagas_total - COUNT(at.aluno_id)) <= 2 THEN 'QUASE_LOTADA'
                WHEN t.vagas_total > 0 THEN 'DISPONIVEL'
                ELSE 'SEM_LIMITE'
            END as status_vagas_db
        FROM turmas t
        LEFT JOIN instrutores i ON t.instrutor_id = i.id
        LEFT JOIN dias_semana d ON t.dia_semana_id = d.id
        LEFT JOIN periodos p ON t.periodo_id = p.id
        LEFT JOIN tipos_turma tt ON t.tipo_id = tt.id
        LEFT JOIN alunos_turmas at ON t.id = at.turma_id AND at.ativo = 1
        LEFT JOIN alunos a ON at.aluno_id = a.id AND a.status IN ('Ativo', 'ativo')
        WHERE t.status = 'ativa'
    ";

    $params = [];
    $conditions = [];

    if (!empty($filtros['dia'])) {
        $diaMapeado = mapearDiaSemana($filtros['dia']);
        $conditions[] = "d.nome = :dia";
        $params[':dia'] = $diaMapeado;
    }

    if (!empty($filtros['instrutor'])) {
        $conditions[] = "i.nome = :instrutor";
        $params[':instrutor'] = $filtros['instrutor'];
    }

    if (!empty($filtros['tipo'])) {
        $conditions[] = "UPPER(tt.nome) LIKE UPPER(:tipo)";
        $params[':tipo'] = '%' . $filtros['tipo'] . '%';
    }

    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    $sql .= "
        GROUP BY t.id, t.nome, i.nome, d.nome, t.vagas_total, tt.nome, p.nome, t.ultima_presenca_em
        ORDER BY 
            CASE tt.nome 
                WHEN 'MULTIMÍDIA' THEN 1 
                WHEN 'DINÂMICO' THEN 2 
                ELSE 3 
            END,
            CASE d.nome
                WHEN 'Segunda' THEN 1
                WHEN 'Terça' THEN 2  
                WHEN 'Quarta' THEN 3
                WHEN 'Quinta' THEN 4
                WHEN 'Sexta' THEN 5
                WHEN 'Sábado' THEN 6
                WHEN 'Domingo' THEN 7
                ELSE 8
            END,
            t.nome
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return processarTurmas($turmas, $filtros);
    } catch (PDOException $e) {
        error_log("Erro ao carregar turmas: " . $e->getMessage());
        return ['turmas' => [], 'estatisticas' => [], 'erro' => $e->getMessage()];
    }
}

/**
 * Processa dados das turmas
 */
function processarTurmas(array $turmasRaw, array $filtros): array {
    $diaHoje = DIAS_SEMANA[date('w')];
    
    $turmasProcessadas = [];
    $estatisticas = [
        'total_turmas' => 0,
        'total_alunos' => 0,
        'total_vagas' => 0,
        'total_disponiveis' => 0
    ];

    foreach ($turmasRaw as $turma) {
        $turma = processarTurmaIndividual($turma, $diaHoje);
        
        if (!aplicarFiltroVagas($turma, $filtros['vagas'])) {
            continue;
        }

        $turmasProcessadas[] = $turma;
        
        $estatisticas['total_turmas']++;
        $estatisticas['total_alunos'] += (int)$turma['total_alunos'];
        
        if ((int)$turma['vagas'] > 0) {
            $estatisticas['total_vagas'] += (int)$turma['vagas'];
            $estatisticas['total_disponiveis'] += (int)$turma['vagas_numericas'];
        }
    }

    return [
        'turmas' => $turmasProcessadas,
        'estatisticas' => $estatisticas
    ];
}

/**
 * Aplica filtro de vagas
 */
function aplicarFiltroVagas(array $turma, string $filtroVagas): bool {
    if (empty($filtroVagas)) {
        return true;
    }
    
    if ($filtroVagas === 'disponivel') {
        return $turma['status_vagas'] !== 'lotada';
    } 
    
    if ($filtroVagas === 'lotada') {
        return $turma['status_vagas'] === 'lotada';
    }

    return true;
}

/**
 * Processa dados individuais de uma turma
 */
function processarTurmaIndividual(array $turma, string $diaHoje): array {
    $vagas = (int)$turma['vagas'];
    $alunos = (int)$turma['total_alunos'];
    
    if ($vagas == 0) {
        $turma['disponiveis'] = 'Ilimitado';
        $turma['vagas_numericas'] = 999;
    } else {
        $turma['disponiveis'] = max(0, $vagas - $alunos);
        $turma['vagas_numericas'] = $turma['disponiveis'];
    }
    
    $turma['hoje'] = ($turma['dia'] === $diaHoje);
    
    if (isset($turma['status_vagas_db'])) {
        $turma['status_vagas'] = strtolower(str_replace('_', '-', $turma['status_vagas_db']));
    } else {
        if ($vagas == 0) {
            $turma['status_vagas'] = 'sem-limite';
        } elseif ($turma['disponiveis'] <= 0) {
            $turma['status_vagas'] = 'lotada';
        } elseif ($turma['disponiveis'] <= 2) {
            $turma['status_vagas'] = 'quase-lotada';
        } else {
            $turma['status_vagas'] = 'disponivel';
        }
    }
    
    switch ($turma['status_vagas']) {
        case 'lotada':
            $turma['card_class'] = 'danger';
            break;
        case 'quase-lotada':
            $turma['card_class'] = 'warning';
            break;
        case 'disponivel':
            $turma['card_class'] = 'success';
            break;
        case 'sem-limite':
            $turma['card_class'] = 'info';
            break;
        default:
            $turma['card_class'] = 'secondary';
    }

    return $turma;
}

/**
 * Carrega opções para filtros
 */
function carregarOpcoesFiltros(PDO $pdo): array {
    try {
        $dias = $pdo->query("
            SELECT DISTINCT d.nome 
            FROM dias_semana d 
            INNER JOIN turmas t ON d.id = t.dia_semana_id 
            WHERE t.status = 'ativa' 
            ORDER BY d.id
        ")->fetchAll(PDO::FETCH_COLUMN);
        
        $instrutores = $pdo->query("
            SELECT DISTINCT i.nome 
            FROM instrutores i 
            INNER JOIN turmas t ON i.id = t.instrutor_id 
            WHERE t.status = 'ativa' 
            ORDER BY i.nome
        ")->fetchAll(PDO::FETCH_COLUMN);
        
        $tipos = $pdo->query("
            SELECT DISTINCT tt.nome 
            FROM tipos_turma tt 
            INNER JOIN turmas t ON tt.id = t.tipo_id 
            WHERE t.status = 'ativa' 
            ORDER BY tt.nome
        ")->fetchAll(PDO::FETCH_COLUMN);
        
        return [
            'dias' => $dias,
            'instrutores' => $instrutores,
            'tipos' => $tipos
        ];
    } catch (PDOException $e) {
        error_log("Erro ao carregar opções de filtros: " . $e->getMessage());
        return ['dias' => [], 'instrutores' => [], 'tipos' => []];
    }
}

// ==================== PROCESSAMENTO PRINCIPAL ====================

$filtros = sanitizeFiltros();

if ($filtros['limpar']) {
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit();
}

$dadosTurmas = carregarTurmas($pdo, $filtros);
$turmas = $dadosTurmas['turmas'];
$estatisticas = $dadosTurmas['estatisticas'];
$opcoesFiltros = carregarOpcoesFiltros($pdo);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa de Turmas | Visualização de Vagas</title>
    
    <!-- Favicon -->
    <link rel="icon" href="/assets/favicon.ico" type="image/x-icon">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <?php if (file_exists('style.css')): ?>
        <link rel="stylesheet" href="style.css">
    <?php endif; ?>
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    
    <style>
        .filtro-debug {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
        }
        
        .filtro-ativo {
            background: #d4edda !important;
            border-color: #c3e6cb !important;
        }
        
        .animate-entry {
            animation: slideInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        
        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-turma {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .card-turma:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .status-badge {
            font-size: 0.8em;
            padding: 4px 8px;
            border-radius: 12px;
        }
        
        .hoje-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75em;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-light">
    <?php if (file_exists('nav.php')) include 'nav.php'; ?>

    <main class="container-fluid mt-4">
        
        <!-- Resumo Geral das Turmas -->
        <section class="row text-center mb-4" aria-label="Resumo geral das turmas">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-primary h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-collection-play text-primary"></i> 
                            Total de Turmas
                        </h6>
                        <h3 class="text-primary fw-bold mb-0">
                            <?= number_format($estatisticas['total_turmas']) ?>
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-success h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-person-fill text-success"></i> 
                            Total de Alunos
                        </h6>
                        <h3 class="text-success fw-bold mb-0">
                            <?= number_format($estatisticas['total_alunos']) ?>
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-info h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-grid-3x3-gap-fill text-info"></i> 
                            Total de Vagas
                        </h6>
                        <h3 class="text-info fw-bold mb-0">
                            <?= number_format($estatisticas['total_vagas']) ?>
                        </h3>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-warning h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">
                            <i class="bi bi-patch-check-fill text-warning"></i> 
                            Vagas Disponíveis
                        </h6>
                        <h3 class="text-warning fw-bold mb-0">
                            <?= number_format($estatisticas['total_disponiveis']) ?>
                        </h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- Data Atual -->
        <div class="row justify-content-center mb-4">
            <div class="col-auto">
                <div class="alert alert-info d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi bi-calendar-check me-2 fs-5"></i>
                    <strong>Visualizando turmas para: <?= date('d/m/Y') ?></strong>
                </div>
            </div>
        </div>

        <!-- Legenda Visual -->
        <div class="row justify-content-center mb-4">
            <div class="col-auto">
                <span class="badge bg-success px-3 py-2 me-2 shadow-sm">
                    <i class="bi bi-check-circle-fill me-1"></i> Vagas disponíveis
                </span>
                <span class="badge bg-warning text-dark px-3 py-2 me-2 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Poucas vagas
                </span>
                <span class="badge bg-danger px-3 py-2 me-2 shadow-sm">
                    <i class="bi bi-x-circle-fill me-1"></i> Turma lotada
                </span>
                <span class="badge bg-info px-3 py-2 shadow-sm">
                    <i class="bi bi-infinity me-1"></i> Vagas ilimitadas
                </span>
            </div>
        </div>

        <!-- Filtros de busca -->
        <section class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-funnel-fill me-2"></i>Filtros
                </h5>
            </div>
            <div class="card-body">
                <form class="row g-3 align-items-end" method="GET" id="filtro-form">
                    <!-- Filtro por Dia -->
                    <div class="col-md-3">
                        <label for="filtro-dia" class="form-label">Dia da Semana</label>
                        <select name="dia" id="filtro-dia" class="form-select <?= !empty($filtros['dia']) ? 'filtro-ativo' : '' ?>">
                            <option value="">Todos os dias</option>
                            <?php foreach ($opcoesFiltros['dias'] as $dia): ?>
                                <option value="<?= htmlspecialchars($dia) ?>" <?= $filtros['dia'] === $dia ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dia) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filtro por Instrutor -->
                    <div class="col-md-3">
                        <label for="filtro-instrutor" class="form-label">Instrutor</label>
                        <select name="instrutor" id="filtro-instrutor" class="form-select <?= !empty($filtros['instrutor']) ? 'filtro-ativo' : '' ?>">
                            <option value="">Todos os instrutores</option>
                            <?php foreach ($opcoesFiltros['instrutores'] as $instrutor): ?>
                                <option value="<?= htmlspecialchars($instrutor) ?>" <?= $filtros['instrutor'] === $instrutor ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($instrutor) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filtro por Tipo -->
                    <div class="col-md-2">
                        <label for="filtro-tipo" class="form-label">Tipo</label>
                        <select name="tipo" id="filtro-tipo" class="form-select <?= !empty($filtros['tipo']) ? 'filtro-ativo' : '' ?>">
                            <option value="">Todos os tipos</option>
                            <?php foreach ($opcoesFiltros['tipos'] as $tipo): ?>
                                <option value="<?= htmlspecialchars($tipo) ?>" <?= $filtros['tipo'] === $tipo ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tipo) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filtro por Status de Vagas -->
                    <div class="col-md-2">
                        <label for="filtro-vagas" class="form-label">Status</label>
                        <select name="vagas" id="filtro-vagas" class="form-select <?= !empty($filtros['vagas']) ? 'filtro-ativo' : '' ?>">
                            <option value="">Todas</option>
                            <option value="disponivel" <?= $filtros['vagas'] === 'disponivel' ? 'selected' : '' ?>>Com vagas</option>
                            <option value="lotada" <?= $filtros['vagas'] === 'lotada' ? 'selected' : '' ?>>Sem vagas</option>
                        </select>
                    </div>

                    <!-- Botão Limpar -->
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary w-100" id="btn-limpar-filtros">
                            <i class="bi bi-x-circle"></i> Limpar
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Resultado dos Filtros -->
        <?php if (count($turmas) > 0): ?>
            <div class="mb-3 text-end">
                <span class="badge bg-secondary px-3 py-2 shadow-sm">
                    <i class="bi bi-search me-1"></i>
                    <?= count($turmas) ?> turma(s) encontrada(s)
                </span>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center shadow-sm" role="alert">
                <i class="bi bi-info-circle me-2"></i> 
                Nenhuma turma encontrada com os filtros aplicados.
                <button type="button" class="btn btn-link p-0 ms-2" id="btn-limpar-inline">
                    Limpar filtros
                </button>
            </div>
        <?php endif; ?>

        <!-- Cards das Turmas -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="cards-container">
            <?php foreach ($turmas as $index => $turma): ?>
                <div class="col card-entry animate-entry" style="animation-delay: <?= $index * 0.1 ?>s">
                    <div class="card card-turma h-100 border-<?= $turma['card_class'] ?> border-2">
                        <div class="card-body d-flex flex-column">
                            
                            <!-- Cabeçalho da Turma -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold text-<?= $turma['card_class'] ?> mb-0">
                                    <?= htmlspecialchars($turma['turma_nome']) ?>
                                </h5>
                                <div class="d-flex flex-column align-items-end">
                                    <span class="status-badge bg-<?= $turma['card_class'] ?> text-white">
                                        <?= is_numeric($turma['disponiveis']) ? $turma['disponiveis'] . ' vagas' : $turma['disponiveis'] ?>
                                    </span>
                                    <?php if ($turma['hoje']): ?>
                                        <span class="hoje-badge mt-1">
                                            <i class="bi bi-calendar-check"></i> Hoje
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Informações da Turma -->
                            <div class="row g-2 mb-3 text-sm">
                                <div class="col-6">
                                    <strong class="text-muted">Instrutor:</strong><br>
                                    <span class="text-truncate"><?= htmlspecialchars($turma['instrutor']) ?></span>
                                </div>
                                <div class="col-6">
                                    <strong class="text-muted">Dia/Período:</strong><br>
                                    <span><?= htmlspecialchars($turma['dia']) ?> - <?= htmlspecialchars($turma['periodo']) ?></span>
                                </div>
                                <div class="col-6">
                                    <strong class="text-muted">Tipo:</strong><br>
                                    <span><?= htmlspecialchars($turma['tipo']) ?></span>
                                </div>
                                <div class="col-6">
                                    <strong class="text-muted">Ocupação:</strong><br>
                                    <span>
                                        <?= $turma['total_alunos'] ?>/<?= 
                                            (int)$turma['vagas'] > 0 ? $turma['vagas'] : '∞' 
                                        ?> alunos
                                    </span>
                                </div>
                            </div>

                            <!-- Botão de Cadastro -->
                            <div class="mt-auto">
                                <?php 
                                    $podeMatricular = ($turma['status_vagas'] !== 'lotada');
                                    $textoBotao = $podeMatricular ? 'Cadastrar Aluno' : 'Turma Lotada';
                                    $classeBotao = $podeMatricular ? 'success' : 'secondary';
                                ?>
                                <a href="precadastro.php?turma_id=<?= urlencode($turma['id']) ?>"
                                   class="btn btn-<?= $classeBotao ?> btn-lg w-100"
                                   <?= !$podeMatricular ? 'onclick="return false;" style="cursor: not-allowed;"' : '' ?>>
                                    <i class="bi bi-person-plus me-2"></i>
                                    <?= $textoBotao ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginação -->
        <nav aria-label="Paginação de turmas" class="mt-4">
            <ul class="pagination justify-content-center" id="paginacao"></ul>
        </nav>
    </main>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Mapa de Turmas carregado!');
            
            // Configurar filtros
            const filtros = {
                dia: document.getElementById('filtro-dia'),
                instrutor: document.getElementById('filtro-instrutor'),
                tipo: document.getElementById('filtro-tipo'),
                vagas: document.getElementById('filtro-vagas')
            };
            
            const form = document.getElementById('filtro-form');
            const btnLimpar = document.getElementById('btn-limpar-filtros');
            const btnLimparInline = document.getElementById('btn-limpar-inline');
            
            // Função para submeter o formulário
            function submitForm() {
                console.log('🔍 Aplicando filtros...');
                form.submit();
            }
            
            // Event listeners para os filtros
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    filtros[key].addEventListener('change', function() {
                        if (this.value) {
                            this.classList.add('filtro-ativo');
                        } else {
                            this.classList.remove('filtro-ativo');
                        }
                        setTimeout(submitForm, 100);
                    });
                }
            });
            
            // Função para limpar filtros
            function limparFiltros() {
                const urlSemParams = window.location.pathname;
                window.location.href = urlSemParams;
            }
            
            if (btnLimpar) {
                btnLimpar.addEventListener('click', limparFiltros);
            }
            
            if (btnLimparInline) {
                btnLimparInline.addEventListener('click', limparFiltros);
            }
            
            // Configurar paginação
            setupPagination();
        });

        // ==================== PAGINAÇÃO ====================
        function setupPagination() {
            const itemsPerPage = <?= ITEMS_PER_PAGE ?>;
            const cards = document.querySelectorAll('.card-entry');
            const pagination = document.getElementById('paginacao');
            
            if (!pagination || cards.length === 0) return;
            
            const totalPages = Math.ceil(cards.length / itemsPerPage);
            let currentPage = 1;
            
            function showPage(page) {
                cards.forEach((card, index) => {
                    const startIndex = (page - 1) * itemsPerPage;
                    const endIndex = startIndex + itemsPerPage;
                    card.style.display = (index >= startIndex && index < endIndex) ? 'block' : 'none';
                });
            }
            
            function updatePagination() {
                pagination.innerHTML = '';
                
                if (totalPages <= 1) {
                    pagination.style.display = 'none';
                    return;
                }
                
                pagination.style.display = 'flex';
                
                // Botão anterior
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                const prevButton = document.createElement('button');
                prevButton.className = 'page-link';
                prevButton.innerHTML = '<i class="bi bi-chevron-left"></i>';
                prevButton.addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        showPage(currentPage);
                        updatePagination();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
                prevLi.appendChild(prevButton);
                pagination.appendChild(prevLi);
                
                // Páginas numeradas
                for (let i = 1; i <= totalPages; i++) {
                    const li = document.createElement('li');
                    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    
                    const button = document.createElement('button');
                    button.className = 'page-link';
                    button.textContent = i;
                    button.addEventListener('click', () => {
                        currentPage = i;
                        showPage(currentPage);
                        updatePagination();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                    
                    li.appendChild(button);
                    pagination.appendChild(li);
                }
                
                // Botão próximo
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                const nextButton = document.createElement('button');
                nextButton.className = 'page-link';
                nextButton.innerHTML = '<i class="bi bi-chevron-right"></i>';
                nextButton.addEventListener('click', () => {
                    if (currentPage < totalPages) {
                        currentPage++;
                        showPage(currentPage);
                        updatePagination();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
                nextLi.appendChild(nextButton);
                pagination.appendChild(nextLi);
            }
            
            showPage(currentPage);
            updatePagination();
        }
    </script>
    
</body>
</html>