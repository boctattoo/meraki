<?php
session_start();
// 1. VERIFICAÇÃO DE SESSÃO E CONEXÃO
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// 2. FUNÇÕES DE SEGURANÇA E AUXILIARES
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// 3. PROCESSAMENTO DE FORMULÁRIOS (POST)
$aluno_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['mensagem_erro'] = 'Erro de segurança. Tente novamente.';
    } else {
        $acao = $_POST['acao'] ?? '';
        
        try {
            if ($acao === 'atualizar_dados') {
                $nome = trim($_POST['nome']);
                $params = [$nome, $_POST['data_nascimento'], $_POST['cpf'], $_POST['email'], $_POST['telefone'], $_POST['status'], $_POST['responsavel'], $_POST['telefone_responsavel']];
                $sql = "UPDATE alunos SET nome=?, data_nascimento=?, cpf=?, email=?, telefone=?, status=?, responsavel=?, telefone_responsavel=? ";
                if (!empty($_POST['nova_senha'])) {
                    $sql .= ", senha = ? ";
                    $params[] = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
                }
                $sql .= "WHERE id = ?";
                $params[] = $aluno_id;
                $pdo->prepare($sql)->execute($params);
                $_SESSION['mensagem_sucesso'] = 'Dados atualizados!';
            } elseif ($acao === 'remover_turma') {
                $turma_id = filter_input(INPUT_POST, 'turma_id', FILTER_VALIDATE_INT);
                $pdo->prepare("UPDATE alunos_turmas SET ativo = 0 WHERE aluno_id = ? AND turma_id = ?")->execute([$aluno_id, $turma_id]);
                $_SESSION['mensagem_sucesso'] = 'Aluno removido da turma!';
            } elseif ($acao === 'adicionar_turma') {
                $turma_id = filter_input(INPUT_POST, 'nova_turma_id', FILTER_VALIDATE_INT);
                $pdo->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, ativo, data_atribuicao) VALUES (?, ?, 1, CURDATE()) ON DUPLICATE KEY UPDATE ativo = 1, data_atribuicao = CURDATE()")->execute([$aluno_id, $turma_id]);
                $_SESSION['mensagem_sucesso'] = 'Aluno adicionado à turma!';
            }
        } catch (Exception $e) {
            $_SESSION['mensagem_erro'] = 'Erro: ' . $e->getMessage();
        }
    }
    header("Location: editar_aluno.php?id=$aluno_id");
    exit();
}

// 4. BUSCA DE DADOS PARA EXIBIÇÃO
if (!$aluno_id) { header('Location: buscar_aluno.php'); exit(); }
$aluno = $pdo->prepare("SELECT * FROM alunos WHERE id = ?");
$aluno->execute([$aluno_id]);
$aluno = $aluno->fetch(PDO::FETCH_ASSOC);
if (!$aluno) { header('Location: buscar_aluno.php'); exit(); }

$turmas_aluno = $pdo->prepare("SELECT t.id, t.nome FROM turmas t JOIN alunos_turmas at ON t.id = at.turma_id WHERE at.aluno_id = ? AND at.ativo = 1");
$turmas_aluno->execute([$aluno_id]);
$turmas_aluno = $turmas_aluno->fetchAll(PDO::FETCH_ASSOC);
$ids_turmas_aluno = array_column($turmas_aluno, 'id');

$turmas_disponiveis = $pdo->query("SELECT t.id, t.nome FROM turmas t WHERE t.status = 'ativa'")->fetchAll(PDO::FETCH_ASSOC);

$csrf_token = generateCSRFToken();
$mensagem_sucesso = $_SESSION['mensagem_sucesso'] ?? null;
$mensagem_erro = $_SESSION['mensagem_erro'] ?? null;
unset($_SESSION['mensagem_sucesso'], $_SESSION['mensagem_erro']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Aluno</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container mt-4 mb-5">
    <?php if ($mensagem_sucesso): ?><div class="alert alert-success"><?php echo htmlspecialchars($mensagem_sucesso); ?></div><?php endif; ?>
    <?php if ($mensagem_erro): ?><div class="alert alert-danger"><?php echo htmlspecialchars($mensagem_erro); ?></div><?php endif; ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Editar Perfil</h4>
            <a href="buscar_aluno.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Voltar</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <!-- ATUALIZADO: Mostra foto de perfil ou placeholder -->
                    <?php
                        $foto_path = (!empty($aluno['foto_perfil']) && file_exists($aluno['foto_perfil']))
                            ? $aluno['foto_perfil']
                            : 'https://i.pravatar.cc/300?u=' . $aluno['id'];
                    ?>
                    <img src="<?php echo $foto_path; ?>" alt="Foto do Aluno" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h5 class="mb-1"><?php echo htmlspecialchars($aluno['nome']); ?></h5>
                    <p class="text-muted">ID do Aluno: <?php echo $aluno['id']; ?></p>
                </div>
                <div class="col-md-8">
                    <!-- Formulário de Dados Cadastrais -->
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <input type="hidden" name="acao" value="atualizar_dados">

                        <div class="row g-3">
                            <div class="col-12"><label class="form-label">Nome Completo</label><input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($aluno['nome']); ?>" required></div>
                            <div class="col-md-6"><label class="form-label">Data de Nascimento</label><input type="date" class="form-control" name="data_nascimento" value="<?php echo htmlspecialchars($aluno['data_nascimento']); ?>"></div>
                            <div class="col-md-6"><label class="form-label">CPF</label><input type="text" class="form-control" name="cpf" value="<?php echo htmlspecialchars($aluno['cpf'] ?? ''); ?>"></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($aluno['email'] ?? ''); ?>"></div>
                            <div class="col-md-6"><label class="form-label">Telefone</label><input type="tel" class="form-control" name="telefone" value="<?php echo htmlspecialchars($aluno['telefone'] ?? ''); ?>"></div>
                            <div class="col-md-6"><label class="form-label">Responsável</label><input type="text" class="form-control" name="responsavel" value="<?php echo htmlspecialchars($aluno['responsavel'] ?? ''); ?>"></div>
                            <div class="col-md-6"><label class="form-label">Telefone do Responsável</label><input type="tel" class="form-control" name="telefone_responsavel" value="<?php echo htmlspecialchars($aluno['telefone_responsavel'] ?? ''); ?>"></div>
                            <div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="status"><option value="Ativo" <?php echo $aluno['status'] === 'Ativo' ? 'selected' : ''; ?>>Ativo</option><option value="Trancado" <?php echo $aluno['status'] === 'Trancado' ? 'selected' : ''; ?>>Trancado</option><option value="Cancelado" <?php echo $aluno['status'] === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option></select></div>
                            <div class="col-md-6"><label class="form-label">Nova Senha (deixe em branco para não alterar)</label><input type="password" class="form-control" name="nova_senha"></div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 float-end"><i class="fas fa-save me-2"></i>Salvar Alterações</button>
                    </form>
                </div>
            </div>
            <hr>
            <!-- Gerenciamento de Turmas -->
            <h5 class="mt-4">Gerenciar Turmas</h5>
            <div class="row">
                <div class="col-md-6">
                    <h6>Turmas Atuais</h6>
                     <?php if (empty($turmas_aluno)): ?>
                        <p class="text-muted">Nenhuma turma.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach($turmas_aluno as $turma): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($turma['nome']); ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Remover aluno desta turma?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"><input type="hidden" name="acao" value="remover_turma"><input type="hidden" name="turma_id" value="<?php echo $turma['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>
                                </form>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h6>Adicionar Nova Turma</h6>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"><input type="hidden" name="acao" value="adicionar_turma">
                        <div class="input-group">
                            <select name="nova_turma_id" class="form-select" required><option value="">Selecione...</option>
                                <?php foreach($turmas_disponiveis as $turma): if (!in_array($turma['id'], $ids_turmas_aluno)): ?>
                                <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
