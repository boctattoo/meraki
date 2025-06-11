
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Sistema Meraki</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="cadastro_aluno.php">Cadastrar Aluno</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="buscar_aluno.php">Buscar/Editar Aluno</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="mapa_turmas.php">Mapa de Turmas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="atualizar_turmas_aluno.php">Ajuste de Turma</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="lista_presenca.php">Lista de Presença</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="precadastro.php">Pré-Cadastro/Contrato</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="visualizar_contratos.php">Visualizar Contratos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="kanban.php">Kanban de Tarefas</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link text-white" href="logout.php">Sair</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div id="chat-meraki" style="position: fixed; bottom: 100px; right: 20px; width: 260px; background: #fff; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: none; z-index: 9999; overflow: hidden;">
  <div style="background: #25D366; color: white; padding: 10px; font-weight: bold;">Merakinho 🤖</div>
  <div id="chat-log" style="padding: 10px; height: 150px; overflow-y: auto; font-size: 14px;"></div>
  <input type="text" id="chat-input" placeholder="Digite sua dúvida..." style="width: 100%; border: none; border-top: 1px solid #ddd; padding: 10px;">
</div>

<script>
const bot = document.querySelector('.mascote-whatsapp');
const chat = document.getElementById('chat-meraki');
const input = document.getElementById('chat-input');
const log = document.getElementById('chat-log');

bot?.addEventListener('click', e => {
  e.preventDefault();
  chat.style.display = chat.style.display === 'none' ? 'block' : 'none';
});

input?.addEventListener('keypress', e => {
  if (e.key === 'Enter') {
    const pergunta = input.value.trim();
    if (!pergunta) return;
    log.innerHTML += `<div><strong>Você:</strong> ${pergunta}</div>`;
    input.value = '';
    fetch('buscar_resposta.php?pergunta=' + encodeURIComponent(pergunta))
      .then(res => res.text())
      .then(resp => {
        log.innerHTML += `<div><strong>Merakinho:</strong> ${resp}</div>`;
        log.scrollTop = log.scrollHeight;
      });
  }
});
</script>