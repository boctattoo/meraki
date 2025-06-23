<?php
session_start();
header('Content-Type: application/json');
require 'conexao.php';

// --- Funções Auxiliares ---
function json_response($data) {
    echo json_encode($data);
    exit();
}

function json_error($message, $reason = null, $extra_data = []) {
    $response = ['success' => false, 'error' => $message];
    if ($reason) $response['reason'] = $reason;
    json_response(array_merge($response, $extra_data));
}


// --- Lógica Principal ---
$action = $_GET['action'] ?? '';

// --- AÇÕES PÚBLICAS (não precisam de login) ---
if ($action === 'login') {
    $identifier = trim($_POST['login_identifier'] ?? '');
    $senha_enviada = trim($_POST['senha'] ?? '');

    if (empty($identifier) || empty($senha_enviada)) {
        json_error('Preencha todos os campos.');
    }

    try {
        $telefone_numerico = preg_replace('/[^0-9]/', '', $identifier);
        
        $stmt = $pdo->prepare(
            "SELECT a.id, a.nome, a.senha, a.data_nascimento, a.cpf as cpf_aluno, c.cpf_cnpj_aluno as cpf_contrato
             FROM alunos a 
             LEFT JOIN contratos c ON a.id = c.aluno_id
             WHERE a.cpf = :identifier 
                OR a.telefone = :telefone 
                OR a.email = :identifier
                OR c.cpf_cnpj_aluno = :identifier
             LIMIT 1"
        );
        $stmt->execute([
            ':identifier' => $identifier, 
            ':telefone' => $telefone_numerico
        ]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$aluno) {
            json_error('Utilizador não encontrado.');
        }

        if (empty($aluno['senha'])) {
            $data_nasc_formatada = $aluno['data_nascimento'] ? date('dmY', strtotime($aluno['data_nascimento'])) : null;
            $cpf_numerico = preg_replace('/[^0-9]/', '', $aluno['cpf_aluno'] ?? $aluno['cpf_contrato'] ?? '');

            if ($senha_enviada === $data_nasc_formatada || (!empty($cpf_numerico) && $senha_enviada === $cpf_numerico)) {
                json_error('Primeiro acesso detetado.', 'primeiro_acesso', ['aluno_id' => $aluno['id']]);
            } else {
                json_error('Senha inicial incorreta.');
            }
        }
        
        if (password_verify($senha_enviada, $aluno['senha'])) {
            $_SESSION['aluno_id'] = $aluno['id'];
            $_SESSION['aluno_nome'] = $aluno['nome'];
            json_response(['success' => true]);
        } else {
            json_error('Senha incorreta.');
        }

    } catch (PDOException $e) {
        json_error('Erro no servidor. Tente novamente mais tarde.');
    }
}

if ($action === 'definir_senha') {
    $aluno_id = filter_input(INPUT_POST, 'aluno_id', FILTER_VALIDATE_INT);
    $nova_senha = $_POST['nova_senha'] ?? '';

    if (!$aluno_id || strlen($nova_senha) < 6) {
        json_error('Dados inválidos ou senha muito curta.');
    }
    
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE alunos SET senha = ? WHERE id = ?");
    if ($stmt->execute([$senha_hash, $aluno_id])) {
        json_response(['success' => true]);
    } else {
        json_error('Não foi possível atualizar a senha.');
    }
}


// --- AÇÕES PROTEGIDAS (requerem login) ---
if (!isset($_SESSION['aluno_id'])) {
    json_error('Acesso não autorizado.');
}

$aluno_id = $_SESSION['aluno_id'];

switch ($action) {
    case 'get_dashboard_data':
        try {
            $aluno = $pdo->prepare("SELECT nome, email, telefone, foto_perfil, pontos_fidelidade FROM alunos WHERE id = ?");
            $aluno->execute([$aluno_id]);
            $aluno = $aluno->fetch(PDO::FETCH_ASSOC);

            $frequencia = $pdo->prepare("SELECT data, presente, aulas_concluidas FROM presencas WHERE aluno_id = ? ORDER BY data DESC");
            $frequencia->execute([$aluno_id]);
            
            $recompensas = $pdo->query("SELECT id, titulo, descricao, pontos_necessarios FROM recompensas WHERE ativo = 1")->fetchAll(PDO::FETCH_ASSOC);
            
            $notificacoes = $pdo->prepare("SELECT * FROM notificacoes WHERE aluno_id = ? AND lida = 0 ORDER BY data_criacao DESC");
            $notificacoes->execute([$aluno_id]);

            json_response([
                'success' => true, 
                'aluno' => $aluno, 
                'frequencia' => $frequencia->fetchAll(PDO::FETCH_ASSOC),
                'recompensas' => $recompensas, 
                'notificacoes' => $notificacoes->fetchAll(PDO::FETCH_ASSOC)
            ]);
        } catch (Exception $e) {
            json_error('Erro ao carregar dados do portal.');
        }
        break;

    case 'update_profile':
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');

        if (empty($nome) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            json_error('Nome e email válido são obrigatórios.');
        }

        $stmt = $pdo->prepare("UPDATE alunos SET nome = ?, email = ?, telefone = ? WHERE id = ?");
        if($stmt->execute([$nome, $email, $telefone, $aluno_id])) {
            $_SESSION['aluno_nome'] = $nome;
            json_response(['success' => true, 'message' => 'Dados atualizados com sucesso!']);
        }
        break;

    case 'upload_photo':
        if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] != 0) {
            json_error('Erro no envio do ficheiro. Código: ' . ($_FILES['foto_perfil']['error'] ?? 'N/A'));
        }

        $upload_dir = 'uploads/perfil/';
        if (!is_dir($upload_dir) && !mkdir($upload_dir, 0775, true)) {
            json_error('Falha ao criar diretório de uploads. Verifique as permissões.');
        }
        if (!is_writable($upload_dir)) {
             json_error('O diretório de uploads não tem permissão de escrita.');
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $file_type = mime_content_type($_FILES['foto_perfil']['tmp_name']);

        if (!in_array($file_type, $allowed_types)) {
            json_error('Formato de ficheiro não permitido. Use JPEG, PNG, ou WEBP.');
        }
        
        $filename = $aluno_id . '_' . time() . '.' . pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
        $path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $path)) {
            $stmt_old = $pdo->prepare("SELECT foto_perfil FROM alunos WHERE id = ?");
            $stmt_old->execute([$aluno_id]);
            if ($old_path = $stmt_old->fetchColumn()) {
                if (file_exists($old_path) && is_file($old_path)) unlink($old_path);
            }
            
            $stmt = $pdo->prepare("UPDATE alunos SET foto_perfil = ? WHERE id = ?");
            $stmt->execute([$path, $aluno_id]);
            json_response(['success' => true, 'path' => $path]);
        } else {
            json_error('Falha ao mover o ficheiro para o destino final.');
        }
        break;
    
    case 'logout':
        session_destroy();
        json_response(['success' => true]);
        break;

    default:
        json_error('Ação desconhecida.');
        break;
}
?>
