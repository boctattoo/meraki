<?php
session_start();
// Se o aluno já estiver logado, redireciona para o portal
if (isset($_SESSION['aluno_id'])) {
    header('Location: portal_do_aluno.php');
    exit();
}
// Mensagens de erro ou sucesso podem ser passadas via GET
$mensagem = $_GET['mensagem'] ?? '';
$erro = $_GET['erro'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Aluno - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f2f5;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-radius: 0.75rem;
            border: none;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                <h3 class="mt-3">Portal do Aluno</h3>
                <p class="text-muted">Acesse as suas informações acadêmicas.</p>
            </div>
            
            <div id="alert-container">
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>
                <?php if ($mensagem): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($mensagem); ?></div>
                <?php endif; ?>
            </div>

            <form id="loginForm">
                <div class="mb-3">
                    <label for="login_identifier" class="form-label">CPF, Celular ou Email</label>
                    <input type="text" class="form-control" id="login_identifier" name="login_identifier" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" id="loginButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Entrar
                    </button>
                </div>
            </form>
            <div class="text-center mt-3">
                <small class="text-muted">Senha inicial: data de nascimento (DDMMAAAA) ou CPF (só números).</small>
            </div>
        </div>
    </div>

    <!-- Modal para Definir Senha no Primeiro Acesso -->
    <div class="modal fade" id="setPasswordModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="setPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setPasswordModalLabel">Crie a sua Senha de Acesso</h5>
                </div>
                <form id="setPasswordForm">
                    <div class="modal-body">
                        <input type="hidden" id="aluno_id_hidden" name="aluno_id">
                        <p>Este é o seu primeiro acesso. Por segurança, crie uma senha pessoal para entrar no portal.</p>
                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="nova_senha" name="nova_senha" required minlength="6">
                            <div class="form-text">Mínimo de 6 caracteres.</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirma_senha" class="form-label">Confirme a Senha</label>
                            <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Salvar Senha e Continuar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const spinner = loginButton.querySelector('.spinner-border');
    const alertContainer = document.getElementById('alert-container');
    const setPasswordModal = new bootstrap.Modal(document.getElementById('setPasswordModal'));
    const setPasswordForm = document.getElementById('setPasswordForm');

    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        spinner.classList.remove('d-none');
        loginButton.disabled = true;
        alertContainer.innerHTML = '';

        fetch('aluno_api.php?action=login', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'portal_do_aluno.php';
            } else {
                if (data.reason === 'primeiro_acesso') {
                    document.getElementById('aluno_id_hidden').value = data.aluno_id;
                    setPasswordModal.show();
                } else {
                    showAlert(data.error || 'Ocorreu um erro. Tente novamente.', 'danger');
                }
            }
        }).catch(() => showAlert('Erro de conexão. Verifique a sua internet.', 'danger'))
        .finally(() => {
            spinner.classList.add('d-none');
            loginButton.disabled = false;
        });
    });
    
    setPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const novaSenha = document.getElementById('nova_senha').value;
        const confirmaSenha = document.getElementById('confirma_senha').value;

        if (novaSenha.length < 6) {
            alert('A senha deve ter no mínimo 6 caracteres.');
            return;
        }
        if (novaSenha !== confirmaSenha) {
            alert('As senhas não coincidem!');
            return;
        }

        fetch('aluno_api.php?action=definir_senha', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                setPasswordModal.hide();
                showAlert('Senha definida com sucesso! Agora pode entrar com a sua nova senha.', 'success');
                loginForm.reset();
            } else {
                alert(data.error || 'Não foi possível definir a senha.');
            }
        });
    });

    function showAlert(message, type) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        alertContainer.innerHTML = '';
        alertContainer.append(wrapper);
    }
});
</script>
</body>
</html>
