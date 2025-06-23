<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Verificar autenticação
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Usuário não autenticado'
    ]);
    exit();
}

require_once 'conexao.php';

// Validar parâmetros obrigatórios
$turma_id = filter_input(INPUT_GET, 'turma_id', FILTER_VALIDATE_INT);
$data = filter_input(INPUT_GET, 'data', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!$turma_id || !$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Parâmetros inválidos. Turma ID e data são obrigatórios.'
    ]);
    exit();
}

// Validar formato da data
if (!DateTime::createFromFormat('Y-m-d', $data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Formato de data inválido. Use YYYY-MM-DD.'
    ]);
    exit();
}

try {
    // Verificar se a turma existe e está ativa
    $sqlTurma = "SELECT id, nome FROM turmas WHERE id = ? AND status = 'ativa'";
    $stmtTurma = $pdo->prepare($sqlTurma);
    $stmtTurma->execute([$turma_id]);
    
    if ($stmtTurma->rowCount() === 0) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Turma não encontrada ou inativa'
        ]);
        exit();
    }

    $turma = $stmtTurma->fetch(PDO::FETCH_ASSOC);

    // Buscar alunos da turma com suas presenças
    $sqlAlunos = "
        SELECT 
            a.id,
            a.nome,
            p.presente,
            CASE 
                WHEN p.presente IS NULL THEN 'nao_marcado'
                WHEN p.presente = 1 THEN 'presente'
                ELSE 'falta'
            END as status_presenca
        FROM alunos a
        INNER JOIN alunos_turmas at ON a.id = at.aluno_id 
        LEFT JOIN presencas p ON a.id = p.aluno_id 
            AND p.turma_id = ? 
            AND p.data = ?
        WHERE at.turma_id = ? 
            AND at.ativo = 1 
            AND a.status IN ('Ativo', 'ativo')
        ORDER BY a.nome ASC
    ";

    $stmtAlunos = $pdo->prepare($sqlAlunos);
    $stmtAlunos->execute([$turma_id, $data, $turma_id]);
    $alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);

    // Processar dados dos alunos
    $alunosProcessados = [];
    $estatisticas = [
        'total' => count($alunos),
        'presentes' => 0,
        'faltas' => 0,
        'nao_marcados' => 0
    ];

    foreach ($alunos as $aluno) {
        $alunoData = [
            'id' => (int)$aluno['id'],
            'nome' => trim($aluno['nome']),
            'presente' => $aluno['presente'] !== null ? (bool)$aluno['presente'] : null,
            'status' => $aluno['status_presenca']
        ];

        // Atualizar estatísticas
        switch ($aluno['status_presenca']) {
            case 'presente':
                $estatisticas['presentes']++;
                break;
            case 'falta':
                $estatisticas['faltas']++;
                break;
            case 'nao_marcado':
                $estatisticas['nao_marcados']++;
                break;
        }

        $alunosProcessados[] = $alunoData;
    }

    // Verificar se presença já foi registrada hoje
    $sqlPresencaRegistrada = "
        SELECT COUNT(DISTINCT aluno_id) as total_registros
        FROM presencas 
        WHERE turma_id = ? AND data = ?
    ";
    $stmtPresenca = $pdo->prepare($sqlPresencaRegistrada);
    $stmtPresenca->execute([$turma_id, $data]);
    $presencaRegistrada = $stmtPresenca->fetch(PDO::FETCH_ASSOC);

    $response = [
        'success' => true,
        'alunos' => $alunosProcessados,
        'turma' => [
            'id' => $turma_id,
            'nome' => $turma['nome']
        ],
        'estatisticas' => $estatisticas,
        'presenca_registrada' => (int)$presencaRegistrada['total_registros'] > 0,
        'data' => $data,
        'message' => count($alunosProcessados) > 0 ? 
            'Dados carregados com sucesso' : 
            'Nenhum aluno encontrado nesta turma'
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    error_log("Erro na consulta de presença: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro interno do servidor. Tente novamente.'
    ]);
} catch (Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro inesperado. Tente novamente.'
    ]);
}
?>