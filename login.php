<?php
session_start();
require 'conexao.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE user = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['passa'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_cargo'] = $user['cargo'];
        header('Location: index.php');
        exit;
    } else {
        $erro = "Usuário ou senha inválidos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema Meraki</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #726cf8 0%, #f89c8c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(100,30,255,0.08);
            width: 100%;
            max-width: 360px;
        }
        .login-card .form-control:focus {
            border-color: #726cf8;
            box-shadow: 0 0 0 .2rem #726cf87d;
        }
        .login-card .btn-primary {
            background: linear-gradient(90deg, #726cf8 60%, #f89c8c 100%);
            border: none;
        }
        .login-card .bi {
            font-size: 2.2rem;
            color: #726cf8;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <form class="login-card" method="post" autocomplete="off">
        <div class="text-center">
            <i class="bi bi-person-circle"></i>
            <h2 class="mb-4">Bem-vindo!</h2>
        </div>
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>
        <button class="btn btn-primary w-100" type="submit">Entrar <i class="bi bi-arrow-right-short"></i></button>
        <div class="mt-4 text-muted small text-center">
            <i class="bi bi-shield-lock"></i> Acesso restrito | Sistema Meraki
        </div>
    </form>
</body>
</html>
