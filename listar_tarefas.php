<?php
session_start();
header('Content-Type: application/json');
require 'conexao.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;
$is_admin = $_SESSION['is_admin'] ?? false; // você pode adaptar com base no seu sistema

try {
    $sql = "
        SELECT
            t.id,
            t.titulo,
            t.descricao,
            t.status,
            t.data_evento,
            t.prioridade,
            t.etiqueta,
            tu.usuario_id AS usuario_atribuido_id, -- ID do usuário atribuído (para o dropdown)
            u.nome AS nome_usuario_atribuido,      -- Nome do usuário atribuído (para exibir no card)
            GROUP_CONCAT(tu_all.usuario_id) AS todos_usuarios_ids, -- Para fins de depuração ou expansão futura
            GROUP_CONCAT(u_all.nome SEPARATOR ', ') AS todos_responsaveis -- Se quiser exibir todos os responsáveis no card
        FROM tarefas t
        LEFT JOIN tarefas_usuarios tu ON tu.tarefa_id = t.id
        LEFT JOIN usuarios u ON u.id = tu.usuario_id
        LEFT JOIN tarefas_usuarios tu_all ON tu_all.tarefa_id = t.id -- Para buscar todos os responsáveis
        LEFT JOIN usuarios u_all ON u_all.id = tu_all.usuario_id   -- Para buscar todos os responsáveis
    ";

    // Filtering by user if not admin
    if (!$is_admin) {
        // If a task needs to be filtered by who it's assigned to:
        // Adjusting WHERE clause for task assignment logic:
        // A user sees tasks assigned to them OR tasks with no assignment.
        // Or if 'is_admin' means 'can see all tasks', then the WHERE should only apply if not admin.
        // For simplicity, let's stick to the current logic: show tasks where current user is assigned.
        $sql .= " WHERE tu.usuario_id = :uid "; // This will only show tasks assigned to the user.
                                                // If a task has no assignment, it won't be shown to non-admins.
    }

    $sql .= " GROUP BY t.id ORDER BY t.data_evento ASC, t.id ASC"; // Added t.id to GROUP BY and ORDER BY for consistency

    $stmt = $pdo->prepare($sql);

    if (!$is_admin) {
        $stmt->bindParam(':uid', $usuario_id, PDO::PARAM_INT);
    }

    $stmt->execute();
    $tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'tarefas' => $tarefas]);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao listar tarefas: ' . $e->getMessage()]);
}