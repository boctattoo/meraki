<?php
// criar_usuario.php - Script para criar um usuário administrador
require 'conexao.php';

$usuario = 'admin';
$senha = password_hash('admin123', PASSWORD_DEFAULT); // Altere a senha conforme necessidade

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, senha) VALUES (:usuario, :senha)");
    $stmt->execute(['usuario' => $usuario, 'senha' => $senha]);
    echo "Usuário criado com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao criar usuário: " . $e->getMessage();
}
