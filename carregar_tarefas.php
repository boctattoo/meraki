<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Sess«ªo expirada']);
    exit();
}

require 'conexao.php';

try {
    $sql = "SELECT 
                t.id,
                t.titulo,
                t.descricao,
                t.status,
                t.data_evento,
                t.prioridade,
                t.etiqueta,
                t.data_criacao,
                t.usuario_id,
                u.nome as nome_usuario_criador,
                ua.id as usuario_atribuido_id,
                ua.nome as nome_usuario_atribuido
            FROM tarefas t
            LEFT JOIN usuarios u ON t.usuario_id = u.id
            LEFT JOIN tarefas_usuarios tu ON t.id = tu.tarefa_id
            LEFT JOIN usuarios ua ON tu.usuario_id = ua.id
            ORDER BY t.data_criacao DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organizar tarefas por status
    $tarefas = [
        'afazer' => [],
        'progresso' => [],
        'concluido' => []
    ];
    
    foreach ($resultados as $tarefa) {
        $tarefas[$tarefa['status']][] = $tarefa;
    }
    
    echo json_encode(['sucesso' => true, 'tarefas' => $tarefas]);
    
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao carregar tarefas: ' . $e->getMessage()]);
}