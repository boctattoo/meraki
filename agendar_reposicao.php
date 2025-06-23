<?php
$aluno_id = filter_input(INPUT_GET, 'aluno_id', FILTER_VALIDATE_INT);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Reposição</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style> body { background-color: #f0f2f5; } .schedule-container { max-width: 700px; margin: 2rem auto; } .time-slot.selected { background-color: #0d6efd !important; color: white !important; } .time-slot.disabled { opacity: 0.5; cursor: not-allowed; } </style>
</head>
<body>
    <div class="container schedule-container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center"><h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Agendamento de Aula de Reposição</h4></div>
            <div class="card-body p-4">
                <div id="alert-container"></div>
                <form id="scheduleForm">
                    <input type="hidden" name="aluno_id" id="aluno_id_form" value="<?php echo $aluno_id; ?>">
                    <div class="mb-3"><label class="form-label">Nome Completo</label><input type="text" name="nome_aluno" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Nº de WhatsApp (com DDD)</label><input type="tel" name="telefone_aluno" class="form-control" placeholder="(00) 00000-0000" required></div>
                    <h5 class="mt-4">Escolha um horário disponível às Sextas-feiras:</h5>
                    <div id="slots-container" class="mt-3"><div class="text-center p-4"><div class="spinner-border text-primary"></div></div></div>
                    <input type="hidden" name="data_reposicao" id="selected_date"><input type="hidden" name="horario_reposicao" id="selected_time">
                    <div class="d-grid mt-4"><button type="submit" class="btn btn-primary btn-lg" disabled>Agendar Reposição</button></div>
                </form>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const alunoId = document.getElementById('aluno_id_form').value;

    function showAlert(message, type = 'danger') {
        document.getElementById('alert-container').innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    }

    function renderSlots(data) {
        const slotsContainer = document.getElementById('slots-container');
        slotsContainer.innerHTML = '';
        if (Object.keys(data).length === 0) {
            slotsContainer.innerHTML = '<div class="alert alert-warning">Não há horários de reposição disponíveis este mês.</div>';
            return;
        }
        for (const [date, slots] of Object.entries(data)) {
            const formattedDate = new Date(date + 'T12:00:00Z').toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            let dateHtml = `<h6 class="mt-3">${formattedDate}</h6><div class="list-group">`;
            for(const slot of slots) {
                dateHtml += `<button type="button" class="list-group-item list-group-item-action time-slot ${slot.vagas <= 0 ? 'disabled' : ''}" data-date="${date}" data-time="${slot.horario}"><div class="d-flex w-100 justify-content-between"><h6 class="mb-1"><i class="far fa-clock me-2"></i>${slot.horario}</h6><small class="badge bg-${slot.vagas > 0 ? 'success' : 'danger'}">${slot.vagas > 0 ? slot.vagas + ' vagas' : 'Lotado'}</small></div></button>`;
            }
            dateHtml += `</div>`;
            slotsContainer.innerHTML += dateHtml;
        }
    }

    function loadAvailableSlots() {
        fetch('reposicao_api.php?action=get_vagas').then(res => res.json()).then(data => {
            if(data.success) renderSlots(data.vagas);
            else showAlert(data.error);
        });
    }

    if (alunoId) {
        fetch(`reposicao_api.php?action=get_aluno_info&aluno_id=${alunoId}`)
        .then(res => res.json())
        .then(data => {
            if(data.success && data.aluno) {
                form.nome_aluno.value = data.aluno.nome;
                form.telefone_aluno.value = data.aluno.telefone;
            }
        });
    }

    document.getElementById('slots-container').addEventListener('click', function(e) {
        const target = e.target.closest('.time-slot');
        if (target && !target.classList.contains('disabled')) {
            document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
            target.classList.add('selected');
            document.getElementById('selected_date').value = target.dataset.date;
            document.getElementById('selected_time').value = target.dataset.time;
            submitButton.disabled = false;
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Agendando...';
        fetch('reposicao_api.php?action=agendar', { method: 'POST', body: new FormData(form) })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                form.reset();
                showAlert('Agendamento realizado! A confirmação será enviada no seu WhatsApp.', 'success');
                window.open(data.whatsapp_link, '_blank');
                loadAvailableSlots();
            } else {
                showAlert(data.error || 'Não foi possível agendar.');
            }
        }).finally(() => {
            submitButton.disabled = true;
            submitButton.innerHTML = 'Agendar Reposição';
        });
    });

    loadAvailableSlots();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
