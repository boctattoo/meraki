<?php
require 'conexao.php'; // usa a conexão já configurada

$dados = $_POST;

try {
    $pdo->beginTransaction();

    // Verifica se a turma tem vagas antes de prosseguir
    if (!empty($dados['turma'])) {
        $turma_id = (int)$dados['turma'];
        $vagas = $pdo->query("SELECT vagas_por_sala FROM turmas WHERE id = $turma_id")->fetchColumn();
        $ocupadas = $pdo->query("SELECT COUNT(*) FROM alunos_turmas WHERE turma_id = $turma_id AND ativo = 1")->fetchColumn();

        if ($ocupadas >= $vagas) {
            $pdo->rollBack();
            header("Location: precadastro.php?erro_turma=1");
            exit;
        }
    }

    // Separa os cursos e remove do array principal
    $cursos = [];
    if (isset($dados['cursos'])) {
        $cursos = is_array($dados['cursos']) ? $dados['cursos'] : explode(',', $dados['cursos']);
        if (count($cursos) === 1) {
            $dados['curso_id'] = $cursos[0];
        }
        unset($dados['cursos']);
    }

    if (!empty($dados['turma'])) {
        $dados['turma_id'] = $dados['turma'];
    }
    unset($dados['turma']);
    unset($dados['curso']); // segurança extra

    // Converte datas
    $datas = ['inicio_aulas', 'termino_aulas', 'data_nascimento_aluno', 'data_nascimento_pagador', 'data_vencimento'];
    foreach ($datas as $campo) {
        if (!empty($dados[$campo])) {
            $dt = DateTime::createFromFormat('d/m/Y', $dados[$campo]);
            if ($dt !== false) {
                $dados[$campo] = $dt->format('Y-m-d');
            }
        }
    }

    // Campos permitidos para salvar
    $camposPermitidos = [
        'nome_aluno','data_nascimento_aluno','estado_civil','profissao_aluno','sexo','endereco_aluno','cep_aluno','cidade_aluno','telefone_aluno','cpf_cnpj_aluno','nome_responsavel','telefone_responsavel',
        'nome_pagador','data_nascimento_pagador','endereco_pagador','cpf_cnpj_pagador','profissao_pagador','bairro_pagador','cidade_pagador','telefone_pagador','celular_pagador',
        'valor_curso','desconto_promocional','valor_descontado','material_didatico','valor_total','qtd_parcelas','valor_parcela','data_vencimento',
        'turma_id','curso_id','duracao','inicio_aulas','termino_aulas','dias_semana','horario','carga_horaria',
        'entrada','parcela_integral','desconto_pontualidade','parcela_com_desconto','parcela_material','qtd_meses'
    ];
    $dados = array_filter($dados, fn($k) => in_array($k, $camposPermitidos), ARRAY_FILTER_USE_KEY);

    // Insere contrato
    $campos = array_keys($dados);
    $placeholders = array_map(fn($campo) => ":$campo", $campos);
    $sql = "INSERT INTO contratos (" . implode(", ", $campos) . ") VALUES (" . implode(", ", $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    foreach ($dados as $campo => $valor) {
        $stmt->bindValue(":$campo", $valor);
    }
    $stmt->execute();
    $contrato_id = $pdo->lastInsertId();

    // Insere cursos vinculados
    if ($contrato_id && count($cursos)) {
        $stmtCurso = $pdo->prepare("INSERT INTO contratos_cursos (contrato_id, curso_id) VALUES (?, ?)");
        foreach ($cursos as $curso_id) {
            if ($curso_id) {
                $stmtCurso->execute([$contrato_id, $curso_id]);
            }
        }
    }

    $pdo->commit();
    echo "<h2>Contrato salvo com sucesso!</h2>";
    echo '<a href="precadastro.php">Voltar ao formulário</a>';

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Erro ao salvar contrato: " . $e->getMessage();
}
?>
