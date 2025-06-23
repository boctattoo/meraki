<?php
// diagnostico_rapido.php - Para ver o que está acontecendo

echo "<h1>🔍 Diagnóstico Rápido - Meraki</h1>";

// 1. Verificar se as correções foram aplicadas
echo "<h2>1. ✅ Verificar se código foi atualizado:</h2>";

$arquivo_principal = 'mapa_turmas.php'; // ou o nome do seu arquivo
if (file_exists($arquivo_principal)) {
    $conteudo = file_get_contents($arquivo_principal);
    
    // Verificar se tem as funções novas
    $tem_mapeamento = strpos($conteudo, 'mapearDiaSemana') !== false;
    $tem_ordenacao = strpos($conteudo, 'WHEN \'MULTIMÍDIA\' THEN 1') !== false;
    $tem_visualizacao = strpos($conteudo, 'data-hoje') !== false;
    
    echo "<div style='background: " . ($tem_mapeamento ? '#d4edda' : '#f8d7da') . "; padding: 10px; margin: 5px;'>";
    echo "🔧 Função mapearDiaSemana: " . ($tem_mapeamento ? "✅ ENCONTRADA" : "❌ NÃO ENCONTRADA");
    echo "</div>";
    
    echo "<div style='background: " . ($tem_ordenacao ? '#d4edda' : '#f8d7da') . "; padding: 10px; margin: 5px;'>";
    echo "📋 Ordenação MULTIMÍDIA: " . ($tem_ordenacao ? "✅ ENCONTRADA" : "❌ NÃO ENCONTRADA");
    echo "</div>";
    
    echo "<div style='background: " . ($tem_visualizacao ? '#d4edda' : '#f8d7da') . "; padding: 10px; margin: 5px;'>";
    echo "👁️ Atributo data-hoje: " . ($tem_visualizacao ? "✅ ENCONTRADO" : "❌ NÃO ENCONTRADO");
    echo "</div>";
    
    if (!$tem_mapeamento || !$tem_ordenacao || !$tem_visualizacao) {
        echo "<div style='background: #fff3cd; padding: 15px; margin: 10px; border: 2px solid #ffc107;'>";
        echo "<strong>⚠️ PROBLEMA IDENTIFICADO:</strong><br>";
        echo "O código NÃO foi atualizado! Você precisa substituir completamente o arquivo principal.";
        echo "</div>";
    }
} else {
    echo "<p style='color: red;'>❌ Arquivo principal não encontrado!</p>";
    echo "<p>Procure pelo arquivo que contém o mapa de turmas (pode ser: index.php, turmas.php, etc.)</p>";
}

// 2. Testar a ordenação atual
echo "<h2>2. 📋 Teste da Ordenação Atual:</h2>";

require_once 'conexao.php';

try {
    $stmt = $pdo->query("
        SELECT 
            t.nome,
            tt.nome as tipo,
            d.nome as dia
        FROM turmas t
        LEFT JOIN tipos_turma tt ON t.tipo_id = tt.id
        LEFT JOIN dias_semana d ON t.dia_semana_id = d.id
        WHERE t.status = 'ativa'
        ORDER BY t.nome
        LIMIT 10
    ");
    
    $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'><th>Turma</th><th>Tipo</th><th>Dia</th></tr>";
    
    foreach ($turmas as $turma) {
        $cor_tipo = ($turma['tipo'] === 'MULTIMÍDIA') ? '#e7f3ff' : '#fff3e7';
        echo "<tr style='background: $cor_tipo;'>";
        echo "<td>{$turma['nome']}</td>";
        echo "<td><strong>{$turma['tipo']}</strong></td>";
        echo "<td>{$turma['dia']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>📝 Como deveria estar:</strong> MULTIMÍDIA primeiro (azul), depois DINÂMICO (laranja), ordenado por dia da semana.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}

// 3. Instruções claras
echo "<h2>3. 📋 Instruções Para Corrigir:</h2>";

echo "<div style='background: #e7f3ff; padding: 20px; border: 2px solid #007bff; margin: 10px;'>";
echo "<h3>🔧 PASSO A PASSO:</h3>";
echo "<ol>";
echo "<li><strong>Encontre o arquivo principal</strong> (provavelmente: mapa_turmas.php, index.php ou turmas.php)</li>";
echo "<li><strong>Faça backup</strong> do arquivo atual (copie e renomeie para backup_arquivo.php)</li>";
echo "<li><strong>Abra o arquivo</strong> no editor de código</li>";
echo "<li><strong>DELETE TODO o conteúdo</strong> do arquivo</li>";
echo "<li><strong>COLE o código corrigido</strong> que eu forneci no artefato 'Sistema de Filtros Corrigido'</li>";
echo "<li><strong>SALVE o arquivo</strong></li>";
echo "<li><strong>Atualize a página</strong> com Ctrl+F5 (limpa cache)</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; margin: 10px; border: 2px solid #ffc107;'>";
echo "<h3>⚠️ IMPORTANTE:</h3>";
echo "<ul>";
echo "<li>Tem que substituir o arquivo INTEIRO</li>";
echo "<li>Não pode só copiar pedaços</li>";
echo "<li>Tem que limpar o cache do navegador depois</li>";
echo "</ul>";
echo "</div>";

// 4. Link direto para o código
echo "<h2>4. 🔗 Código Para Copiar:</h2>";
echo "<p>👆 Copie TODO o código do artefato 'Sistema de Filtros Corrigido' acima nesta conversa.</p>";
echo "<p>Ele tem <strong>" . (file_exists($arquivo_principal) ? number_format(strlen(file_get_contents($arquivo_principal))) : 'muitas') . "</strong> caracteres e deve substituir completamente seu arquivo atual.</p>";

echo "<h2>5. 🧪 Após Substituir:</h2>";
echo "<p>Execute este diagnóstico novamente para confirmar que funcionou!</p>";
?>