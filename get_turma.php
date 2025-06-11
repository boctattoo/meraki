<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conexao.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("
  SELECT 
    t.id,
    t.nome,
    ds.nome AS dia_semana,
    p.nome AS periodo
  FROM turmas t
  LEFT JOIN dias_semana ds ON ds.id = t.dia_semana_id
  LEFT JOIN periodos p ON p.id = t.periodo_id
  WHERE t.id = ?
");
$stmt->execute([$id]);

$turma = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($turma);
