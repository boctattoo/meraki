<?php
// carregar_tarefas.php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não autenticado.']);
    exit();
}

require 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

$usuario_id = $_SESSION['usuario_id'];
$tarefas = [
    'afazer'    => [],
    'progresso' => [],
    'concluido' => []
];

try {
    $stmt = $pdo->prepare("SELECT id, titulo, descricao, status, data_evento, prioridade, etiqueta FROM tarefas WHERE usuario_id = ? ORDER BY data_criacao DESC");
    $stmt->execute([$usuario_id]);

    while ($row = $stmt->fetch()) {
        // Garante que 'data_evento' seja null se for '0000-00-00' ou vazio
        $row['data_evento'] = ($row['data_evento'] == '0000-00-00' || empty($row['data_evento'])) ? null : $row['data_evento'];
        
        $tarefas[$row['status']][] = $row;
    }

    echo json_encode(['sucesso' => true, 'tarefas' => $tarefas]);

} catch (PDOException $e) {
    // Em um ambiente de produção, registre o erro em um log em vez de exibi-lo diretamente.
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao carregar tarefas: ' . $e->getMessage()]);
}
?>