<?php
require 'conexao.php';
$id_curso = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT SUM(carga_horaria) AS total FROM modulos WHERE curso_id = ?");
$stmt->execute([$id_curso]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(['carga_horaria' => $row['total']]);
