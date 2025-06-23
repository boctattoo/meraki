<?php
session_start();
if (!isset($_SESSION['aluno_id'])) {
    header('Location: login_aluno.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Portal do Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .main-content { padding-bottom: 80px; }
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
        }
        .nav-link { color: #6c757d; text-decoration: none; text-align: center; }
        .nav-link.active { color: #0d6efd; font-weight: bold; }
        .nav-link i { font-size: 1.5rem; }
        .view { display: none; }
        .view.active { display: block; }
        .profile-pic-container { position: relative; width: 120px; height: 120px; margin: 0 auto; }
        .profile-pic { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .profile-pic-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); color: white; display: none; align-items: center; justify-content: center; border-radius: 50%; }
        .profile-pic-container.loading .profile-pic-overlay { display: flex; }
    </style>
</head>
<body>

    <div class="main-content container mt-4">
        <div id="alert-placeholder"></div>

        <!-- Perfil -->
        <div id="view-perfil" class="view active">
            <div class="text-center mb-4">
                <div class="profile-pic-container" id="profilePicContainer">
                    <img src="https://i.pravatar.cc/150" class="profile-pic" id="profileImage" alt="Foto de Perfil">
                    <div class="profile-pic-overlay">
                        <div class="spinner-border text-light" role="status"></div>
                    </div>
                </div>
                <h4 class="mt-3" id="alunoNome">Carregando...</h4>
                <input type="file" id="uploadFoto" accept="image/*" style="display:none;">
                <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('uploadFoto').click();"><i class="fas fa-camera me-1"></i> Trocar Foto</button>
            </div>
            <form id="profileForm">
                <div class="mb-3"><label class="form-label">Nome Completo</label><input type="text" class="form-control" name="nome" id="formNome"></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" id="formEmail"></div>
                <div class="mb-3"><label class="form-label">Telefone</label><input type="tel" class="form-control" name="telefone" id="formTelefone"></div>
                <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
            </form>
        </div>

        <!-- Frequência -->
        <div id="view-frequencia" class="view">
             <h4 class="mb-3">Minha Frequência</h4>
             <div class="card text-center mb-4"><div class="card-body"><h5 class="card-title">Aproveitamento Geral</h5><p class="display-4 fw-bold text-success" id="aproveitamentoPercent">-%</p></div></div>
             <ul class="list-group" id="listaFrequencia"></ul>
        </div>

        <!-- Recompensas -->
        <div id="view-recompensas" class="view">
            <h4 class="mb-3">Programa de Pontos</h4>
            <div class="card text-center mb-4 bg-primary text-white"><div class="card-body"><h5 class="card-title">Meus Pontos</h5><p class="display-4 fw-bold" id="meusPontos">0</p></div></div>
             <h5>Recompensas Disponíveis</h5>
             <div id="listaRecompensas"></div>
        </div>
    </div>

    <!-- Menu de Navegação -->
    <nav class="bottom-nav">
        <a href="#perfil" class="nav-link active"><i class="fas fa-user-circle"></i><div>Perfil</div></a>
        <a href="#frequencia" class="nav-link"><i class="fas fa-check-circle"></i><div>Frequência</div></a>
        <a href="#recompensas" class="nav-link"><i class="fas fa-star"></i><div>Recompensas</div></a>
        <a href="#" id="logoutButton" class="nav-link"><i class="fas fa-sign-out-alt"></i><div>Sair</div></a>
    </nav>
    
<script>
document.addEventListener('DOMContentLoaded', function() {
    const views = document.querySelectorAll('.view');
    const navLinks = document.querySelectorAll('.bottom-nav .nav-link');
    const alertPlaceholder = document.getElementById('alert-placeholder');

    function showAlert(message, type = 'danger') {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        alertPlaceholder.innerHTML = ''; // Limpa alertas anteriores
        alertPlaceholder.append(wrapper);
    }

    function switchView(hash) {
        hash = hash.replace('#', '');
        views.forEach(view => view.classList.toggle('active', view.id === `view-${hash}`));
        navLinks.forEach(link => link.classList.toggle('active', link.getAttribute('href') === `#${hash}`));
    }

    window.addEventListener('hashchange', () => switchView(window.location.hash || '#perfil'));
    document.querySelectorAll('.bottom-nav .nav-link:not(#logoutButton)').forEach(link => {
        link.addEventListener('click', (e) => { e.preventDefault(); window.location.hash = e.currentTarget.getAttribute('href'); });
    });

    document.getElementById('logoutButton').addEventListener('click', (e) => {
        e.preventDefault();
        fetch('aluno_api.php?action=logout').then(() => window.location.href = 'login_aluno.php');
    });
    
    function loadDashboardData() {
        fetch('aluno_api.php?action=get_dashboard_data')
        .then(res => res.json())
        .then(data => {
            if (!data.success) { showAlert(data.error); return; }
            
            const { aluno, frequencia, recompensas } = data;
            document.getElementById('alunoNome').textContent = aluno.nome;
            document.getElementById('formNome').value = aluno.nome;
            document.getElementById('formEmail').value = aluno.email;
            document.getElementById('formTelefone').value = aluno.telefone;
            document.getElementById('profileImage').src = aluno.foto_perfil ? aluno.foto_perfil : 'https://i.pravatar.cc/150';

            const listaFrequencia = document.getElementById('listaFrequencia');
            listaFrequencia.innerHTML = frequencia.map(aula => {
                const dataFormatada = new Date(aula.data + 'T12:00:00Z').toLocaleDateString('pt-BR');
                return `<li class="list-group-item d-flex justify-content-between align-items-center">${dataFormatada}<span class="badge bg-${aula.presente == 1 ? 'success' : 'danger'}">${aula.presente == 1 ? 'Presente' : 'Falta'}</span></li>`;
            }).join('');
            const presentes = frequencia.filter(a => a.presente == 1).length;
            document.getElementById('aproveitamentoPercent').textContent = `${frequencia.length > 0 ? Math.round((presentes / frequencia.length) * 100) : 0}%`;

            document.getElementById('meusPontos').textContent = aluno.pontos_fidelidade || 0;
            document.getElementById('listaRecompensas').innerHTML = recompensas.map(rec => `
                <div class="card mb-2"><div class="card-body"><h6 class="card-title">${rec.titulo}</h6><p class="card-text small">${rec.descricao}</p><span class="badge bg-primary">${rec.pontos_necessarios} pontos</span></div></div>`
            ).join('');
        });
    }

    document.getElementById('uploadFoto').addEventListener('change', function(e){
        if (e.target.files.length === 0) return;
        const formData = new FormData();
        formData.append('foto_perfil', e.target.files[0]);
        
        document.getElementById('profilePicContainer').classList.add('loading');

        fetch('aluno_api.php?action=upload_photo', { method: 'POST', body: formData})
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('profileImage').src = data.path + '?' + new Date().getTime();
                showAlert('Foto de perfil atualizada!', 'success');
            } else {
                showAlert(data.error || 'Erro desconhecido ao enviar a foto.');
            }
        })
        .catch(() => showAlert('Erro de conexão. Verifique a sua internet.'))
        .finally(() => document.getElementById('profilePicContainer').classList.remove('loading'));
    });
    
    document.getElementById('profileForm').addEventListener('submit', function(e){
        e.preventDefault();
        fetch('aluno_api.php?action=update_profile', {method: 'POST', body: new FormData(this)})
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert('Dados atualizados com sucesso!', 'success');
                loadDashboardData();
            } else {
                showAlert(data.error || 'Não foi possível atualizar os dados.');
            }
        });
    });

    switchView(window.location.hash || '#perfil');
    loadDashboardData();
});
</script>
</body>
</html>
