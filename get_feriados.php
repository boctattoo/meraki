<?php
require 'conexao.php';

$anoAtual = date('Y');
$anoLimite = $anoAtual + 1;

$stmt = $pdo->prepare("SELECT data FROM feriados WHERE YEAR(data) BETWEEN ? AND ?");
$stmt->execute([$anoAtual, $anoLimite]);

$datas = $stmt->fetchAll(PDO::FETCH_COLUMN);

header('Content-Type: application/json');
echo json_encode($datas);
