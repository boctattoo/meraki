<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php'; // Certifique-se de que conexao.php está configurado corretamente
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban | Meraki</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    </head>
<body>

<?php include 'nav.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Painel de Tarefas (Kanban)</h2>

    <div class="row filtros-kanban mb-3">
        <div class="col-md-6 mb-2">
            <input type="text" id="filtroBusca" class="form-control" placeholder="Buscar tarefa por título ou descrição..." onkeyup="filtrarTarefas()">
        </div>
        <div class="col-md-6 text-md-end mb-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarTarefa">Adicionar Tarefa</button>
        </div>
    </div>

    <div class="row" id="quadro-kanban">
        <div class="col-md-4">
            <div class="kanban-column" id="afazer">
                <h4 class="kanban-title">A Fazer</h4>
                <div class="kanban-tarefas"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kanban-column" id="progresso">
                <h4 class="kanban-title">Em Progresso</h4>
                <div class="kanban-tarefas"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kanban-column" id="concluido">
                <h4 class="kanban-title">Concluído</h4>
                <div class="kanban-tarefas"></div>
            </div>
        </div>
    </div>
</div>

<?php include 'modais_kanban.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.js"></script>

<script>
// Armazena todas as tarefas carregadas para facilitar a busca
let todasAsTarefas = {
    afazer: [],
    progresso: [],
    concluido: []
};

// NOVO: Armazena todos os usuários carregados para popular os dropdowns
let todosOsUsuarios = [];

document.addEventListener("DOMContentLoaded", async () => { // Adicionado 'async' aqui
    // Carrega usuários e tarefas ao iniciar a página
    await carregarUsuarios(); // Espera os usuários serem carregados primeiro
    await carregarTarefas(); // Depois carrega as tarefas

    // Inicializa Dragula para as colunas do Kanban
    const dragulaKanban = dragula([
        document.querySelector('#afazer .kanban-tarefas'),
        document.querySelector('#progresso .kanban-tarefas'),
        document.querySelector('#concluido .kanban-tarefas')
    ]);

    // Evento de 'drop' do Dragula: quando uma tarefa é arrastada e solta
    dragulaKanban.on('drop', (el, target, source) => {
        const tarefaId = el.dataset.id;
        const novoStatus = target.closest('.kanban-column').id;
        const statusAnterior = source.closest('.kanban-column').id; // Captura o status de origem

        // Preenche os campos do modal de tratativa e o exibe
        document.getElementById('tratativa_tarefa_id').value = tarefaId;
        document.getElementById('tratativa_status_anterior').value = statusAnterior; // Novo campo
        document.getElementById('tratativa_status_novo').value = novoStatus;
        document.getElementById('tratativa_texto').value = ''; // Limpa o campo de texto
        new bootstrap.Modal(document.getElementById('modalTratativa')).show();
    });

    // Submissão do formulário de tratativa (mover tarefa)
    document.getElementById("formTratativa").addEventListener("submit", async (e) => {
        e.preventDefault(); // Impede o envio padrão do formulário

        const id = document.getElementById('tratativa_tarefa_id').value;
        const status_anterior = document.getElementById('tratativa_status_anterior').value; // Coleta status anterior
        const status_novo = document.getElementById('tratativa_status_novo').value;
        const texto = document.getElementById('tratativa_texto').value.trim();

        if (texto === '') {
            alert('Escreva uma tratativa para a mudança de status.');
            return;
        }

        try {
            const resposta = await fetch('mover_tarefa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, status_anterior, status: status_novo, tratativa: texto }) // Envia status_anterior
            });

            const resultado = await resposta.json();
            if (resultado.sucesso) {
                bootstrap.Modal.getInstance(document.getElementById('modalTratativa')).hide();
                showToast('Tarefa movida com sucesso!', 'success'); // Exibe toast de sucesso
                carregarTarefas(); // Recarrega as tarefas para refletir a mudança
            } else {
                showToast('Erro ao mover tarefa: ' + resultado.erro, 'danger'); // Exibe toast de erro
                console.error('Erro ao mover tarefa:', resultado.erro);
                carregarTarefas(); // Recarrega mesmo com erro para reverter o card ao estado original se o backend falhar
            }
        } catch (error) {
            console.error('Erro na requisição de mover tarefa:', error);
            showToast('Erro de conexão ao mover tarefa. Tente novamente.', 'danger'); // Exibe toast de erro
            carregarTarefas(); // Recarrega em caso de erro de rede
        }
    });

    // Adicionar Tarefa
    document.getElementById("formAdicionarTarefa").addEventListener("submit", async (e) => {
        e.preventDefault();
        const titulo = document.getElementById('adicionar_titulo').value.trim();
        const descricao = document.getElementById('adicionar_descricao').value.trim();
        const data_evento = document.getElementById('adicionar_data').value;
        const prioridade = document.getElementById('adicionar_prioridade').value;
        const etiqueta = document.getElementById('adicionar_etiqueta').value.trim();
        const usuario_atribuido = document.getElementById('adicionar_usuario_atribuido').value; // NOVO: Coleta o ID do usuário atribuído

        if (titulo === '') {
            alert('O título da tarefa é obrigatório.');
            return;
        }

        try {
            const resposta = await fetch('adicionar_tarefa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                // NOVO: Inclui usuario_atribuido no body
                body: JSON.stringify({ titulo, descricao, data_evento, prioridade, etiqueta, usuario_atribuido })
            });

            const resultado = await resposta.json();
            if (resultado.sucesso) {
                bootstrap.Modal.getInstance(document.getElementById('modalAdicionarTarefa')).hide();
                showToast('Tarefa adicionada com sucesso!', 'success'); // Exibe toast
                document.getElementById("formAdicionarTarefa").reset(); // Limpa o formulário
                carregarTarefas();
            } else {
                showToast('Erro ao adicionar tarefa: ' + resultado.erro, 'danger'); // Exibe toast
                console.error('Erro ao adicionar tarefa:', resultado.erro);
            }
        } catch (error) {
            console.error('Erro na requisição de adicionar tarefa:', error);
            showToast('Erro de conexão ao adicionar tarefa. Tente novamente.', 'danger'); // Exibe toast
        }
    });

    // Editar Tarefa
    document.getElementById("formEditarTarefa").addEventListener("submit", async (e) => {
        e.preventDefault();
        const id = document.getElementById('editar_id').value;
        const titulo = document.getElementById('editar_titulo').value.trim();
        const descricao = document.getElementById('editar_descricao').value.trim();
        const data_evento = document.getElementById('editar_data').value;
        const prioridade = document.getElementById('editar_prioridade').value;
        const etiqueta = document.getElementById('editar_etiqueta').value.trim();
        const usuario_atribuido = document.getElementById('editar_usuario_atribuido').value; // NOVO: Coleta o ID do usuário atribuído

        if (titulo === '') {
            alert('O título da tarefa é obrigatório.');
            return;
        }

        try {
            const resposta = await fetch('editar_tarefa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                // NOVO: Inclui usuario_atribuido no body
                body: JSON.stringify({ id, titulo, descricao, data_evento, prioridade, etiqueta, usuario_atribuido })
            });

            const resultado = await resposta.json();
            if (resultado.sucesso) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditarTarefa')).hide();
                showToast('Tarefa atualizada com sucesso!', 'success'); // Exibe toast
                carregarTarefas();
            } else {
                showToast('Erro ao atualizar tarefa: ' + resultado.erro, 'danger'); // Exibe toast
                console.error('Erro ao atualizar tarefa:', resultado.erro);
            }
        } catch (error) {
            console.error('Erro na requisição de editar tarefa:', error);
            showToast('Erro de conexão ao editar tarefa. Tente novamente.', 'danger'); // Exibe toast
        }
    });

    // Excluir Tarefa
    document.getElementById("formExcluirTarefa").addEventListener("submit", async (e) => {
        e.preventDefault();
        const id = document.getElementById('excluir_id').value;

        try {
            const resposta = await fetch('excluir_tarefa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });

            const resultado = await resposta.json();
            if (resultado.sucesso) {
                bootstrap.Modal.getInstance(document.getElementById('modalExcluir')).hide();
                showToast('Tarefa excluída com sucesso!', 'success'); // Exibe toast
                carregarTarefas();
            } else {
                showToast('Erro ao excluir tarefa: ' + resultado.erro, 'danger'); // Exibe toast
                console.error('Erro ao excluir tarefa:', resultado.erro);
            }
        } catch (error) {
            console.error('Erro na requisição de excluir tarefa:', error);
            showToast('Erro de conexão ao excluir tarefa. Tente novamente.', 'danger'); // Exibe toast
        }
    });
}); // Fim do DOMContentLoaded

/**
 * Função para carregar usuários e popular os dropdowns de atribuição.
 */
async function carregarUsuarios() {
    try {
        const resposta = await fetch('carregar_usuarios.php'); // Ou 'get_usuarios.php' se preferir este nome
        const dados = await resposta.json();

        if (dados.sucesso) {
            todosOsUsuarios = dados.usuarios;
            const selectAdd = document.getElementById('adicionar_usuario_atribuido');
            const selectEdit = document.getElementById('editar_usuario_atribuido');

            // Limpa as opções existentes (exceto a "Ninguém")
            selectAdd.innerHTML = '<option value="">Ninguém</option>';
            selectEdit.innerHTML = '<option value="">Ninguém</option>';

            // Adiciona os usuários aos dropdowns
            todosOsUsuarios.forEach(usuario => {
                const optionAdd = document.createElement('option');
                optionAdd.value = usuario.id;
                optionAdd.textContent = usuario.nome;
                selectAdd.appendChild(optionAdd);

                const optionEdit = document.createElement('option');
                optionEdit.value = usuario.id;
                optionEdit.textContent = usuario.nome;
                selectEdit.appendChild(optionEdit);
            });
        } else {
            console.error('Erro ao carregar usuários:', dados.erro);
            showToast('Erro ao carregar lista de usuários.', 'warning');
        }
    } catch (error) {
        console.error('Erro de conexão ao carregar usuários:', error);
        showToast('Erro de conexão ao carregar lista de usuários.', 'warning');
    }
}

/**
 * Função para carregar e exibir as tarefas no Kanban.
 * @returns {Promise<void>}
 */
async function carregarTarefas() {
    try {
        const resposta = await fetch('carregar_tarefas.php');
        const dados = await resposta.json();

        if (!dados.sucesso) {
            showToast(dados.erro, 'danger'); // Exibe toast de erro
            return;
        }

        // Armazena as tarefas carregadas globalmente para a função de filtro
        todasAsTarefas = dados.tarefas;

        // Renderiza as tarefas (inicialmente sem filtro)
        renderizarTarefas(todasAsTarefas);

    } catch (error) {
        console.error('Erro ao carregar tarefas:', error);
        showToast('Não foi possível carregar as tarefas. Verifique a conexão.', 'danger'); // Exibe toast de erro
    }
}

/**
 * Renderiza as tarefas nas colunas do Kanban com base nos dados fornecidos.
 * @param {object} tarefasObj - Objeto contendo as tarefas organizadas por status.
 */
function renderizarTarefas(tarefasObj) {
    ['afazer', 'progresso', 'concluido'].forEach(status => {
        const container = document.querySelector(`#${status} .kanban-tarefas`);
        container.innerHTML = ''; // Limpa o container antes de adicionar as tarefas
        tarefasObj[status].forEach(tarefa => {
            const div = document.createElement('div');
            div.className = 'kanban-card';
            div.dataset.id = tarefa.id;
            div.innerHTML = `
                <div class="fw-bold">${tarefa.titulo}</div>
                <div class="text-muted small">${tarefa.descricao}</div>
                ${tarefa.data_evento ? `<div class="small">Data: ${formatarData(tarefa.data_evento)}</div>` : ''}
                ${tarefa.prioridade ? `<div class="small prioridade-${tarefa.prioridade.toLowerCase()}">Prioridade: ${tarefa.prioridade}</div>` : ''}
                ${tarefa.etiqueta ? `<div class="etiqueta">${tarefa.etiqueta}</div>` : ''}
                ${tarefa.nome_usuario_atribuido ? `<div class="small mt-2"><strong>Atribuído a:</strong> ${tarefa.nome_usuario_atribuido}</div>` : ''} <div class="kanban-actions">
                    <button class="btn-sm btn-info btn-ver-tratativas" title="Ver Tratativas" data-id="${tarefa.id}">👁️</button> <button class="btn-sm btn-secondary btn-editar" title="Editar Tarefa" data-task='${JSON.stringify(tarefa)}'>✏️</button> <button class="btn-sm btn-danger btn-excluir" title="Excluir Tarefa" data-id="${tarefa.id}">🗑️</button> </div>
            `;
            container.appendChild(div);
        });
    });

    // NOVO: Adiciona event listeners usando event delegation para os botões
    document.querySelectorAll('.kanban-tarefas').forEach(container => {
        container.addEventListener('click', (event) => {
            if (event.target.classList.contains('btn-ver-tratativas')) {
                const taskId = event.target.dataset.id;
                abrirModalTratativasHistorico(taskId);
            } else if (event.target.classList.contains('btn-editar')) {
                const taskData = JSON.parse(event.target.dataset.task);
                abrirModalEditar(taskData);
            } else if (event.target.classList.contains('btn-excluir')) {
                const taskId = event.target.dataset.id;
                abrirModalExcluir(taskId);
            }
        });
    });
}

/**
 * Filtra as tarefas exibidas no Kanban com base no texto de busca.
 */
function filtrarTarefas() {
    const termoBusca = document.getElementById('filtroBusca').value.toLowerCase();
    const tarefasFiltradas = {
        afazer: [],
        progresso: [],
        concluido: []
    };

    ['afazer', 'progresso', 'concluido'].forEach(status => {
        todasAsTarefas[status].forEach(tarefa => {
            if (tarefa.titulo.toLowerCase().includes(termoBusca) ||
                tarefa.descricao.toLowerCase().includes(termoBusca) ||
                (tarefa.nome_usuario_atribuido && tarefa.nome_usuario_atribuido.toLowerCase().includes(termoBusca)) || // NOVO: Filtra por nome do usuário
                (tarefa.etiqueta && tarefa.etiqueta.toLowerCase().includes(termoBusca))
            ) {
                tarefasFiltradas[status].push(tarefa);
            }
        });
    });
    renderizarTarefas(tarefasFiltradas);
}

// Funções para abrir modais
function abrirModalEditar(tarefa) {
    document.getElementById('editar_id').value = tarefa.id;
    document.getElementById('editar_titulo').value = tarefa.titulo;
    document.getElementById('editar_descricao').value = tarefa.descricao;
    document.getElementById('editar_data').value = tarefa.data_evento || '';
    document.getElementById('editar_prioridade').value = tarefa.prioridade || '';
    document.getElementById('editar_etiqueta').value = tarefa.etiqueta || '';

    // NOVO: Preenche o dropdown de usuário atribuído
    const selectUsuario = document.getElementById('editar_usuario_atribuido');
    selectUsuario.value = tarefa.usuario_atribuido_id || ''; // Define o valor selecionado

    new bootstrap.Modal(document.getElementById('modalEditarTarefa')).show();
}

function abrirModalExcluir(id) {
    document.getElementById('excluir_id').value = id;
    new bootstrap.Modal(document.getElementById('modalExcluir')).show();
}

// Nova função: Abrir modal de histórico de tratativas
async function abrirModalTratativasHistorico(tarefaId) {
    try {
        const resposta = await fetch(`carregar_tratativas.php?tarefa_id=${tarefaId}`);
        const dados = await resposta.json();

        const modalTratativasBody = document.getElementById('modalTratativasBody');
        modalTratativasBody.innerHTML = ''; // Limpa o conteúdo anterior

        if (dados.sucesso && dados.tratativas.length > 0) {
            dados.tratativas.forEach(tratativa => {
                const div = document.createElement('div');
                div.className = 'tratativa-item';
                div.innerHTML = `
                    <p><strong>De:</strong> ${tratativa.status_anterior} <strong>Para:</strong> ${tratativa.status_novo}</p>
                    <p>${tratativa.texto}</p>
                    <p class="text-muted small">Por ${tratativa.nome_usuario} em ${formatarDataHora(tratativa.data_registro)}</p>
                `;
                modalTratativasBody.appendChild(div);
            });
        } else if (dados.sucesso && dados.tratativas.length === 0) {
            modalTratativasBody.innerHTML = '<p class="text-center text-muted">Nenhuma tratativa registrada para esta tarefa.</p>';
        } else {
            modalTratativasBody.innerHTML = `<p class="text-danger text-center">Erro ao carregar tratativas: ${dados.erro}</p>`;
        }
        // Garante que o modal seja instanciado e exibido
        const historicoModal = new bootstrap.Modal(document.getElementById('modalHistoricoTratativas'));
        historicoModal.show();
    } catch (error) {
        console.error('Erro ao carregar tratativas:', error);
        showToast('Erro de conexão ao carregar histórico de tratativas.', 'danger');
    }
}

/**
 * Formata uma data no formato YYYY-MM-DD para DD/MM/YYYY.
 * @param {string} dataString - A string da data no formato YYYY-MM-DD.
 * @returns {string} A data formatada.
 */
function formatarData(dataString) {
    if (!dataString) return '';
    const [ano, mes, dia] = dataString.split('-');
    return `${dia}/${mes}/${ano}`;
}

/**
 * Formata uma data e hora (timestamp) para um formato legível.
 * @param {string} dateTimeString - A string do timestamp.
 * @returns {string} A data e hora formatada.
 */
function formatarDataHora(dateTimeString) {
    if (!dateTimeString) return '';
    const date = new Date(dateTimeString);
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Exibe um toast de notificação.
 * @param {string} message - A mensagem a ser exibida.
 * @param {string} type - O tipo de toast (e.g., 'success', 'danger', 'warning', 'info').
 */
function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container') || (() => {
        const div = document.createElement('div');
        div.className = 'toast-container';
        document.body.appendChild(div);
        return div;
    })();

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0 fade show`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();

    // Remove o toast do DOM após o tempo de exibição para evitar acúmulo
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>

</body>
</html>