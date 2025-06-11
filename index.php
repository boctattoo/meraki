<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Exemplo de nome do usuário (você pode usar $_SESSION['usuario_nome'] ou similar)
$usuario_nome = $_SESSION['usuario_nome'] ?? 'Usuário Meraki';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Meraki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .dashboard {
            max-width: 480px;
            margin: 0 auto;
            padding: 2rem 1.2rem;
            background-color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .dashboard img.mascote {
            width: 100px;
            margin-bottom: 1rem;
        }

        .usuario-info {
            text-align: center;
            margin-bottom: 2rem;
        }

        .usuario-info .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 0.5rem;
        }

        .usuario-info h2 {
            font-size: 1.2rem;
            margin: 0;
        }

        .dashboard a.btn {
            font-size: 1.05rem;
            padding: 0.9rem;
            margin-bottom: 1rem;
            width: 100%;
        }

        .logout {
            margin-top: 2rem;
            width: 100%;
            text-align: center;
        }

        @media (min-width: 768px) {
            .dashboard {
                border-radius: 16px;
                margin-top: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <img src="assets/mascote_meraki.webp" alt="Mascote Mêrakinho" class="mascote">

        <div class="usuario-info">
            <img src="https://i.pravatar.cc/150?img=3" alt="Avatar" class="avatar">
            <h2>Olá, <?= htmlspecialchars($usuario_nome) ?>!</h2>
        </div>

        <a href="cadastro_aluno.php" class="btn btn-primary">Cadastrar Aluno</a>
        <a href="precadastro.php" class="btn btn-info text-white">Pré-Cadastro</a>
        <a href="mapa_turmas.php" class="btn btn-success">Ver Mapa de Turmas</a>
        <button class="btn btn-secondary" disabled>Importar Planilha (em breve)</button>

        <div class="logout">
            <a href="logout.php" class="btn btn-outline-danger">Sair</a>
        </div>
    </div>
</body>
</html>
