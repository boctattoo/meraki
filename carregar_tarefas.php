<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não logado']);
    exit();
}

require 'conexao.php';

try {
    $sql = "SELECT t.*, u.nome as nome_usuario_atribuido, t.usuario_atribuido_id
            FROM tarefas t 
            LEFT JOIN usuarios u ON t.usuario_atribuido_id = u.id 
            ORDER BY t.id DESC";
    
    $stmt = $pdo->query($sql);
    $todas_tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $tarefas_organizadas = [
        'afazer' => [],
        'progresso' => [],
        'concluido' => []
    ];
    
    foreach ($todas_tarefas as $tarefa) {
        $status = $tarefa['status'] ?? 'afazer';
        if (isset($tarefas_organizadas[$status])) {
            $tarefas_organizadas[$status][] = $tarefa;
        }
    }
    
    echo json_encode([
        'sucesso' => true,
        'tarefas' => $tarefas_organizadas
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'sucesso' => false,
        'erro' => 'Erro ao carregar tarefas: ' . $e->getMessage()
    ]);
}
?>