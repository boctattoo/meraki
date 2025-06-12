<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require 'conexao.php';

// Configuração apenas para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Obter dados de entrada
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input) || empty($input)) {
    $input = $_POST; // fallback para formulários tradicionais
}

$response = ['success' => false, 'message' => ''];

// Validação básica
$campos_obrigatorios = ['id', 'nome', 'responsavel'];
foreach ($campos_obrigatorios as $campo) {
    if (empty($input[$campo])) {
        $response['message'] = "Campo obrigatório ausente: $campo";
        echo json_encode($response);
        exit;
    }
}

$id = (int) $input['id'];

// Verificar se aluno existe
$stmt = $pdo->prepare("SELECT * FROM alunos WHERE id = ?");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    $response['message'] = 'Aluno não encontrado.';
    echo json_encode($response);
    exit;
}

// Determinar status e data_status
$novo_status = $input['status'] ?? 'Ativo';
$data_status = ($novo_status !== $aluno['status']) ? date('Y-m-d') : $aluno['data_status'];

try {
    $pdo->beginTransaction();
    
    // Atualizar dados do aluno (campos que existem na estrutura real)
    $stmt = $pdo->prepare("
        UPDATE alunos SET 
            nome=?, 
            telefone=?, 
            responsavel=?, 
            telefone_responsavel=?, 
            data_nascimento=?, 
            email=?, 
            curso_id=?,
            status=?, 
            data_status=? 
        WHERE id=?
    ");
    
    $ok = $stmt->execute([
        $input['nome'],
        $input['telefone'] ?? null,
        $input['responsavel'],
        $input['telefone_responsavel'] ?? null,
        $input['data_nascimento'] ?? null,  
        $input['email'] ?? null,
        !empty($input['curso_id']) ? $input['curso_id'] : null,
        $novo_status,
        $data_status,
        $id
    ]);
    
    if ($ok) {
        // Atualizar turmas do aluno
        $pdo->prepare("DELETE FROM alunos_turmas WHERE aluno_id=?")->execute([$id]);
        
        if ($novo_status === 'Ativo' && isset($input['turmas_id']) && is_array($input['turmas_id'])) {
            $stmt_turma = $pdo->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, data_atribuicao, ativo) VALUES (?, ?, CURDATE(), 1)");
            
            foreach ($input['turmas_id'] as $turma_id) {
                if (is_numeric($turma_id) && $turma_id > 0) {
                    $stmt_turma->execute([$id, $turma_id]);
                }
            }
        }
        
        // Log da operação
        $stmt_log = $pdo->prepare("
            INSERT INTO logs_sistema (usuario_id, acao, descricao, tabela_afetada, registro_id, ip) 
            VALUES (?, 'UPDATE', ?, 'alunos', ?, ?)
        ");
        $stmt_log->execute([
            $_SESSION['usuario_id'],
            "Aluno {$input['nome']} atualizado",
            $id,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
        
        $pdo->commit();
        $response['success'] = true;
        $response['message'] = 'Dados atualizados com sucesso!';
        
    } else {
        $pdo->rollback();
        $response['message'] = 'Erro ao salvar os dados.';
    }
    
} catch (PDOException $e) {
    $pdo->rollback();
    
    // Log do erro
    error_log("Erro ao atualizar aluno ID $id: " . $e->getMessage());
    
    $response['message'] = 'Erro no banco de dados. Tente novamente.';
}

echo json_encode($response);
?>