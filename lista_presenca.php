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

// =================== CONFIGURAÇÕES ===================
$dias_pt = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
$meses_pt = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// =================== PARÂMETROS ===================
$mes_atual = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$ano_atual = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');
$turma_id = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : null;
$pagina_aula = isset($_GET['pagina_aula']) ? (int)$_GET['pagina_aula'] : 1; // Nova: página da aula
$modo_impressao = isset($_GET['modo_impressao']) ? $_GET['modo_impressao'] : 'individual'; // individual, todas
$aulas_mes_config = isset($_GET['aulas_mes']) ? (int)$_GET['aulas_mes'] : 8; // Configurável: aulas esperadas no mês

// =================== FUNÇÕES AUXILIARES ===================
function obterFeriados($pdo, $ano, $mes) {
    $stmt = $pdo->prepare("SELECT data, descricao FROM feriados WHERE YEAR(data) = ? AND MONTH(data) = ?");
    $stmt->execute([$ano, $mes]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obterModulosPorCurso($pdo, $turma_id) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT m.id, m.nome, m.carga_horaria, c.nome as curso_nome
        FROM modulos m 
        INNER JOIN cursos c ON m.curso_id = c.id
        INNER JOIN contratos_cursos cc ON c.id = cc.curso_id
        INNER JOIN contratos ct ON cc.contrato_id = ct.id
        INNER JOIN alunos_turmas at ON ct.aluno_id = at.aluno_id
        WHERE at.turma_id = ? AND at.ativo = 1
        ORDER BY m.ordem, m.nome
    ");
    $stmt->execute([$turma_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function gerarDatasPeriodo($pdo, $turma_info, $ano, $mes, $dias_pt) {
    $datas_colunas = [];
    
    if ($turma_info && $turma_info['dia_semana']) {
        $dia_semana_db = ucfirst(mb_strtolower($turma_info['dia_semana'], 'UTF-8'));
        $index_dia = array_search($dia_semana_db, $dias_pt);
        
        if ($index_dia !== false) {
            $primeiro_dia = new DateTime("$ano-$mes-01");
            $ultimo_dia = new DateTime("$ano-$mes-" . $primeiro_dia->format('t'));
            
            $data_atual = clone $primeiro_dia;
            $numero_aula = 1;
            
            // Busca feriados do mês
            $feriados = obterFeriados($pdo, $ano, $mes);
            
            while ($data_atual <= $ultimo_dia) {
                if ((int)$data_atual->format('w') === $index_dia) {
                    $data_str = $data_atual->format('Y-m-d');
                    $eh_feriado = array_filter($feriados, function($f) use ($data_str) {
                        return $f['data'] === $data_str;
                    });
                    
                    $datas_colunas[] = [
                        'data' => $data_str,
                        'data_br' => $data_atual->format('d/m'),
                        'data_completa' => $data_atual->format('d/m/Y'),
                        'data_extenso' => $dias_pt[(int)$data_atual->format('w')] . ', ' . $data_atual->format('d') . ' de ' . 
                                         $GLOBALS['meses_pt'][(int)$data_atual->format('m')] . ' de ' . $data_atual->format('Y'),
                        'eh_feriado' => !empty($eh_feriado),
                        'feriado_desc' => !empty($eh_feriado) ? array_values($eh_feriado)[0]['descricao'] : null,
                        'numero_aula' => $eh_feriado ? null : $numero_aula
                    ];
                    
                    if (!$eh_feriado) $numero_aula++;
                }
                $data_atual->modify('+1 day');
            }
        }
    }
    
    return $datas_colunas;
}

// =================== CONSULTAS PRINCIPAIS ===================
try {
    // Busca turmas
    $stmt_turmas = $pdo->prepare("
        SELECT t.id, t.nome, d.nome AS dia_semana, p.nome AS periodo, i.nome AS instrutor,
               COUNT(at.aluno_id) as total_alunos, t.vagas_total
        FROM turmas t 
        LEFT JOIN dias_semana d ON t.dia_semana_id = d.id 
        LEFT JOIN periodos p ON t.periodo_id = p.id
        LEFT JOIN instrutores i ON t.instrutor_id = i.id
        LEFT JOIN alunos_turmas at ON t.id = at.turma_id AND at.ativo = 1
        WHERE t.status = 'ativa'
        GROUP BY t.id, t.nome, d.nome, p.nome, i.nome, t.vagas_total
        ORDER BY t.nome
    ");
    $stmt_turmas->execute();
    $turmas = $stmt_turmas->fetchAll(PDO::FETCH_ASSOC);

    $alunos = [];
    $datas_colunas = [];
    $aula_atual = null;
    $turma_info = null;
    $modulos_disponiveis = [];
    $presencas_dados = [];

    if ($turma_id) {
        // Busca informações da turma
        $stmt_turma_info = $pdo->prepare("
            SELECT t.nome, d.nome AS dia_semana, p.nome AS periodo, i.nome AS instrutor,
                   t.vagas_total, tt.nome as tipo_turma, tt.cor as cor_tipo
            FROM turmas t 
            LEFT JOIN dias_semana d ON t.dia_semana_id = d.id 
            LEFT JOIN periodos p ON t.periodo_id = p.id
            LEFT JOIN instrutores i ON t.instrutor_id = i.id
            LEFT JOIN tipos_turma tt ON t.tipo_id = tt.id
            WHERE t.id = ?
        ");
        $stmt_turma_info->execute([$turma_id]);
        $turma_info = $stmt_turma_info->fetch(PDO::FETCH_ASSOC);

        // Busca alunos da turma
        $stmt_alunos = $pdo->prepare("
            SELECT a.id, a.nome, a.telefone, a.data_nascimento,
                   GROUP_CONCAT(c.nome SEPARATOR ', ') as cursos
            FROM alunos a 
            INNER JOIN alunos_turmas at ON a.id = at.aluno_id 
            LEFT JOIN contratos ct ON a.id = ct.aluno_id AND ct.consolidado = 1
            LEFT JOIN contratos_cursos cc ON ct.id = cc.contrato_id
            LEFT JOIN cursos c ON cc.curso_id = c.id
            WHERE at.turma_id = ? AND at.ativo = 1 AND a.status IN ('Ativo', 'ativo')
            GROUP BY a.id, a.nome, a.telefone, a.data_nascimento
            ORDER BY a.nome
        ");
        $stmt_alunos->execute([$turma_id]);
        $alunos = $stmt_alunos->fetchAll(PDO::FETCH_ASSOC);

        // Gera todas as datas do período
        $datas_colunas = gerarDatasPeriodo($pdo, $turma_info, $ano_atual, $mes_atual, $dias_pt);
        
        // Filtra apenas aulas (não feriados) para paginação
        $aulas_disponiveis = array_filter($datas_colunas, function($d) { return !$d['eh_feriado']; });
        $aulas_disponiveis = array_values($aulas_disponiveis); // Reindexar array
        
        // Define a aula atual baseada na página
        if (!empty($aulas_disponiveis)) {
            $total_aulas = count($aulas_disponiveis);
            $pagina_aula = max(1, min($pagina_aula, $total_aulas));
            $aula_atual = $aulas_disponiveis[$pagina_aula - 1] ?? null;
        }

        // Busca módulos disponíveis
        $modulos_disponiveis = obterModulosPorCurso($pdo, $turma_id);

        // Busca presenças da aula atual se existir
        if ($aula_atual && $alunos) {
            $alunos_ids = array_column($alunos, 'id');
            
            if (!empty($alunos_ids)) {
                $placeholders_alunos = str_repeat('?,', count($alunos_ids) - 1) . '?';
                
                $stmt_presencas = $pdo->prepare("
                    SELECT aluno_id, data, presente 
                    FROM presencas 
                    WHERE turma_id = ? AND data = ? AND aluno_id IN ($placeholders_alunos)
                ");
                $stmt_presencas->execute(array_merge([$turma_id, $aula_atual['data']], $alunos_ids));
                
                foreach ($stmt_presencas->fetchAll(PDO::FETCH_ASSOC) as $presenca) {
                    $presencas_dados[$presenca['aluno_id']] = $presenca['presente'];
                }
            }
        }

        // Configuração do total de aulas esperadas no mês
        $total_aulas_esperadas_mes = $aulas_mes_config; // Usa configuração ou padrão 8
    }
} catch (Exception $e) {
    $erro_consulta = "Erro na consulta: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Presença por Aula - Sistema Meraki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .container-fluid { max-width: 1400px; }

        /* CARDS */
        .card-aula {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card-aula:hover {
            transform: translateY(-2px);
        }

        .header-aula {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .header-feriado {
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
        }

        /* TABELA */
        .table-aula {
            font-size: 14px;
            margin: 0;
        }

        .table-aula th {
            background: #495057;
            color: white;
            text-align: center;
            vertical-align: middle;
            padding: 12px 8px;
            border: 1px solid #6c757d;
            font-weight: 600;
        }

        .table-aula td {
            padding: 10px 8px;
            border: 1px solid #dee2e6;
            text-align: center;
            vertical-align: middle;
            min-height: 45px;
        }

        .nome-aluno {
            width: 200px;
            min-width: 200px;
            background-color: #f8f9fa;
            text-align: left !important;
            padding: 12px 10px !important;
            font-weight: 600;
            border-right: 3px solid #6c757d;
        }

        .campo-presenca { width: 100px; background: #fff3cd; }
        .campo-questionario { width: 120px; background: #d1ecf1; }
        .campo-apostila { width: 100px; background: #d4edda; }
        .campo-modulo { width: 120px; background: #f8d7da; }
        .campo-aula-modulo { width: 100px; background: #ffeaa7; }
        .campo-aulas-concluidas { width: 120px; background: #dda0dd; position: relative; }
        .campo-observacoes { width: 180px; background: #e2e3e5; }

        /* Linha para preenchimento manual */
        .linha-preenchimento {
            display: inline-block;
            border-bottom: 2px solid #333;
            width: 30px;
            height: 20px;
            margin-right: 5px;
        }

        /* NAVEGAÇÃO */
        .navegacao-aulas {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .btn-navegacao {
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-navegacao:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .indicador-aula {
            display: inline-block;
            padding: 4px 8px;
            background: var(--primary-color);
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .indicador-feriado {
            background: var(--warning-color);
            color: #000;
        }

        /* IMPRESSÃO */
        @page {
            size: A4 portrait;
            margin: 1cm;
        }

        @media print {
            body {
                background: white !important;
                color: black !important;
                font-size: 12px !important;
            }

            .no-print { display: none !important; }

            .container-fluid {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
            }

            .card-aula {
                box-shadow: none !important;
                border: 2px solid #000 !important;
            }

            .header-aula {
                background: #f0f0f0 !important;
                color: black !important;
                border-bottom: 2px solid #000 !important;
            }

            .table-aula {
                font-size: 11px !important;
            }

            .table-aula th,
            .table-aula td {
                border: 1px solid #000 !important;
                padding: 8px 4px !important;
            }

            .nome-aluno {
                width: 180px !important;
                font-size: 10px !important;
            }

            .campo-presenca { width: 80px !important; }
            .campo-questionario { width: 100px !important; }
            .campo-apostila { width: 80px !important; }
            .campo-modulo { width: 100px !important; }
            .campo-aula-modulo { width: 80px !important; }
            .campo-aulas-concluidas { width: 100px !important; }
            .campo-observacoes { width: 120px !important; }

            /* Linha para preenchimento na impressão */
            .linha-preenchimento {
                border-bottom: 2px solid #000 !important;
                width: 25px !important;
                height: 15px !important;
                display: inline-block !important;
            }
        }

        /* ANIMAÇÕES */
        .fade-in { 
            animation: fadeIn 0.6s ease-in; 
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-presente { color: var(--success-color); font-weight: bold; }
        .status-ausente { color: var(--danger-color); font-weight: bold; }

        .info-turma {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .estatisticas {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-light">
    <?php include 'nav.php'; ?>
    
    <div class="container-fluid mt-4 fade-in">
        
        <?php if ($modo_impressao === 'todas' && $turma_id && !empty($aulas_disponiveis)): ?>
        <!-- =================== MODO IMPRESSÃO TODAS AS AULAS =================== -->
        <style>
            body { background: white !important; }
            .no-print { display: none !important; }
            .card-aula { margin-bottom: 30px; page-break-inside: avoid; }
            .header-aula { background: #f0f0f0 !important; color: black !important; border: 2px solid #000; }
        </style>
        
        <div class="text-center mb-4">
            <h3>MICROLINS BAURU - LISTA COMPLETA DE PRESENÇA</h3>
            <h4>Turma: <?= htmlspecialchars($turma_info['nome']) ?> | Período: <?= $meses_pt[$mes_atual] ?>/<?= $ano_atual ?></h4>
            <p>Instrutor: <?= htmlspecialchars($turma_info['instrutor'] ?? 'Não definido') ?> | Data: <?= date('d/m/Y H:i') ?></p>
        </div>

        <?php foreach ($aulas_disponiveis as $index => $aula_info): ?>
            <?php 
            // Busca presenças desta aula específica
            $presencas_aula = [];
            if ($alunos) {
                $alunos_ids = array_column($alunos, 'id');
                if (!empty($alunos_ids)) {
                    $placeholders_alunos = str_repeat('?,', count($alunos_ids) - 1) . '?';
                    $stmt_presencas_aula = $pdo->prepare("
                        SELECT aluno_id, presente 
                        FROM presencas 
                        WHERE turma_id = ? AND data = ? AND aluno_id IN ($placeholders_alunos)
                    ");
                    $stmt_presencas_aula->execute(array_merge([$turma_id, $aula_info['data']], $alunos_ids));
                    foreach ($stmt_presencas_aula->fetchAll(PDO::FETCH_ASSOC) as $presenca) {
                        $presencas_aula[$presenca['aluno_id']] = $presenca['presente'];
                    }
                }
            }
            ?>
            
            <div class="card card-aula">
                <div class="header-aula">
                    <h5 class="mb-0">Aula <?= $aula_info['numero_aula'] ?> - <?= $aula_info['data_extenso'] ?></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-aula">
                            <thead>
                                <tr>
                                    <th class="nome-aluno">Nome do Aluno</th>
                                    <th class="campo-presenca">Presença</th>
                                    <th class="campo-questionario">Quest.</th>
                                    <th class="campo-apostila">Apost.</th>
                                    <th class="campo-modulo">Módulo</th>
                                    <th class="campo-aula-modulo">Aula Mód.</th>
                                    <th class="campo-aulas-concluidas">Concl.</th>
                                    <th class="campo-observacoes">Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alunos as $aluno): ?>
                                    <tr>
                                        <td class="nome-aluno">
                                            <strong><?= htmlspecialchars($aluno['nome']) ?></strong>
                                        </td>
                                        <td class="campo-presenca">
                                            <?php 
                                            $presente = $presencas_aula[$aluno['id']] ?? null;
                                            if ($presente === 1) {
                                                echo '<strong style="color: green;">✓</strong>';
                                            } elseif ($presente === 0) {
                                                echo '<strong style="color: red;">✗</strong>';
                                            } else {
                                                echo '';
                                            }
                                            ?>
                                        </td>
                                        <td class="campo-questionario"></td>
                                        <td class="campo-apostila"></td>
                                        <td class="campo-modulo"></td>
                                        <td class="campo-aula-modulo"></td>
                                        <td class="campo-aulas-concluidas">
                                            <strong><span class="linha-preenchimento"></span> / <?= $total_aulas_esperadas_mes ?></strong>
                                        </td>
                                        <td class="campo-observacoes"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php if ($index < count($aulas_disponiveis) - 1): ?>
                <div style="page-break-after: always;"></div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <script>
            window.addEventListener('load', function() {
                setTimeout(() => window.print(), 500);
            });
        </script>
        
        <?php else: ?>
        <!-- =================== MODO NORMAL =================== -->
        <!-- =================== CONTROLES =================== -->
        <div class="row no-print">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Lista de Presença por Aula</h5>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-light btn-sm" onclick="window.print()">
                                    <i class="fas fa-print"></i> Imprimir Aula
                                </button>
                                <?php if (!empty($aulas_disponiveis)): ?>
                                <button class="btn btn-outline-light btn-sm" onclick="imprimirTodas()">
                                    <i class="fas fa-print"></i> Imprimir Todas
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="turma_id" class="form-label">Turma:</label>
                                <select name="turma_id" id="turma_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Selecione uma turma --</option>
                                    <?php foreach ($turmas as $turma): ?>
                                        <option value="<?= $turma['id'] ?>" <?= ($turma_id == $turma['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($turma['nome']) ?> 
                                            (<?= $turma['total_alunos'] ?>/<?= $turma['vagas_total'] ?? '∞' ?> alunos)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="mes" class="form-label">Mês:</label>
                                <select name="mes" id="mes" class="form-select" onchange="this.form.submit()">
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= $m == $mes_atual ? 'selected' : '' ?>>
                                            <?= $meses_pt[$m] ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="ano" class="form-label">Ano:</label>
                                <input type="number" name="ano" id="ano" value="<?= $ano_atual ?>" 
                                       class="form-control" min="2020" max="2030" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-2">
                                <label for="aulas_mes" class="form-label">Aulas/Mês:</label>
                                <input type="number" name="aulas_mes" id="aulas_mes" value="<?= $aulas_mes_config ?>" 
                                       class="form-control" min="4" max="20" title="Número de aulas esperadas no mês">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($turma_id && $turma_info): ?>
        
        <!-- =================== INFO DA TURMA =================== -->
        <div class="info-turma no-print">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <strong>Turma:</strong><br>
                    <?= htmlspecialchars($turma_info['nome']) ?>
                </div>
                <div class="col-md-2">
                    <strong>Instrutor:</strong><br>
                    <?= htmlspecialchars($turma_info['instrutor'] ?? 'Não definido') ?>
                </div>
                <div class="col-md-2">
                    <strong>Período:</strong><br>
                    <?= htmlspecialchars($turma_info['periodo'] ?? 'Não definido') ?>
                </div>
                <div class="col-md-2">
                    <strong>Dia da Semana:</strong><br>
                    <?= htmlspecialchars($turma_info['dia_semana'] ?? 'Não definido') ?>
                </div>
                <div class="col-md-2">
                    <strong>Alunos:</strong><br>
                    <?= count($alunos) ?>/<?= $turma_info['vagas_total'] ?? '∞' ?>
                </div>
                <div class="col-md-2">
                    <strong>Aulas no Período:</strong><br>
                    <?= count($aulas_disponiveis ?? []) ?> / <?= $total_aulas_esperadas_mes ?> aulas
                </div>
            </div>
        </div>

        <!-- =================== NAVEGAÇÃO ENTRE AULAS =================== -->
        <?php if (!empty($aulas_disponiveis)): ?>
        <div class="navegacao-aulas no-print">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <?php if ($pagina_aula > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['pagina_aula' => $pagina_aula - 1])) ?>" 
                               class="btn btn-outline-primary btn-navegacao">
                                <i class="fas fa-chevron-left"></i> Anterior
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($pagina_aula < count($aulas_disponiveis)): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['pagina_aula' => $pagina_aula + 1])) ?>" 
                               class="btn btn-outline-primary btn-navegacao">
                                Próxima <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6 text-center">
                    <h6 class="mb-0">
                        Navegação de Aulas
                        <span class="indicador-aula">
                            <?= $pagina_aula ?> de <?= count($aulas_disponiveis) ?>
                        </span>
                    </h6>
                </div>
                
                <div class="col-md-3 text-end">
                    <select onchange="location.href='?<?= http_build_query(array_diff_key($_GET, ['pagina_aula' => ''])) ?>&pagina_aula=' + this.value" 
                            class="form-select d-inline-block w-auto">
                        <?php foreach ($aulas_disponiveis as $i => $aula_nav): ?>
                            <option value="<?= $i + 1 ?>" <?= ($i + 1) == $pagina_aula ? 'selected' : '' ?>>
                                Aula <?= $aula_nav['numero_aula'] ?> - <?= $aula_nav['data_completa'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- =================== AULA ATUAL =================== -->
        <?php if ($aula_atual): ?>
        <div class="row">
            <div class="col-12">
                <div class="card card-aula">
                    <div class="header-aula <?= $aula_atual['eh_feriado'] ? 'header-feriado' : '' ?>">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">
                                    <?php if ($aula_atual['eh_feriado']): ?>
                                        <i class="fas fa-calendar-times me-2"></i>FERIADO
                                    <?php else: ?>
                                        <i class="fas fa-calendar-check me-2"></i>Aula <?= $aula_atual['numero_aula'] ?>
                                    <?php endif; ?>
                                </h4>
                                <h5 class="mb-0"><?= $aula_atual['data_extenso'] ?></h5>
                                <?php if ($aula_atual['eh_feriado']): ?>
                                    <p class="mb-0 mt-2"><i class="fas fa-info-circle me-1"></i><?= htmlspecialchars($aula_atual['feriado_desc']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-end">
                                <?php if (!$aula_atual['eh_feriado']): ?>
                                <div class="estatisticas">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <small>Presentes</small><br>
                                            <strong class="status-presente"><?= count(array_filter($presencas_dados, function($p) { return $p == 1; })) ?></strong>
                                        </div>
                                        <div class="col-6">
                                            <small>Ausentes</small><br>
                                            <strong class="status-ausente"><?= count(array_filter($presencas_dados, function($p) { return $p == 0; })) ?></strong>
                                        </div>
                                    </div>
                                    <div class="row text-center mt-2">
                                        <div class="col-12">
                                            <small>Meta Mensal</small><br>
                                            <strong style="color: #6f42c1;"><span class="linha-preenchimento"></span> / <?= $total_aulas_esperadas_mes ?></strong>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!$aula_atual['eh_feriado']): ?>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-aula">
                                <thead>
                                    <tr>
                                        <th class="nome-aluno">Nome do Aluno</th>
                                        <th class="campo-presenca">
                                            <i class="fas fa-user-check"></i><br>
                                            Presença
                                        </th>
                                        <th class="campo-questionario">
                                            <i class="fas fa-question-circle"></i><br>
                                            Questionário<br>
                                            <small>(0-10)</small>
                                        </th>
                                        <th class="campo-apostila">
                                            <i class="fas fa-book"></i><br>
                                            Apostila<br>
                                            <small>(S/N)</small>
                                        </th>
                                        <th class="campo-modulo">
                                            <i class="fas fa-puzzle-piece"></i><br>
                                            Módulo<br>
                                            <small>Estudado</small>
                                        </th>
                                        <th class="campo-aula-modulo">
                                            <i class="fas fa-list-ol"></i><br>
                                            Aula do<br>
                                            Módulo
                                        </th>
                                        <th class="campo-aulas-concluidas">
                                            <i class="fas fa-calendar-check"></i><br>
                                            Aulas Concluídas<br>
                                            <small>no Mês</small>
                                        </th>
                                        <th class="campo-observacoes">
                                            <i class="fas fa-sticky-note"></i><br>
                                            Observações
                                        </th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <?php foreach ($alunos as $aluno): ?>
                                        <tr>
                                            <td class="nome-aluno">
                                                <strong><?= htmlspecialchars($aluno['nome']) ?></strong>
                                                <?php if ($aluno['cursos']): ?>
                                                    <br><small style="color: #666;"><?= htmlspecialchars($aluno['cursos']) ?></small>
                                                <?php endif; ?>
                                                <br><small style="color: #999;">ID: <?= $aluno['id'] ?></small>
                                            </td>
                                            
                                            <td class="campo-presenca">
                                                <?php 
                                                $presente = $presencas_dados[$aluno['id']] ?? null;
                                                if ($presente === 1) {
                                                    echo '<i class="fas fa-check-circle status-presente"></i><br><strong>PRESENTE</strong>';
                                                } elseif ($presente === 0) {
                                                    echo '<i class="fas fa-times-circle status-ausente"></i><br><strong>AUSENTE</strong>';
                                                } else {
                                                    echo '<div style="height: 40px; border: 1px dashed #ccc; background: #f9f9f9;"></div>';
                                                }
                                                ?>
                                            </td>
                                            
                                            <td class="campo-questionario">
                                                <div style="height: 40px; border: 1px dashed #ccc; background: #f9f9f9; display: flex; align-items: center; justify-content: center;">
                                                    <small style="color: #999;">0-10</small>
                                                </div>
                                            </td>
                                            
                                            <td class="campo-apostila">
                                                <div style="height: 40px; border: 1px dashed #ccc; background: #f9f9f9; display: flex; align-items: center; justify-content: center;">
                                                    <small style="color: #999;">S / N</small>
                                                </div>
                                            </td>
                                            
                                            <td class="campo-modulo">
                                                <div style="height: 40px; border: 1px dashed #ccc; background: #f9f9f9; display: flex; align-items: center; justify-content: center;">
                                                    <small style="color: #999;">Módulo</small>
                                                </div>
                                            </td>
                                            
                                            <td class="campo-aula-modulo">
                                                <div style="height: 40px; border: 1px dashed #ccc; background: #f9f9f9; display: flex; align-items: center; justify-content: center;">
                                                    <small style="color: #999;">Nº Aula</small>
                                                </div>
                                            </td>
                                            
                                            <td class="campo-aulas-concluidas">
                                                <div style="height: 40px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #333;">
                                                    <span class="linha-preenchimento"></span> / <?= $total_aulas_esperadas_mes ?>
                                                </div>
                                            </td>
                                            
                                            <td class="campo-observacoes">
                                                <div style="height: 40px; border: 1px dashed #ccc; background: #f9f9f9;"></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- FERIADO -->
                    <div class="card-body text-center">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <h5>Não há aula nesta data</h5>
                            <p class="mb-0">Esta data corresponde a um feriado: <strong><?= htmlspecialchars($aula_atual['feriado_desc']) ?></strong></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- =================== MÓDULOS DISPONÍVEIS =================== -->
        <?php if ($modulos_disponiveis && !$aula_atual['eh_feriado']): ?>
        <div class="row mt-3 no-print">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-book"></i> Módulos Disponíveis para esta Turma</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($modulos_disponiveis as $modulo): ?>
                                <div class="col-md-3 mb-2">
                                    <span class="badge bg-light text-dark border p-2">
                                        <?= htmlspecialchars($modulo['nome']) ?>
                                        <small>(<?= $modulo['carga_horaria'] ?>h)</small>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- =================== ASSINATURAS (PARA IMPRESSÃO) =================== -->
        <div class="row mt-4 d-none d-print-block">
            <div class="col-12">
                <div style="border-top: 2px solid #000; padding-top: 20px; margin-top: 40px;">
                    <div class="row">
                        <div class="col-6 text-center">
                            <div style="border-bottom: 1px solid #000; margin-bottom: 5px; height: 50px;"></div>
                            <strong>Instrutor: <?= htmlspecialchars($turma_info['instrutor'] ?? 'Nome do Instrutor') ?></strong>
                        </div>
                        <div class="col-6 text-center">
                            <div style="border-bottom: 1px solid #000; margin-bottom: 5px; height: 50px;"></div>
                            <strong>Coordenação Pedagógica</strong>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small>Sistema Meraki - Microlins Bauru | Data: <?= date('d/m/Y H:i') ?></small>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- =================== ESTADO VAZIO =================== -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-calendar-check fa-3x mb-3 text-muted"></i>
                    <h4>Selecione uma turma para visualizar as aulas</h4>
                    <p class="mb-0">Use os filtros acima para escolher a turma e o período desejado.</p>
                    <hr>
                    <p class="small text-muted mb-0">
                        <strong>Nova funcionalidade:</strong> Agora cada aula é exibida em uma página separada, 
                        facilitando o preenchimento e a impressão individual.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <?php endif; ?>

    </div>

    <!-- =================== SCRIPTS =================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Atalhos de teclado
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey) {
                    switch(e.key) {
                        case 'p':
                            e.preventDefault();
                            window.print();
                            break;
                        case 'ArrowLeft':
                            e.preventDefault();
                            const btnAnterior = document.querySelector('a[href*="pagina_aula=' + (<?= $pagina_aula ?> - 1) + '"]');
                            if (btnAnterior) btnAnterior.click();
                            break;
                        case 'ArrowRight':
                            e.preventDefault();
                            const btnProxima = document.querySelector('a[href*="pagina_aula=' + (<?= $pagina_aula ?> + 1) + '"]');
                            if (btnProxima) btnProxima.click();
                            break;
                    }
                }
            });

            // Auto-save de formulários (implementação futura)
            const campos = document.querySelectorAll('input, select, textarea');
            campos.forEach(campo => {
                campo.addEventListener('change', function() {
                    // Implementar auto-save aqui
                    console.log('Campo alterado:', this.name, this.value);
                });
            });

            // Feedback visual
            function mostrarFeedback(mensagem, tipo = 'info') {
                const toast = document.createElement('div');
                toast.className = `alert alert-${tipo} position-fixed top-0 end-0 m-3`;
                toast.style.zIndex = '9999';
                toast.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${mensagem}</span>
                        <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => toast.remove(), 5000);
            }

            // Detecta mudanças nos filtros
            const selects = document.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.name !== 'pagina_aula') {
                        mostrarFeedback('Carregando nova turma/período...', 'info');
                    }
                });
            });
        });

        // Função para imprimir todas as aulas
        function imprimirTodas() {
            if (confirm('Deseja abrir uma nova janela com todas as aulas para impressão?')) {
                const url = new URL(window.location);
                url.searchParams.set('modo_impressao', 'todas');
                const novaJanela = window.open(url.toString(), '_blank');
                
                // Aguarda carregar e imprime automaticamente
                novaJanela.addEventListener('load', function() {
                    setTimeout(() => {
                        novaJanela.print();
                    }, 1000);
                });
            }
        }
    </script>
</body>
</html>