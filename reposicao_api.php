<?php
session_start();
header('Content-Type: application/json');
require 'conexao.php';

// --- CONFIGURAÇÕES ---
define('VAGAS_POR_HORARIO', 15);
$action = $_GET['action'] ?? '';

function json_error($msg) { echo json_encode(['success' => false, 'error' => $msg]); exit; }

switch ($action) {
    case 'get_vagas':
        try {
            $horarios_base = ["08:00 - 10:00", "10:00 - 12:00", "13:00 - 14:00", "15:00 - 17:00", "17:00 - 19:00", "19:00 - 21:00"];
            
            // Busca apenas sextas do mês vigente
            $proximas_sextas = [];
            $data_inicio = new DateTime('first day of this month');
            $data_fim = new DateTime('last day of this month');
            while ($data_inicio <= $data_fim) {
                if ($data_inicio->format('w') == 5) {
                    $proximas_sextas[] = $data_inicio->format('Y-m-d');
                }
                $data_inicio->modify('+1 day');
            }
            
            // Remove a 1ª sexta-feira do mês
            if (!empty($proximas_sextas)) array_shift($proximas_sextas);

            // Busca datas e horários bloqueados
            $stmt_bloqueados = $pdo->prepare("SELECT data_bloqueada, horario_bloqueado FROM reposicoes_horarios_bloqueados");
            $stmt_bloqueados->execute();
            $bloqueios = $stmt_bloqueados->fetchAll(PDO::FETCH_ASSOC);

            $stmt_feriados = $pdo->prepare("SELECT data FROM feriados");
            $stmt_feriados->execute();
            $feriados = $stmt_feriados->fetchAll(PDO::FETCH_COLUMN);

            $stmt_agendados = $pdo->prepare("SELECT data_reposicao, horario_reposicao, COUNT(*) as total FROM reposicoes_agendadas WHERE status = 'agendada' GROUP BY data_reposicao, horario_reposicao");
            $stmt_agendados->execute();
            $agendamentos = $stmt_agendados->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);

            $vagas_disponiveis = [];
            foreach ($proximas_sextas as $data) {
                $dia_inteiro_bloqueado = false;
                foreach($bloqueios as $b) {
                    if ($b['data_bloqueada'] == $data && $b['horario_bloqueado'] === null) {
                        $dia_inteiro_bloqueado = true;
                        break;
                    }
                }

                if ($dia_inteiro_bloqueado || in_array($data, $feriados)) continue;

                $vagas_disponiveis[$data] = [];
                foreach ($horarios_base as $horario) {
                    $horario_especifico_bloqueado = false;
                    foreach($bloqueios as $b) {
                        if ($b['data_bloqueada'] == $data && $b['horario_bloqueado'] === $horario) {
                            $horario_especifico_bloqueado = true;
                            break;
                        }
                    }
                    if($horario_especifico_bloqueado) continue;

                    $agendados_neste_horario = $agendamentos[$data][$horario]['total'] ?? 0;
                    $vagas_restantes = VAGAS_POR_HORARIO - $agendados_neste_horario;
                    $vagas_disponiveis[$data][] = ['horario' => $horario, 'vagas' => $vagas_restantes];
                }
                if(empty($vagas_disponiveis[$data])) unset($vagas_disponiveis[$data]);
            }
            echo json_encode(['success' => true, 'vagas' => $vagas_disponiveis]);
        } catch (Exception $e) { json_error('Erro ao buscar vagas.'); }
        break;

    case 'agendar':
        $aluno_id = filter_input(INPUT_POST, 'aluno_id', FILTER_VALIDATE_INT) ?: null;
        $nome_aluno = filter_input(INPUT_POST, 'nome_aluno', FILTER_SANITIZE_STRING);
        $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone_aluno'] ?? '');
        $data = filter_input(INPUT_POST, 'data_reposicao', FILTER_SANITIZE_STRING);
        $horario = filter_input(INPUT_POST, 'horario_reposicao', FILTER_SANITIZE_STRING);

        if (!$nome_aluno || !$telefone || !$data || !$horario) { json_error('Todos os campos são obrigatórios.'); }

        try {
            $stmt_vagas = $pdo->prepare("SELECT COUNT(*) FROM reposicoes_agendadas WHERE data_reposicao = ? AND horario_reposicao = ? AND status = 'agendada'");
            $stmt_vagas->execute([$data, $horario]);
            if ($stmt_vagas->fetchColumn() >= VAGAS_POR_HORARIO) { json_error('Desculpe, este horário ficou sem vagas. Por favor, escolha outro.'); }

            $stmt_insert = $pdo->prepare("INSERT INTO reposicoes_agendadas (aluno_id, nome_aluno, telefone_aluno, data_reposicao, horario_reposicao) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->execute([$aluno_id, $nome_aluno, $telefone, $data, $horario]);

            $data_formatada = date('d/m/Y', strtotime($data));
            $mensagem = "Olá, {$nome_aluno}! A sua aula de reposição foi agendada com sucesso para o dia {$data_formatada}, no horário das {$horario}. Até lá!";
            $link_whatsapp = "https://wa.me/55{$telefone}?text=" . urlencode($mensagem);
            
            echo json_encode(['success' => true, 'whatsapp_link' => $link_whatsapp]);
        } catch (Exception $e) { json_error('Erro no servidor ao agendar: '. $e->getMessage()); }
        break;
    
    case 'get_aluno_info':
        $id = filter_input(INPUT_GET, 'aluno_id', FILTER_VALIDATE_INT);
        if (!$id) { json_error('ID do aluno inválido.'); }
        
        $stmt = $pdo->prepare("SELECT nome, telefone FROM alunos WHERE id = ?");
        $stmt->execute([$id]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($aluno) {
            echo json_encode(['success' => true, 'aluno' => $aluno]);
        } else {
            json_error('Aluno não encontrado.');
        }
        break;

    default:
        json_error('Ação não reconhecida.');
        break;
}
?>
