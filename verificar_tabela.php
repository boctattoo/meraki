<?php
require 'conexao.php';

echo "<h2>Estrutura da Tabela ALUNOS</h2>";

try {
    // Verificar estrutura da tabela alunos
    $stmt = $pdo->query("DESCRIBE alunos");
    $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 8px;'>Campo</th>";
    echo "<th style='padding: 8px;'>Tipo</th>";
    echo "<th style='padding: 8px;'>Null</th>";
    echo "<th style='padding: 8px;'>Key</th>";
    echo "<th style='padding: 8px;'>Default</th>";
    echo "</tr>";
    
    foreach ($colunas as $coluna) {
        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>" . $coluna['Field'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Type'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Null'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Key'] . "</td>";
        echo "<td style='padding: 8px;'>" . ($coluna['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Estrutura da Tabela CONTRATOS</h2>";
    
    // Verificar estrutura da tabela contratos
    $stmt = $pdo->query("DESCRIBE contratos");
    $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 8px;'>Campo</th>";
    echo "<th style='padding: 8px;'>Tipo</th>";
    echo "<th style='padding: 8px;'>Null</th>";
    echo "<th style='padding: 8px;'>Key</th>";
    echo "<th style='padding: 8px;'>Default</th>";
    echo "</tr>";
    
    foreach ($colunas as $coluna) {
        echo "<tr>";
        echo "<td style='padding: 8px; font-weight: bold;'>" . $coluna['Field'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Type'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Null'] . "</td>";
        echo "<td style='padding: 8px;'>" . $coluna['Key'] . "</td>";
        echo "<td style='padding: 8px;'>" . ($coluna['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Todas as Tabelas do Banco</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tabelas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($tabelas as $tabela) {
        $nomeTabela = array_values($tabela)[0];
        echo "<li style='padding: 4px;'><strong>$nomeTabela</strong></li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>