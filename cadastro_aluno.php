<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function checkSessionTimeout() {
    $timeout = 7200;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        header('Location: login.php?timeout=1');
        exit();
    }
    $_SESSION['last_activity'] = time();
}

function sanitizeInput($data) {
    return is_array($data) ? array_map('sanitizeInput', $data) : htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

checkSessionTimeout();
$csrf_token = generateCSRFToken();

$mensagem = '';
$erro = '';
$duplicatas = [];

try {
    $turmas = $pdo->query("SELECT t.id, t.nome, t.vagas_total, COUNT(at.aluno_id) as ocupadas FROM turmas t LEFT JOIN alunos_turmas at ON t.id = at.turma_id AND at.ativo = 1 WHERE t.status = 'ativa' GROUP BY t.id ORDER BY t.nome")->fetchAll(PDO::FETCH_ASSOC);
    $cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
    $tipos_aluno = $pdo->query("SELECT id, nome FROM tipos_aluno ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $erro = "Erro ao carregar dados: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $erro = 'Token de segurança inválido.';
    } else {
        $pdo->beginTransaction();
        try {
            $dados = sanitizeInput($_POST);

            $nome = $dados['nome'] ?? '';
            $telefone = preg_replace('/[^0-9]/', '', $dados['telefone'] ?? '');
            $email = $dados['email'] ?? '';
            $data_nascimento = $dados['data_nascimento'] ?? null;
            $tipo_aluno_id = (int)($dados['tipo_aluno_id'] ?? 0);
            $curso_ids = $dados['curso_id'] ?? [];
            $turma_ids = isset($dados['turma_id']) ? explode(',', $dados['turma_id']) : [];
            $observacoes = $dados['observacoes'] ?? '';

            if (empty($nome) || empty($curso_ids) || empty($turma_ids) || !$tipo_aluno_id) {
                throw new Exception("Todos os campos obrigatórios devem ser preenchidos.");
            }

            if ($tipo_aluno_id == 1 && count($turma_ids) > 1) {
                throw new Exception("Alunos Parcelados podem escolher apenas uma turma.");
            }

            $check = $pdo->prepare("SELECT id, nome FROM alunos WHERE nome LIKE ? OR telefone = ?");
            $check->execute(["%$nome%", $telefone]);
            $duplicatas = $check->fetchAll();

            if (!empty($duplicatas) && empty($dados['confirmar_duplicata'])) {
                $erro = 'Possível duplicata encontrada!';
                $pdo->rollBack();
            } else {
                foreach ($turma_ids as $turma_id) {
                    $vagas = $pdo->prepare("SELECT t.vagas_total, COUNT(at.aluno_id) as ocupadas FROM turmas t LEFT JOIN alunos_turmas at ON t.id = at.turma_id AND at.ativo = 1 WHERE t.id = ? GROUP BY t.id");
                    $vagas->execute([$turma_id]);
                    $res = $vagas->fetch();
                    if ($res && $res['ocupadas'] >= $res['vagas_total']) {
                        throw new Exception("Turma ID $turma_id está lotada.");
                    }
                }

                $stmt = $pdo->prepare("INSERT INTO alunos (nome, telefone, email, data_nascimento, tipo_aluno_id, status, data_cadastro, observacoes) VALUES (?, ?, ?, ?, ?, 'Ativo', NOW(), ?)");
                $stmt->execute([$nome, $telefone, $email, $data_nascimento, $tipo_aluno_id, $observacoes]);
                $aluno_id = $pdo->lastInsertId();
                // echo "ALUNO_ID GERADO: $aluno_id"; // REMOVIDO O EXIT PARA PERMITIR CONTINUIDADE


                foreach ($curso_ids as $curso_id) {
    $pdo->prepare("INSERT INTO alunos_cursos (aluno_id, curso_id) VALUES (?, ?)")->execute([$aluno_id, $curso_id]);
}

                foreach ($turma_ids as $turma_id) {
                    $pdo->prepare("INSERT INTO alunos_turmas (aluno_id, turma_id, data_atribuicao, ativo) VALUES (?, ?, CURDATE(), 1)")->execute([$aluno_id, $turma_id]);
                }

                $pdo->commit();
                $mensagem = "Aluno cadastrado com sucesso!";
                $_POST = [];
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $erro = $e->getMessage();
        }
    }
}


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include 'nav.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno | Sistema Meraki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
 <div class="container py-4">
    <h2 class="text-center mb-4">Cadastro de Aluno</h2>

    <?php if ($mensagem): ?>
        <div class="alert alert-success" role="alert" aria-live="polite"> <?= $mensagem ?> </div>
    <?php endif; ?>

    <?php if ($erro): ?>
        <div class="alert alert-danger" role="alert" aria-live="assertive"> <?= $erro ?> </div>
    <?php endif; ?>

    <?php if (!empty($duplicatas)): ?>
        <div class="alert alert-warning" role="alert">
            <strong>Possível duplicata encontrada!</strong>
            <ul>
                <?php foreach ($duplicatas as $dup): ?>
                    <li>ID: <?= $dup['id'] ?> - <?= $dup['nome'] ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="confirmar_duplicata" id="confirmar_duplicata" value="1">
                <label class="form-check-label" for="confirmar_duplicata">Confirmo o cadastro mesmo assim.</label>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" id="cadastroForm">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <div class="mb-3">
            <label for="nome" class="form-label">Nome *</label>
            <input type="text" class="form-control" name="nome" id="nome" required value="<?= $_POST['nome'] ?? '' ?>">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" name="telefone" id="telefone" value="<?= $_POST['telefone'] ?? '' ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" id="email" value="<?= $_POST['email'] ?? '' ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control" name="data_nascimento" id="data_nascimento" value="<?= $_POST['data_nascimento'] ?? '' ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="tipo_aluno_id" class="form-label">Tipo de Aluno *</label>
                <select class="form-select" name="tipo_aluno_id" id="tipo_aluno_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach($tipos_aluno as $tipo): ?>
                        <option value="<?= $tipo['id'] ?>" <?= ($_POST['tipo_aluno_id'] ?? '') == $tipo['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tipo['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="curso_id" class="form-label">Cursos *</label>
            <select class="form-select" name="curso_id[]" id="curso_id" multiple required>
                <?php foreach($cursos as $curso): ?>
                    <option value="<?= $curso['id'] ?>" <?= in_array($curso['id'], $_POST['curso_id'] ?? []) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($curso['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text text-muted">Cursos selecionados: <span id="cursosSelecionados"></span></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Turmas *</label>
            <input type="hidden" name="turma_id" id="turma_id">
            <div class="row">
                <?php foreach ($turmas as $turma): 
                    $restantes = $turma['vagas_total'] - $turma['ocupadas'];
                    $lotada = $restantes <= 0;
                ?>
                <div class="col-md-6">
                    <div class="card mb-2 turma-card <?= $lotada ? 'bg-light text-muted' : '' ?>" data-id="<?= $turma['id'] ?>">
                        <div class="card-body">
                            <h6><?= htmlspecialchars($turma['nome']) ?></h6>
                            <small><?= $turma['ocupadas'] ?>/<?= $turma['vagas_total'] ?> vagas</small>
                            <?php if ($lotada): ?>
                                <span class="badge bg-danger float-end">Lotada</span>
                            <?php else: ?>
                                <span class="badge bg-success float-end">Disponível</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="observacoes" class="form-label">Observações</label>
            <textarea class="form-control" name="observacoes" id="observacoes" rows="3"><?= $_POST['observacoes'] ?? '' ?></textarea>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Salvar Cadastro
            </button>
            <button type="reset" class="btn btn-outline-secondary">
                <i class="fas fa-undo me-2"></i>Limpar Formulário
            </button>
        </div>
    </form>
</div>

<script>
const telefoneInput = document.getElementById('telefone');
telefoneInput.addEventListener('input', function () {
    let val = telefoneInput.value.replace(/\D/g, '');
    if (val.length > 10) val = val.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    else val = val.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    telefoneInput.value = val;
});

const turmaInput = document.getElementById('turma_id');
let selecionadas = [];
const tipoAluno = document.querySelector('[name="tipo_aluno_id"]');
tipoAluno.addEventListener('change', () => selecionadas = []);

document.querySelectorAll('.turma-card').forEach(card => {
    if (!card.classList.contains('bg-light')) {
        card.addEventListener('click', () => {
            const id = card.dataset.id;
            if (card.classList.contains('border-success')) {
                card.classList.remove('border-success');
                selecionadas = selecionadas.filter(tid => tid !== id);
            } else {
                if (tipoAluno.value == '1' && selecionadas.length >= 1) return;
                card.classList.add('border-success');
                selecionadas.push(id);
            }
            turmaInput.value = selecionadas.join(',');
        });
    }
});

const cursosSelect = document.getElementById('curso_id');
const cursosSelecionados = document.getElementById('cursosSelecionados');
function atualizarCursosSelecionados() {
    const nomes = Array.from(cursosSelect.selectedOptions).map(opt => opt.text);
    cursosSelecionados.textContent = nomes.join(', ');
}
cursosSelect.addEventListener('change', atualizarCursosSelecionados);
document.addEventListener('DOMContentLoaded', atualizarCursosSelecionados);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
