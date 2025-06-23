<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require 'conexao.php';

try {
    $pdo->beginTransaction();
    
    // Capturar dados do formulário
    $nome_aluno = $_POST['nome_aluno'] ?? '';
    $data_nascimento_aluno = $_POST['data_nascimento_aluno'] ?? '';
    $estado_civil = $_POST['estado_civil'] ?? '';
    $profissao_aluno = $_POST['profissao_aluno'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $endereco_aluno = $_POST['endereco_aluno'] ?? '';
    $cep_aluno = $_POST['cep_aluno'] ?? '';
    $cidade_aluno = $_POST['cidade_aluno'] ?? '';
    $telefone_aluno = $_POST['telefone_aluno'] ?? '';
    $cpf_cnpj_aluno = $_POST['cpf_cnpj_aluno'] ?? '';
    $nome_responsavel = $_POST['nome_responsavel'] ?? '';
    $telefone_responsavel = $_POST['telefone_responsavel'] ?? '';
    $tipo_aluno = $_POST['tipo_aluno'] ?? 'regular';
    
    // Dados do pagador
    $nome_pagador = $_POST['nome_pagador'] ?? '';
    $data_nascimento_pagador = $_POST['data_nascimento_pagador'] ?? '';
    $profissao_pagador = $_POST['profissao_pagador'] ?? '';
    $endereco_pagador = $_POST['endereco_pagador'] ?? '';
    $bairro_pagador = $_POST['bairro_pagador'] ?? '';
    $cidade_pagador = $_POST['cidade_pagador'] ?? '';
    $cpf_cnpj_pagador = $_POST['cpf_cnpj_pagador'] ?? '';
    $telefone_pagador = $_POST['telefone_pagador'] ?? '';
    $celular_pagador = $_POST['celular_pagador'] ?? '';
    
    // Dados do curso
    $cursos = $_POST['cursos'] ?? [];
    $turma = $_POST['turma'] ?? null;
    $turmas_vip = $_POST['turmas_vip'] ?? [];
    $duracao = $_POST['duracao'] ?? '';
    $carga_horaria = $_POST['carga_horaria'] ?? '';
    $inicio_aulas = $_POST['inicio_aulas'] ?? '';
    $termino_aulas = $_POST['termino_aulas'] ?? '';
    $dias_semana = $_POST['dias_semana'] ?? '';
    $horario = $_POST['horario'] ?? '';
    
    // Dados financeiros
    $entrada = $_POST['entrada'] ?? 0;
    $parcela_integral = $_POST['parcela_integral'] ?? 0;
    $desconto_pontualidade = $_POST['desconto_pontualidade'] ?? 0;
    $parcela_com_desconto = $_POST['parcela_com_desconto'] ?? 0;
    $parcela_material = $_POST['parcela_material'] ?? 0;
    $qtd_meses = $_POST['qtd_meses'] ?? 0;
    $data_vencimento = $_POST['data_vencimento'] ?? 10;
    
    // Campo de observações
    $observacoes = $_POST['observacoes'] ?? '';
    
    // Converter datas para formato MySQL
    $data_nascimento_aluno_mysql = '';
    if ($data_nascimento_aluno) {
        $data_parts = explode('/', $data_nascimento_aluno);
        if (count($data_parts) == 3) {
            $data_nascimento_aluno_mysql = $data_parts[2] . '-' . $data_parts[1] . '-' . $data_parts[0];
        }
    }
    
    $data_nascimento_pagador_mysql = '';
    if ($data_nascimento_pagador) {
        $data_parts = explode('/', $data_nascimento_pagador);
        if (count($data_parts) == 3) {
            $data_nascimento_pagador_mysql = $data_parts[2] . '-' . $data_parts[1] . '-' . $data_parts[0];
        }
    }
    
    $inicio_aulas_mysql = '';
    if ($inicio_aulas) {
        $data_parts = explode('/', $inicio_aulas);
        if (count($data_parts) == 3) {
            $inicio_aulas_mysql = $data_parts[2] . '-' . $data_parts[1] . '-' . $data_parts[0];
        }
    }
    
    $termino_aulas_mysql = '';
    if ($termino_aulas) {
        $data_parts = explode('/', $termino_aulas);
        if (count($data_parts) == 3) {
            $termino_aulas_mysql = $data_parts[2] . '-' . $data_parts[1] . '-' . $data_parts[0];
        }
    }
    
    // Verificar se aluno já existe (usando a coluna correta: cpf)
    $stmt = $pdo->prepare("SELECT id FROM alunos WHERE cpf = ? OR nome = ?");
    $stmt->execute([$cpf_cnpj_aluno, $nome_aluno]);
    $aluno_existente = $stmt->fetch();
    
    if ($aluno_existente) {
        $aluno_id = $aluno_existente['id'];
        
        // Atualizar dados do aluno existente
        $stmt = $pdo->prepare("
            UPDATE alunos SET 
                nome = ?, data_nascimento = ?, telefone = ?, cpf = ?, 
                responsavel = ?, telefone_responsavel = ?, email = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $nome_aluno, $data_nascimento_aluno_mysql, $telefone_aluno, $cpf_cnpj_aluno,
            $nome_responsavel, $telefone_responsavel, '', $aluno_id
        ]);
    } else {
        // Inserir novo aluno
        $stmt = $pdo->prepare("
            INSERT INTO alunos (
                nome, data_nascimento, telefone, cpf, responsavel, telefone_responsavel, 
                data_cadastro, status
            ) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'ativo')
        ");
        $stmt->execute([
            $nome_aluno, $data_nascimento_aluno_mysql, $telefone_aluno, $cpf_cnpj_aluno,
            $nome_responsavel, $telefone_responsavel
        ]);
        $aluno_id = $pdo->lastInsertId();
    }
    
    // Inserir contrato
    $stmt = $pdo->prepare("
        INSERT INTO contratos (
            aluno_id, nome_aluno, data_nascimento_aluno, estado_civil, profissao_aluno,
            sexo, endereco_aluno, cep_aluno, cidade_aluno, telefone_aluno, cpf_cnpj_aluno,
            nome_responsavel, telefone_responsavel, nome_pagador, data_nascimento_pagador,
            endereco_pagador, cpf_cnpj_pagador, profissao_pagador, bairro_pagador,
            cidade_pagador, telefone_pagador, celular_pagador, curso, duracao, carga_horaria,
            inicio_aulas, termino_aulas, dias_semana, horario, entrada, parcela_integral,
            desconto_pontualidade, parcela_com_desconto, parcela_material, qtd_meses,
            observacoes, turma_id, consolidado
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0
        )
    ");
    
    // Preparar dados para o curso
    $cursos_string = '';
    if (!empty($cursos)) {
        // Buscar nomes dos cursos
        $cursos_nomes = [];
        foreach ($cursos as $curso_id) {
            $stmt_curso = $pdo->prepare("SELECT nome FROM cursos WHERE id = ?");
            $stmt_curso->execute([$curso_id]);
            $curso_nome = $stmt_curso->fetchColumn();
            if ($curso_nome) {
                $cursos_nomes[] = $curso_nome;
            }
        }
        $cursos_string = implode(', ', $cursos_nomes);
    }
    
    // Determinar turma_id baseado no tipo de aluno
    $turma_id_final = null;
    if ($tipo_aluno === 'vip' && !empty($turmas_vip)) {
        $turma_id_final = $turmas_vip[0]; // Usar a primeira turma para o contrato
    } elseif ($turma) {
        $turma_id_final = $turma;
    }
    
    $stmt->execute([
        $aluno_id, $nome_aluno, $data_nascimento_aluno_mysql, $estado_civil, $profissao_aluno,
        $sexo, $endereco_aluno, $cep_aluno, $cidade_aluno, $telefone_aluno, $cpf_cnpj_aluno,
        $nome_responsavel, $telefone_responsavel, $nome_pagador, $data_nascimento_pagador_mysql,
        $endereco_pagador, $cpf_cnpj_pagador, $profissao_pagador, $bairro_pagador,
        $cidade_pagador, $telefone_pagador, $celular_pagador, $cursos_string, $duracao, $carga_horaria,
        $inicio_aulas_mysql, $termino_aulas_mysql, $dias_semana, $horario, $entrada, $parcela_integral,
        $desconto_pontualidade, $parcela_com_desconto, $parcela_material, $qtd_meses,
        $observacoes, $turma_id_final
    ]);
    
    $contrato_id = $pdo->lastInsertId();
    
    // Associar aluno às turmas
    if ($tipo_aluno === 'vip' && !empty($turmas_vip)) {
        foreach ($turmas_vip as $turma_id) {
            // Verificar se já existe associação
            $stmt = $pdo->prepare("SELECT id FROM alunos_turmas WHERE aluno_id = ? AND turma_id = ?");
            $stmt->execute([$aluno_id, $turma_id]);
            
            if (!$stmt->fetch()) {
                $stmt = $pdo->prepare("
                    INSERT INTO alunos_turmas (aluno_id, turma_id, ativo, data_associacao) 
                    VALUES (?, ?, 1, NOW())
                ");
                $stmt->execute([$aluno_id, $turma_id]);
            }
        }
    } elseif ($turma) {
        // Verificar se já existe associação
        $stmt = $pdo->prepare("SELECT id FROM alunos_turmas WHERE aluno_id = ? AND turma_id = ?");
        $stmt->execute([$aluno_id, $turma]);
        
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare("
                INSERT INTO alunos_turmas (aluno_id, turma_id, ativo, data_associacao) 
                VALUES (?, ?, 1, NOW())
            ");
            $stmt->execute([$aluno_id, $turma]);
        }
    }
    
    $pdo->commit();
    
    // Redirecionar com sucesso
    header('Location: precadastro.php?sucesso=1&contrato_id=' . $contrato_id);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    
    echo "<div style='padding: 20px; font-family: Arial;'>";
    echo "<h2 style='color: #d32f2f;'>Erro ao salvar contrato</h2>";
    echo "<p><strong>Mensagem:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Arquivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Linha:</strong> " . $e->getLine() . "</p>";
    echo "<hr>";
    echo "<a href='precadastro.php' style='background: #1976d2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>← Voltar ao Formulário</a>";
    echo "</div>";
    
    // Debug adicional se necessário
    if (isset($_GET['debug'])) {
        echo "<hr><h3>Dados POST recebidos:</h3>";
        echo "<pre style='background: #f5f5f5; padding: 15px;'>" . print_r($_POST, true) . "</pre>";
    }
}
?>