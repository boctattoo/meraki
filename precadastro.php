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

// Buscar turmas e cursos
$cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$turmas = $pdo->query("SELECT id, nome, vagas FROM turmas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pré-Cadastro - Microlins Bauru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #1a237e;
      --secondary-color: #3f51b5;
      --success-color: #4caf50;
      --warning-color: #ff9800;
      --danger-color: #f44336;
    }

    body { 
      background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .main-form { 
      max-width: 1000px; 
      margin: 30px auto; 
      background: #fff; 
      padding: 40px; 
      border-radius: 15px; 
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      position: relative;
    }

    .main-form::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    }

    .section {
      margin-bottom: 30px;
      padding: 25px;
      border-radius: 10px;
      background: #fafafa;
      border-left: 4px solid var(--primary-color);
    }

    .form-section-title {
      font-size: 1.25em;
      font-weight: 600;
      margin-bottom: 20px;
      color: var(--primary-color);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-control, .form-select {
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      padding: 12px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 0.2rem rgba(63, 81, 181, 0.25);
    }

    .btn-primary {
      background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
      border: none;
      padding: 15px 40px;
      font-size: 18px;
      font-weight: 600;
      border-radius: 25px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(26, 35, 126, 0.3);
    }

    .step-indicator {
      display: flex;
      justify-content: center;
      margin-bottom: 30px;
      align-items: center;
    }

    .step {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #e0e0e0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #666;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .step.active {
      background: var(--primary-color);
      color: white;
      transform: scale(1.1);
    }

    .step.completed {
      background: var(--success-color);
      color: white;
    }

    .step-line {
      width: 80px;
      height: 2px;
      background: #e0e0e0;
      transition: all 0.3s ease;
    }

    .step-line.completed {
      background: var(--success-color);
    }

    .progress {
      height: 8px;
      border-radius: 4px;
      margin-bottom: 30px;
    }

    .progress-bar {
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
      transition: width 0.6s ease;
    }

    /* Sistema de etapas simplificado */
    .form-step {
      display: none;
    }

    .form-step.show {
      display: block !important;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .required::after {
      content: ' *';
      color: var(--danger-color);
      font-weight: bold;
    }

    .curso-item {
      background: white;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      border: 2px solid #e0e0e0;
    }

    .alert {
      border-radius: 10px;
      border: none;
      padding: 15px 20px;
      margin-bottom: 20px;
    }

    .form-check {
      padding: 8px 12px;
      border: 1px solid #e0e0e0;
      border-radius: 6px;
      margin-bottom: 8px;
      transition: all 0.3s ease;
    }

    .form-check:hover {
      background-color: #f8f9fa;
      border-color: var(--secondary-color);
    }

    .form-check-input:checked + .form-check-label {
      color: var(--primary-color);
      font-weight: 600;
    }

    .turma-vip-section {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      border: 2px dashed #dee2e6;
    }

    .badge-vip {
      background: linear-gradient(45deg, #ffd700, #ffed4e);
      color: #000;
      font-weight: bold;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.8em;
    }

    /* Estilo para o campo de observações */
    .observacoes-section {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      border: 2px dashed #dee2e6;
      margin-top: 20px;
    }

    .observacoes-section textarea {
      resize: vertical;
      min-height: 100px;
    }

    @media (max-width: 768px) {
      .main-form { 
        padding: 20px; 
        margin: 15px;
      }
      
      .section {
        padding: 15px;
      }

      .form-check {
        margin-bottom: 10px;
      }
    }
  </style>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="container">
  <form class="main-form shadow" id="contratoForm" action="salvar_contrato.php" method="POST" autocomplete="off">
    
    <!-- Campo hidden para tipo de aluno -->
    <input type="hidden" name="tipo_aluno" id="tipo_aluno_hidden" value="regular">
    
    <!-- Indicador de Progresso -->
    <div class="step-indicator">
      <div class="step active" id="step-1">1</div>
      <div class="step-line" id="line-1"></div>
      <div class="step" id="step-2">2</div>
      <div class="step-line" id="line-2"></div>
      <div class="step" id="step-3">3</div>
      <div class="step-line" id="line-3"></div>
      <div class="step" id="step-4">4</div>
    </div>

    <!-- Barra de Progresso -->
    <div class="progress">
      <div class="progress-bar" id="progressBar" style="width: 25%"></div>
    </div>

    <h2 class="text-center mb-4">
      <i class="fas fa-file-contract me-2"></i>
      Pré-Cadastro / Contrato de Prestação de Serviços
    </h2>

    <!-- Etapa 1: Identificação do Aluno -->
    <div class="form-step show" id="etapa-1">
      <div class="section">
        <h5 class="form-section-title">
          <i class="fas fa-user"></i>
          1. Identificação do Aluno/Empresa
        </h5>
        
        <div class="row">
          <div class="col-md-8 mb-3">
            <label class="required">Nome do Aluno/Empresa</label>
            <input type="text" name="nome_aluno" class="form-control" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="required">Data de Nascimento</label>
            <input type="text" name="data_nascimento_aluno" class="form-control" placeholder="dd/mm/aaaa" required>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-4 mb-3">
            <label>Estado Civil</label>
            <input type="text" name="estado_civil" class="form-control">
          </div>
          <div class="col-md-4 mb-3">
            <label>Profissão</label>
            <input type="text" name="profissao_aluno" class="form-control">
          </div>
          <div class="col-md-4 mb-3">
            <label class="required">Sexo</label>
            <select name="sexo" class="form-select" required>
              <option value="">Selecione</option>
              <option value="Masculino">Masculino</option>
              <option value="Feminino">Feminino</option>
              <option value="Outro">Outro</option>
            </select>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Endereço</label>
            <input type="text" name="endereco_aluno" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label>CEP</label>
            <input type="text" name="cep_aluno" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label>Cidade</label>
            <input type="text" name="cidade_aluno" class="form-control">
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-3 mb-3">
            <label class="required">Telefone</label>
            <input type="text" name="telefone_aluno" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label class="required">CPF/CNPJ</label>
            <input type="text" name="cpf_cnpj_aluno" class="form-control" required>
          </div>
          <div class="col-md-3 mb-3">
            <label>Responsável</label>
            <input type="text" name="nome_responsavel" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label>Tipo de Aluno</label>
            <select name="tipo_aluno_select" class="form-select" id="tipo-aluno" onchange="verificarTipoAluno()">
              <option value="regular">Regular</option>
              <option value="vip">VIP (Múltiplas Turmas)</option>
            </select>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Telefone Responsável</label>
            <input type="text" name="telefone_responsavel" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <!-- Etapa 2: Identificação do Pagador -->
    <div class="form-step" id="etapa-2">
      <div class="section">
        <h5 class="form-section-title">
          <i class="fas fa-credit-card"></i>
          2. Identificação do Pagador
        </h5>
        
        <div class="mb-3">
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copiarDadosAluno()">
            <i class="fas fa-copy me-1"></i>
            Copiar dados do aluno
          </button>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Nome do Pagador</label>
            <input type="text" name="nome_pagador" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label>Data de Nascimento</label>
            <input type="text" name="data_nascimento_pagador" class="form-control" placeholder="dd/mm/aaaa">
          </div>
          <div class="col-md-3 mb-3">
            <label>Profissão</label>
            <input type="text" name="profissao_pagador" class="form-control">
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-4 mb-3">
            <label>Endereço</label>
            <input type="text" name="endereco_pagador" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label>Bairro</label>
            <input type="text" name="bairro_pagador" class="form-control">
          </div>
          <div class="col-md-3 mb-3">
            <label>Cidade</label>
            <input type="text" name="cidade_pagador" class="form-control">
          </div>
          <div class="col-md-2 mb-3">
            <label>CPF/CNPJ</label>
            <input type="text" name="cpf_cnpj_pagador" class="form-control">
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Telefone</label>
            <input type="text" name="telefone_pagador" class="form-control">
          </div>
          <div class="col-md-6 mb-3">
            <label>Celular</label>
            <input type="text" name="celular_pagador" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <!-- Etapa 3: Curso, Prazo e Duração -->
    <div class="form-step" id="etapa-3">
      <div class="section">
        <h5 class="form-section-title">
          <i class="fas fa-graduation-cap"></i>
          3. Curso, Prazo e Duração
        </h5>
        
        <div id="cursos-container">
          <div class="curso-item">
            <div class="row align-items-end">
              <div class="col-md-8">
                <label class="required">Nome do Curso</label>
                <select name="cursos[]" class="form-select curso-select" required onchange="atualizarInfoCursos()">
                  <option value="">Selecione...</option>
                  <?php foreach ($cursos as $c): ?>
                    <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['nome']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-success w-100" onclick="adicionarCurso()">
                  <i class="fas fa-plus me-1"></i>
                  Adicionar Curso
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Seção de Turmas -->
        <div class="mt-4">
          <div id="turma-regular" class="row">
            <div class="col-md-12">
              <label class="required">Turma</label>
              <select name="turma" class="form-select" id="turma-select" required onchange="atualizarInfoTurma()">
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $t): ?>
                  <?php
                    $ocupadas = $pdo->query("SELECT COUNT(*) FROM alunos_turmas WHERE turma_id = {$t['id']} AND ativo = 1")->fetchColumn();
                    $vagas = (int) $t['vagas'];
                    $sem_vagas = $ocupadas >= $vagas;
                    $disabled = $sem_vagas ? 'disabled' : '';
                    $nome_exibido = htmlspecialchars($t['nome']) . ($sem_vagas ? ' (Sem vagas)' : " ({$ocupadas}/{$vagas})");
                  ?>
                  <option value="<?= $t['id'] ?>" <?= $disabled ?> data-dia="<?= htmlspecialchars($t['nome']) ?>"><?= $nome_exibido ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Seção VIP - Múltiplas Turmas -->
          <div id="turmas-vip" class="row mt-3 turma-vip-section" style="display: none;">
            <div class="col-12">
              <h6 class="text-primary mb-3">
                <i class="fas fa-crown text-warning"></i>
                Turmas VIP <span class="badge-vip">PREMIUM</span>
              </h6>
              <label class="required">Selecione múltiplas turmas para aluno VIP</label>
              <div class="row">
                <?php foreach ($turmas as $t): ?>
                  <?php
                    $ocupadas = $pdo->query("SELECT COUNT(*) FROM alunos_turmas WHERE turma_id = {$t['id']} AND ativo = 1")->fetchColumn();
                    $vagas = (int) $t['vagas'];
                    $sem_vagas = $ocupadas >= $vagas;
                    $nome_exibido = htmlspecialchars($t['nome']) . ($sem_vagas ? ' (Sem vagas)' : " ({$ocupadas}/{$vagas})");
                  ?>
                  <div class="col-md-4 mb-2">
                    <div class="form-check">
                      <input class="form-check-input turma-vip-checkbox" type="checkbox" 
                             name="turmas_vip[]" value="<?= $t['id'] ?>" id="turma-<?= $t['id'] ?>"
                             data-dia="<?= htmlspecialchars($t['nome']) ?>" 
                             <?= $sem_vagas ? 'disabled' : '' ?>
                             onchange="atualizarInfoTurmasVIP()">
                      <label class="form-check-label <?= $sem_vagas ? 'text-muted' : '' ?>" for="turma-<?= $t['id'] ?>">
                        <?= $nome_exibido ?>
                      </label>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Alunos VIP podem participar de múltiplas turmas simultaneamente
              </small>
            </div>
          </div>
        </div>
        
        <!-- Informações do Curso -->
        <div class="row mt-4">
          <div class="col-md-3">
            <label>Duração do Curso</label>
            <input type="text" name="duracao" class="form-control" id="duracao" readonly>
          </div>
          <div class="col-md-3">
            <label>Carga Horária</label>
            <input type="text" name="carga_horaria" class="form-control" id="carga-horaria" readonly>
          </div>
          <div class="col-md-3">
            <label>Início das Aulas</label>
            <input type="text" name="inicio_aulas" class="form-control" id="inicio-aulas" readonly>
          </div>
          <div class="col-md-3">
            <label>Término das Aulas</label>
            <input type="text" name="termino_aulas" class="form-control" id="termino-aulas" readonly>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <label>Dias da Semana</label>
            <input type="text" name="dias_semana" class="form-control" id="dias-semana" readonly>
          </div>
          <div class="col-md-6">
            <label>Horário Escolhido</label>
            <input type="text" name="horario" class="form-control" id="horario" readonly>
          </div>
        </div>
      </div>
    </div>

    <!-- Etapa 4: Valor e Condições de Pagamento -->
    <div class="form-step" id="etapa-4">
      <div class="section">
        <h5 class="form-section-title">
          <i class="fas fa-dollar-sign"></i>
          4. Valor e Condições de Pagamento
        </h5>
        
        <div class="row">
          <div class="col-md-4 mb-3">
            <label>Entrada (R$)</label>
            <input type="text" name="entrada" class="form-control" oninput="atualizarCalculos()">
          </div>
          <div class="col-md-4 mb-3">
            <label>Valor da Parcela Integral (R$)</label>
            <input type="text" name="parcela_integral" class="form-control" oninput="calcularDesconto(); atualizarCalculos()">
          </div>
          <div class="col-md-4 mb-3">
            <label>Desconto de Pontualidade (R$)</label>
            <input type="text" name="desconto_pontualidade" class="form-control" oninput="calcularDesconto(); atualizarCalculos()">
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-4 mb-3">
            <label>Parcela com Desconto até Vencimento (R$)</label>
            <input type="text" name="parcela_com_desconto" class="form-control" readonly>
          </div>
          <div class="col-md-4 mb-3">
            <label>Parcela do Material (R$)</label>
            <input type="text" name="parcela_material" class="form-control">
          </div>
          <div class="col-md-2 mb-3">
            <label>Quantidade de Meses</label>
            <input type="number" name="qtd_meses" class="form-control" id="qtd-meses" min="1" max="60" onchange="atualizarTerminoAulas()">
          </div>
          <div class="col-md-2 mb-3">
            <label>Data de Vencimento</label>
            <input type="text" name="data_vencimento" class="form-control" value="10" readonly>
          </div>
        </div>

        <!-- Campo de Observações -->
        <div class="observacoes-section">
          <h6 class="text-primary mb-3">
            <i class="fas fa-comment text-info"></i>
            Observações Gerais
          </h6>
          <textarea name="observacoes" class="form-control" rows="4" 
                    placeholder="Digite aqui observações sobre o contrato, condições especiais, acordos adicionais, informações sobre o aluno, etc..."></textarea>
          <small class="text-muted">
            <i class="fas fa-info-circle"></i>
            Campo opcional para registrar informações complementares sobre o contrato
          </small>
        </div>

        <!-- Resumo do Contrato -->
        <div class="alert alert-info mt-4">
          <h6><i class="fas fa-file-alt me-2"></i>Resumo do Contrato</h6>
          <div id="resumo-contrato">
            <p><strong>Complete todas as etapas para ver o resumo.</strong></p>
          </div>
          <div id="alerta-vip" style="display: none;" class="mt-3 p-3 border border-warning rounded bg-light">
            <h6 class="text-warning mb-2">
              <i class="fas fa-crown"></i> Aluno VIP - Múltiplas Turmas
            </h6>
            <p class="mb-1"><strong>Turmas Selecionadas:</strong> <span id="turmas-vip-lista"></span></p>
            <small class="text-muted">Este aluno terá acesso a múltiplas turmas simultaneamente.</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Navegação -->
    <div class="d-flex justify-content-between mt-4">
      <button type="button" class="btn btn-outline-secondary" id="btnAnterior" onclick="etapaAnterior()" style="display: none;">
        <i class="fas fa-arrow-left me-1"></i>
        Anterior
      </button>
      
      <button type="button" class="btn btn-primary" id="btnProximo" onclick="proximaEtapa()">
        Próximo
        <i class="fas fa-arrow-right ms-1"></i>
      </button>
      
      <button type="submit" class="btn btn-success" id="btnSalvar" style="display: none;">
        <i class="fas fa-save me-1"></i>
        Salvar Contrato
      </button>
    </div>

    <!-- Link para visualizar contratos -->
    <div class="mt-4 text-center">
      <a href="visualizar_contratos.php" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-eye me-1"></i>
        Ver Contratos Registrados
      </a>
    </div>
  </form>
</div>

<script>
let etapaAtual = 1;
const totalEtapas = 4;

// Função para mostrar etapa específica
function mostrarEtapa(numeroEtapa) {
  console.log('Mostrando etapa:', numeroEtapa);
  
  // Esconder todas as etapas
  for (let i = 1; i <= totalEtapas; i++) {
    const etapa = document.getElementById('etapa-' + i);
    if (etapa) {
      etapa.classList.remove('show');
    }
  }
  
  // Mostrar etapa atual
  const etapaAtiva = document.getElementById('etapa-' + numeroEtapa);
  if (etapaAtiva) {
    etapaAtiva.classList.add('show');
  }
  
  // Atualizar indicadores
  atualizarIndicadores();
  atualizarBotoes();
  atualizarProgresso();
}

function atualizarIndicadores() {
  for (let i = 1; i <= totalEtapas; i++) {
    const step = document.getElementById('step-' + i);
    const line = document.getElementById('line-' + i);
    
    if (step) {
      step.classList.remove('active', 'completed');
      if (i < etapaAtual) {
        step.classList.add('completed');
      } else if (i === etapaAtual) {
        step.classList.add('active');
      }
    }
    
    if (line && i < etapaAtual) {
      line.classList.add('completed');
    } else if (line) {
      line.classList.remove('completed');
    }
  }
}

function atualizarBotoes() {
  const btnAnterior = document.getElementById('btnAnterior');
  const btnProximo = document.getElementById('btnProximo');
  const btnSalvar = document.getElementById('btnSalvar');
  
  if (btnAnterior) btnAnterior.style.display = etapaAtual > 1 ? 'block' : 'none';
  if (btnProximo) btnProximo.style.display = etapaAtual < totalEtapas ? 'block' : 'none';
  if (btnSalvar) btnSalvar.style.display = etapaAtual === totalEtapas ? 'block' : 'none';
}

function atualizarProgresso() {
  const progressBar = document.getElementById('progressBar');
  if (progressBar) {
    const progresso = (etapaAtual / totalEtapas) * 100;
    progressBar.style.width = progresso + '%';
  }
}

function proximaEtapa() {
  if (validarEtapaAtual()) {
    if (etapaAtual < totalEtapas) {
      etapaAtual++;
      mostrarEtapa(etapaAtual);
      if (etapaAtual === totalEtapas) {
        gerarResumoContrato();
      }
    }
  } else {
    alert('Por favor, preencha todos os campos obrigatórios.');
  }
}

function etapaAnterior() {
  if (etapaAtual > 1) {
    etapaAtual--;
    mostrarEtapa(etapaAtual);
  }
}

function validarEtapaAtual() {
  const etapa = document.getElementById('etapa-' + etapaAtual);
  let valido = true;
  
  if (etapaAtual === 1) {
    // Validar campos obrigatórios da etapa 1
    const camposObrigatorios = etapa.querySelectorAll('[required]');
    camposObrigatorios.forEach(campo => {
      if (!campo.value.trim()) {
        campo.style.borderColor = '#f44336';
        valido = false;
      } else {
        campo.style.borderColor = '#e0e0e0';
      }
    });
  } else if (etapaAtual === 3) {
    // Validar cursos
    const cursoSelecionado = document.querySelector('.curso-select').value;
    if (!cursoSelecionado) {
      alert('Selecione pelo menos um curso.');
      valido = false;
    }
    
    // Validar turmas baseado no tipo de aluno
    const tipoAluno = document.getElementById('tipo-aluno').value;
    if (tipoAluno === 'vip') {
      const turmasVipSelecionadas = document.querySelectorAll('.turma-vip-checkbox:checked');
      if (turmasVipSelecionadas.length === 0) {
        alert('Selecione pelo menos uma turma para aluno VIP.');
        valido = false;
      }
    } else {
      const turmaSelecionada = document.getElementById('turma-select').value;
      if (!turmaSelecionada) {
        alert('Selecione uma turma.');
        valido = false;
      }
    }
  }
  
  return valido;
}

function copiarDadosAluno() {
  const mappings = [
    ['nome_aluno', 'nome_pagador'],
    ['endereco_aluno', 'endereco_pagador'],
    ['cidade_aluno', 'cidade_pagador'],
    ['cpf_cnpj_aluno', 'cpf_cnpj_pagador'],
    ['telefone_aluno', 'telefone_pagador'],
    ['data_nascimento_aluno', 'data_nascimento_pagador'],
    ['profissao_aluno', 'profissao_pagador']
  ];

  mappings.forEach(([origem, destino]) => {
    const campoOrigem = document.querySelector(`[name="${origem}"]`);
    const campoDestino = document.querySelector(`[name="${destino}"]`);
    if (campoOrigem && campoDestino) {
      campoDestino.value = campoOrigem.value;
    }
  });
  
  alert('Dados copiados com sucesso!');
}

function adicionarCurso() {
  const container = document.getElementById('cursos-container');
  const novoCurso = container.querySelector('.curso-item').cloneNode(true);
  
  // Limpar valores
  novoCurso.querySelector('.curso-select').value = '';
  
  // Trocar botão
  const botao = novoCurso.querySelector('button');
  botao.className = 'btn btn-danger w-100';
  botao.innerHTML = '<i class="fas fa-trash me-1"></i> Remover Curso';
  botao.onclick = function() { 
    novoCurso.remove(); 
    atualizarInfoCursos(); 
  };
  
  container.appendChild(novoCurso);
}

function verificarTipoAluno() {
  const tipoAluno = document.getElementById('tipo-aluno').value;
  const turmaRegular = document.getElementById('turma-regular');
  const turmasVip = document.getElementById('turmas-vip');
  
  // Atualizar campo hidden
  document.getElementById('tipo_aluno_hidden').value = tipoAluno;
  
  if (tipoAluno === 'vip') {
    turmaRegular.style.display = 'none';
    turmasVip.style.display = 'block';
    // Remover required do select regular
    document.getElementById('turma-select').removeAttribute('required');
  } else {
    turmaRegular.style.display = 'block';
    turmasVip.style.display = 'none';
    // Adicionar required de volta
    document.getElementById('turma-select').setAttribute('required', 'required');
    // Limpar seleções VIP
    document.querySelectorAll('.turma-vip-checkbox').forEach(cb => cb.checked = false);
  }
  
  // Limpar informações da turma
  limparInfoTurma();
}

function limparInfoTurma() {
  document.getElementById('dias-semana').value = '';
  document.getElementById('horario').value = '';
  document.getElementById('inicio-aulas').value = '';
  document.getElementById('termino-aulas').value = '';
}

function atualizarInfoCursos() {
  console.log('Atualizando informações dos cursos...');
  
  const cursosSelects = document.querySelectorAll('.curso-select');
  const cursosSelecionados = Array.from(cursosSelects)
    .filter(select => select.value)
    .map(select => select.value);

  console.log('Cursos selecionados:', cursosSelecionados);

  if (cursosSelecionados.length > 0) {
    // Carga horária real baseada nos módulos do banco de dados
    const cursosHoras = {
      '1': 176,   // Web Designer
      '2': 144,   // Operador de Dados  
      '3': 176,   // Marketing Digital
      '4': 112,   // Games & Aplicativos
      '5': 160,   // Profissional da Saúde
      '6': 240,   // Soft Skills
      '7': 160,   // Inglês
      '8': 160,   // Design Gráfico
      '9': 160,   // Técnico de TI
      '10': 80,   // Robótica
      '11': 160,  // Assistente Administrativo
      '12': 176,  // ADS
      '13': 112,  // Office Essencial
      '14': 40    // Módulo Personalizado
    };

    const totalHoras = cursosSelecionados.reduce((total, cursoId) => {
      return total + (cursosHoras[cursoId] || 40);
    }, 0);

    // Cálculo realista: 8 horas por semana = 32 horas por mês
    const mesesCalculados = Math.ceil(totalHoras / 8);
    
    console.log('Total de horas:', totalHoras, 'Meses calculados:', mesesCalculados);
    
    document.getElementById('carga-horaria').value = totalHoras + 'h';
    document.getElementById('duracao').value = mesesCalculados + ' meses';
    
    // Só atualizar quantidade de meses se estiver vazio
    const qtdMesesInput = document.getElementById('qtd-meses');
    if (!qtdMesesInput.value || qtdMesesInput.value == '0') {
      qtdMesesInput.value = mesesCalculados;
    }
    
    // Atualizar término se já tiver início
    atualizarTerminoAulas();
  } else {
    // Limpar campos se nenhum curso selecionado
    document.getElementById('carga-horaria').value = '';
    document.getElementById('duracao').value = '';
    // Não limpar qtd_meses para manter valor digitado pelo usuário
  }
}

function atualizarTerminoAulas() {
  const inicioAulas = document.getElementById('inicio-aulas').value;
  const qtdMeses = parseInt(document.getElementById('qtd-meses').value) || 0;
  
  if (inicioAulas && qtdMeses > 0) {
    const dataFinal = calcularDataFinal(inicioAulas, qtdMeses);
    document.getElementById('termino-aulas').value = dataFinal;
    
    // Também atualizar o campo duração para refletir os meses digitados
    document.getElementById('duracao').value = qtdMeses + ' meses';
  }
}

function atualizarInfoTurma() {
  const turmaId = document.getElementById('turma-select').value;
  console.log('Atualizando informações da turma:', turmaId);
  
  if (turmaId) {
    // Informações reais baseadas no banco de dados fornecido
    const turmasInfo = {
      '29': { dia: 'Segunda-feira', horario: '10:00 às 12:00', inicio: '17/06/2025' },
      '30': { dia: 'Segunda-feira', horario: '15:30 às 17:30', inicio: '17/06/2025' },
      '31': { dia: 'Segunda-feira', horario: '17:30 às 19:30', inicio: '17/06/2025' },
      '32': { dia: 'Segunda-feira', horario: '19:30 às 21:30', inicio: '17/06/2025' },
      '33': { dia: 'Terça-feira', horario: '10:00 às 12:00', inicio: '18/06/2025' },
      '34': { dia: 'Terça-feira', horario: '15:30 às 17:30', inicio: '18/06/2025' },
      '35': { dia: 'Terça-feira', horario: '19:30 às 21:30', inicio: '18/06/2025' },
      '36': { dia: 'Quarta-feira', horario: '08:00 às 10:00', inicio: '19/06/2025' },
      '37': { dia: 'Quarta-feira', horario: '13:30 às 15:30', inicio: '19/06/2025' },
      '38': { dia: 'Quarta-feira', horario: '15:30 às 17:30', inicio: '19/06/2025' },
      '39': { dia: 'Quarta-feira', horario: '19:30 às 21:30', inicio: '19/06/2025' },
      '40': { dia: 'Quinta-feira', horario: '10:00 às 12:00', inicio: '20/06/2025' },
      '41': { dia: 'Quinta-feira', horario: '15:30 às 17:30', inicio: '20/06/2025' },
      '42': { dia: 'Quinta-feira', horario: '19:30 às 21:30', inicio: '20/06/2025' },
      '43': { dia: 'Online', horario: 'Flexível', inicio: '16/06/2025' },
      '44': { dia: 'Sábado', horario: '08:00 às 10:00', inicio: '22/06/2025' },
      '45': { dia: 'Sábado', horario: '10:00 às 12:00', inicio: '22/06/2025' },
      '46': { dia: 'Sábado', horario: '10:00 às 12:00', inicio: '22/06/2025' },
      '47': { dia: 'Sábado', horario: '13:00 às 15:00', inicio: '22/06/2025' },
      '48': { dia: 'Sábado', horario: '15:00 às 17:00', inicio: '22/06/2025' },
      '50': { dia: 'Online', horario: 'Flexível', inicio: '16/06/2025' },
      '52': { dia: 'Segunda-feira', horario: '19:00 às 21:00', inicio: '17/06/2025' },
      '53': { dia: 'Terça-feira', horario: '15:30 às 17:30', inicio: '18/06/2025' },
      '55': { dia: 'Sábado', horario: '08:00 às 10:00', inicio: '22/06/2025' },
      '57': { dia: 'Quinta-feira', horario: '10:00 às 12:00', inicio: '20/06/2025' },
      '58': { dia: 'Quinta-feira', horario: '17:30 às 19:30', inicio: '20/06/2025' },
      '59': { dia: 'Terça-feira', horario: '17:30 às 19:30', inicio: '18/06/2025' }
    };

    const info = turmasInfo[turmaId];
    console.log('Info da turma:', info);
    
    if (info) {
      document.getElementById('dias-semana').value = info.dia;
      document.getElementById('horario').value = info.horario;
      document.getElementById('inicio-aulas').value = info.inicio;
      
      // Calcular término baseado na duração
      const duracaoText = document.getElementById('duracao').value;
      if (duracaoText) {
        const meses = parseInt(duracaoText.match(/\d+/)?.[0] || '6');
        const dataFinal = calcularDataFinal(info.inicio, meses);
        document.getElementById('termino-aulas').value = dataFinal;
      }
    } else {
      // Valores padrão se não encontrar a turma
      document.getElementById('dias-semana').value = 'A definir';
      document.getElementById('horario').value = 'A definir';
      document.getElementById('inicio-aulas').value = '01/07/2025';
      
      const meses = parseInt(document.getElementById('qtd-meses').value) || 6;
      const dataFinal = calcularDataFinal('01/07/2025', meses);
      document.getElementById('termino-aulas').value = dataFinal;
    }
  }
}

function atualizarInfoTurmasVIP() {
  const checkboxes = document.querySelectorAll('.turma-vip-checkbox:checked');
  const turmasSelecionadas = Array.from(checkboxes);
  
  console.log('Turmas VIP selecionadas:', turmasSelecionadas.length);
  
  if (turmasSelecionadas.length > 0) {
    // Pegar informações da primeira turma selecionada
    const primeiraTurma = turmasSelecionadas[0];
    const turmaId = primeiraTurma.value;
    
    // Usar a mesma lógica da função atualizarInfoTurma
    const turmasInfo = {
      '29': { dia: 'Segunda-feira', horario: '10:00 às 12:00', inicio: '17/06/2025' },
      '30': { dia: 'Segunda-feira', horario: '15:30 às 17:30', inicio: '17/06/2025' },
      '31': { dia: 'Segunda-feira', horario: '17:30 às 19:30', inicio: '17/06/2025' },
      '32': { dia: 'Segunda-feira', horario: '19:30 às 21:30', inicio: '17/06/2025' },
      '33': { dia: 'Terça-feira', horario: '10:00 às 12:00', inicio: '18/06/2025' },
      '34': { dia: 'Terça-feira', horario: '15:30 às 17:30', inicio: '18/06/2025' },
      '35': { dia: 'Terça-feira', horario: '19:30 às 21:30', inicio: '18/06/2025' },
      '36': { dia: 'Quarta-feira', horario: '08:00 às 10:00', inicio: '19/06/2025' },
      '37': { dia: 'Quarta-feira', horario: '13:30 às 15:30', inicio: '19/06/2025' },
      '38': { dia: 'Quarta-feira', horario: '15:30 às 17:30', inicio: '19/06/2025' },
      '39': { dia: 'Quarta-feira', horario: '19:30 às 21:30', inicio: '19/06/2025' },
      '40': { dia: 'Quinta-feira', horario: '10:00 às 12:00', inicio: '20/06/2025' },
      '41': { dia: 'Quinta-feira', horario: '15:30 às 17:30', inicio: '20/06/2025' },
      '42': { dia: 'Quinta-feira', horario: '19:30 às 21:30', inicio: '20/06/2025' },
      '43': { dia: 'Online', horario: 'Flexível', inicio: '16/06/2025' },
      '44': { dia: 'Sábado', horario: '08:00 às 10:00', inicio: '22/06/2025' },
      '45': { dia: 'Sábado', horario: '10:00 às 12:00', inicio: '22/06/2025' },
      '46': { dia: 'Sábado', horario: '10:00 às 12:00', inicio: '22/06/2025' },
      '47': { dia: 'Sábado', horario: '13:00 às 15:00', inicio: '22/06/2025' },
      '48': { dia: 'Sábado', horario: '15:00 às 17:00', inicio: '22/06/2025' },
      '50': { dia: 'Online', horario: 'Flexível', inicio: '16/06/2025' }
    };
    
    // Coletar todos os dias e horários das turmas selecionadas
    const diasHorarios = turmasSelecionadas.map(checkbox => {
      const turmaId = checkbox.value;
      const info = turmasInfo[turmaId];
      return info ? `${info.dia} ${info.horario}` : 'A definir';
    });
    
    // Encontrar a data de início mais próxima
    const datasInicio = turmasSelecionadas.map(checkbox => {
      const turmaId = checkbox.value;
      const info = turmasInfo[turmaId];
      return info ? info.inicio : '01/07/2025';
    });
    
    const dataInicioMaisProxima = datasInicio.reduce((maisProxima, atual) => {
      const [dia1, mes1, ano1] = maisProxima.split('/').map(x => parseInt(x));
      const [dia2, mes2, ano2] = atual.split('/').map(x => parseInt(x));
      const data1 = new Date(ano1, mes1 - 1, dia1);
      const data2 = new Date(ano2, mes2 - 1, dia2);
      return data1 <= data2 ? maisProxima : atual;
    });
    
    document.getElementById('dias-semana').value = diasHorarios.join(' | ');
    document.getElementById('horario').value = 'Múltiplos horários';
    document.getElementById('inicio-aulas').value = dataInicioMaisProxima;
    
    // Calcular término
    const duracaoText = document.getElementById('duracao').value;
    if (duracaoText) {
      const meses = parseInt(duracaoText.match(/\d+/)?.[0] || '6');
      const dataFinal = calcularDataFinal(dataInicioMaisProxima, meses);
      document.getElementById('termino-aulas').value = dataFinal;
    }
  } else {
    limparInfoTurma();
  }
}

function calcularDataFinal(dataInicio, meses) {
  if (!dataInicio) return '';
  
  const [dia, mes, ano] = dataInicio.split('/').map(x => parseInt(x));
  const data = new Date(ano, mes - 1, dia);
  data.setMonth(data.getMonth() + meses);
  
  return data.toLocaleDateString('pt-BR');
}

function calcularDesconto() {
  const integralInput = document.querySelector('[name="parcela_integral"]');
  const descontoInput = document.querySelector('[name="desconto_pontualidade"]');
  const finalInput = document.querySelector('[name="parcela_com_desconto"]');

  if (integralInput && descontoInput && finalInput) {
    const integral = parseFloat(integralInput.value.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
    const desconto = parseFloat(descontoInput.value.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
    const final = Math.max(integral - desconto, 0);

    finalInput.value = final.toFixed(2);
  }
}

function atualizarCalculos() {
  // Função para atualizar cálculos gerais se necessário
  console.log('Atualizando cálculos...');
}

function gerarResumoContrato() {
  const nomeAluno = document.querySelector('[name="nome_aluno"]').value;
  const cursos = Array.from(document.querySelectorAll('.curso-select'))
    .filter(select => select.value)
    .map(select => select.options[select.selectedIndex].text);
  const duracao = document.getElementById('duracao').value;
  const cargaHoraria = document.getElementById('carga-horaria').value;
  const diasSemana = document.getElementById('dias-semana').value;
  const horario = document.getElementById('horario').value;
  const entrada = document.querySelector('[name="entrada"]').value || '0';
  const parcelaIntegral = document.querySelector('[name="parcela_integral"]').value || '0';
  const parcelaDesconto = document.querySelector('[name="parcela_com_desconto"]').value || '0';
  const qtdMeses = document.getElementById('qtd-meses').value;
  const observacoes = document.querySelector('[name="observacoes"]').value;

  let resumo = `
    <p><strong>Aluno:</strong> ${nomeAluno}</p>
    <p><strong>Curso(s):</strong> ${cursos.join(', ')}</p>
    <p><strong>Duração:</strong> ${duracao} (${cargaHoraria})</p>
    <p><strong>Horários:</strong> ${diasSemana} - ${horario}</p>
    <p><strong>Valores:</strong> Entrada R$ ${entrada} + ${qtdMeses}x de R$ ${parcelaDesconto} (com desconto) ou R$ ${parcelaIntegral} (sem desconto)</p>
  `;

  if (observacoes.trim()) {
    resumo += `<p><strong>Observações:</strong> ${observacoes}</p>`;
  }

  document.getElementById('resumo-contrato').innerHTML = resumo;
}

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM carregado, inicializando formulário...');
  mostrarEtapa(1);
  
  // Configurar máscaras
  configurarMascaras();
  
  // Configurar event listeners para cálculo automático
  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('curso-select')) {
      console.log('Curso mudou, atualizando cálculos...');
      atualizarInfoCursos();
    }
  });
  
  // Adicionar event listener ao select inicial
  const selectInicial = document.querySelector('.curso-select');
  if (selectInicial) {
    selectInicial.addEventListener('change', atualizarInfoCursos);
    console.log('Event listener adicionado ao select inicial');
  }
  
  // Debug: verificar se os cursos estão carregados
  console.log('Número de cursos disponíveis:', document.querySelectorAll('.curso-select option').length);
  
  // Forçar atualização inicial após pequeno delay
  setTimeout(function() {
    const primeiraOpcao = document.querySelector('.curso-select');
    if (primeiraOpcao && primeiraOpcao.value) {
      console.log('Atualizando cálculos iniciais...');
      atualizarInfoCursos();
    }
  }, 500);
});

function configurarMascaras() {
  // Máscara para telefone
  document.querySelectorAll('[name*="telefone"]').forEach(input => {
    input.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        if (value.length < 14) {
          value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        }
      }
      e.target.value = value;
    });
  });

  // Máscara para CPF/CNPJ
  document.querySelectorAll('[name*="cpf_cnpj"]').forEach(input => {
    input.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
      } else {
        value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
      }
      e.target.value = value;
    });
  });

  // Máscara para data
  document.querySelectorAll('[name*="data_nascimento"]').forEach(input => {
    input.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      value = value.replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3');
      e.target.value = value;
    });
  });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>