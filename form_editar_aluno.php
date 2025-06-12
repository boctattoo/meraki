<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID de aluno não informado.");

$stmt = $pdo->prepare("SELECT * FROM alunos WHERE id = ?");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$aluno) die("Aluno não encontrado.");

function inputVal($array, $key) {
    return isset($array[$key]) ? htmlspecialchars($array[$key]) : '';
}

// Carregar dados auxiliares (baseado na estrutura real)
$cursos = $pdo->query("SELECT id, nome FROM cursos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$turmas = $pdo->query("SELECT id, nome FROM turmas WHERE status = 'ativa' ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Buscar turmas do aluno
$stmt = $pdo->prepare("SELECT turma_id FROM alunos_turmas WHERE aluno_id = ? AND ativo = 1");
$stmt->execute([$id]);
$turmas_aluno = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Aluno</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .loading { display: none; }
    .alert-container { position: fixed; top: 20px; right: 20px; z-index: 1050; }
  </style>
</head>
<body>

<!-- Container de alertas -->
<div class="alert-container" id="alertContainer"></div>

<div class="container mt-4">
  <div class="row">
    <div class="col-lg-8 mx-auto">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h3 class="mb-0">Editar Aluno: <?= htmlspecialchars($aluno['nome']) ?></h3>
        </div>
        <div class="card-body">
          
          <form id="form-editar" class="row g-3">
            <input type="hidden" name="id" value="<?= $aluno['id'] ?>">
            
            <!-- Dados Básicos -->
            <div class="col-12">
              <h5 class="text-secondary mb-3">Dados Básicos</h5>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Nome *</label>
              <input type="text" name="nome" class="form-control" value="<?= inputVal($aluno, 'nome') ?>" required>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Telefone</label>
              <input type="tel" name="telefone" class="form-control" value="<?= inputVal($aluno, 'telefone') ?>">
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="<?= inputVal($aluno, 'email') ?>">
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Data de Nascimento</label>
              <input type="date" name="data_nascimento" class="form-control" value="<?= inputVal($aluno, 'data_nascimento') ?>">
            </div>
            
            <!-- Responsável -->
            <div class="col-12 mt-4">
              <h5 class="text-secondary mb-3">Responsável</h5>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Nome do Responsável *</label>
              <input type="text" name="responsavel" class="form-control" value="<?= inputVal($aluno, 'responsavel') ?>" required>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Telefone do Responsável</label>
              <input type="tel" name="telefone_responsavel" class="form-control" value="<?= inputVal($aluno, 'telefone_responsavel') ?>">
            </div>
            
            <!-- Curso e Status -->
            <div class="col-12 mt-4">
              <h5 class="text-secondary mb-3">Curso e Status</h5>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Curso</label>
              <select name="curso_id" class="form-select">
                <option value="">Selecione um curso...</option>
                <?php foreach ($cursos as $curso): ?>
                  <option value="<?= $curso['id'] ?>" <?= $aluno['curso_id'] == $curso['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($curso['nome']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <div class="mt-2">
                <?php 
                $status_opcoes = ['Ativo', 'Trancado', 'Cancelado'];
                foreach ($status_opcoes as $status): 
                ?>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" 
                           id="status_<?= $status ?>" value="<?= $status ?>" 
                           <?= $aluno['status'] === $status ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status_<?= $status ?>">
                      <?= $status ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            
            <!-- Turmas -->
            <div class="col-12 mt-4">
              <h5 class="text-secondary mb-3">Turmas</h5>
            </div>
            
            <div class="col-12">
              <label class="form-label">Selecionar Turmas</label>
              <select name="turmas_id[]" class="form-select" multiple size="6">
                <?php foreach ($turmas as $turma): ?>
                  <option value="<?= $turma['id'] ?>" <?= in_array($turma['id'], $turmas_aluno) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($turma['nome']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Segure Ctrl para selecionar múltiplas turmas</div>
            </div>
            
            <!-- Botões -->
            <div class="col-12 text-end mt-4">
              <button type="button" class="btn btn-secondary me-2" onclick="history.back()">
                Cancelar
              </button>
              <button type="submit" class="btn btn-success">
                <span class="loading spinner-border spinner-border-sm me-1" role="status"></span>
                Salvar Alterações
              </button>
            </div>
            
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("form-editar");
  const btnSubmit = form.querySelector('button[type="submit"]');
  const loading = btnSubmit.querySelector('.loading');
  
  form.addEventListener("submit", function(e) {
    e.preventDefault();
    
    // Mostrar loading
    loading.style.display = 'inline-block';
    btnSubmit.disabled = true;
    
    // Coletar dados do formulário
    const dados = {
      id: form.querySelector("[name='id']").value,
      nome: form.querySelector("[name='nome']").value,
      telefone: form.querySelector("[name='telefone']").value,
      email: form.querySelector("[name='email']").value,
      data_nascimento: form.querySelector("[name='data_nascimento']").value,
      responsavel: form.querySelector("[name='responsavel']").value,
      telefone_responsavel: form.querySelector("[name='telefone_responsavel']").value,
      curso_id: form.querySelector("[name='curso_id']").value,
      status: form.querySelector("input[name='status']:checked")?.value || 'Ativo',
      turmas_id: Array.from(form.querySelector("[name='turmas_id[]']").selectedOptions).map(opt => opt.value)
    };
    
    // Enviar dados
    fetch("editar_aluno.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(dados)
    })
    .then(res => res.json())
    .then(data => {
      // Esconder loading
      loading.style.display = 'none';
      btnSubmit.disabled = false;
      
      // Mostrar resultado
      mostrarAlerta(data.message, data.success ? 'success' : 'danger');
      
      // Se sucesso, redirecionar após 2 segundos
      if (data.success) {
        setTimeout(() => {
          window.location.href = 'lista_alunos.php';
        }, 2000);
      }
    })
    .catch(err => {
      console.error("Erro:", err);
      loading.style.display = 'none';
      btnSubmit.disabled = false;
      mostrarAlerta("Erro ao enviar os dados. Tente novamente.", 'danger');
    });
  });
  
  function mostrarAlerta(mensagem, tipo) {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = 'alert-' + Date.now();
    
    const alertHTML = `
      <div class="alert alert-${tipo} alert-dismissible fade show" id="${alertId}" role="alert">
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-remover após 5 segundos
    setTimeout(() => {
      const alert = document.getElementById(alertId);
      if (alert) alert.remove();
    }, 5000);
  }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>