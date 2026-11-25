<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

exigirUsuarioApi();

$meuId = (int)usuarioLogado()['id'];

function previaUltima(array $c): ?array
{
    if ($c['ultima_em'] === null) {
        return null;
    }
    if ($c['ultima_apagada']) {
        $texto = '';
    } elseif ($c['ultima_tipo'] === 'imagem') {
        $texto = '📷 Foto';
    } elseif ($c['ultima_tipo'] === 'documento') {
        $texto = '📄 ' . $c['ultima_texto'];
    } else {
        $texto = $c['ultima_texto'];
    }
    return ['texto' => $texto, 'apagada' => (bool)$c['ultima_apagada'], 'hora' => date('H:i', strtotime($c['ultima_em']))];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lista = [];

    foreach (listarConversas($pdo, $meuId) as $c) {
        $lista[] = [
            'id'        => (int)$c['id'],
            'usuario'   => false,
            'contatoId' => (int)$c['contato_id'],
            'foto'      => fotoContato((int)$c['contato_id']),
            'nome'      => $c['nome'],
            'telefone'  => $c['telefone'],
            'grupo'     => (bool)$c['grupo'],
            'fixada'    => (bool)$c['fixada'],
            'favorita'  => (bool)$c['favorita'],
            'bloqueado' => (bool)$c['bloqueado'],
            'naoLidas'  => (int)$c['nao_lidas'],
            'ultima'    => previaUltima($c),
            'ordem'     => $c['ultima_em'] ? strtotime($c['ultima_em']) : 0,
        ];
    }

    foreach (listarChats($pdo, $meuId) as $c) {
        $lista[] = [
            'id'        => 'u' . $c['id'],
            'usuario'   => true,
            'contatoId' => 0,
            'foto'      => 'img/avatares/usuario.svg',
            'nome'      => $c['nome'],
            'telefone'  => $c['telefone'],
            'recado'    => $c['recado'],
            'grupo'     => false,
            'fixada'    => false,
            'favorita'  => false,
            'bloqueado' => false,
            'naoLidas'  => (int)$c['nao_lidas'],
            'ultima'    => previaUltima($c),
            'ordem'     => $c['ultima_em'] ? strtotime($c['ultima_em']) : time(),
        ];
    }

    usort($lista, function ($a, $b) {
        return [$b['fixada'], $b['ordem']] <=> [$a['fixada'], $a['ordem']];
    });

    responderJson(['conversas' => $lista]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = limpar($_POST['acao'] ?? '');

    try {
        if ($acao === 'iniciar') {
            $chatId = abrirChatPorTelefone($pdo, $meuId, limpar($_POST['telefone'] ?? ''));
            responderJson(['ok' => true, 'id' => 'u' . $chatId]);
        }

        [$tipo, $id] = idDaConversa(limpar($_POST['id'] ?? ''));

        if ($tipo === 'usuario') {
            if (!buscarChat($pdo, $id, $meuId)) {
                responderJson(['erro' => 'Conversa não encontrada.'], 404);
            }
            if ($acao !== 'ler') {
                responderJson(['erro' => 'Fixar e favoritar valem só para as conversas com contatos.'], 422);
            }
            marcarChatComoLido($pdo, $id, $meuId);
            responderJson(['ok' => true]);
        }

        $conversa = buscarConversaPorId($pdo, $id, $meuId);
        if (!$conversa) {
            responderJson(['erro' => 'Conversa não encontrada.'], 404);
        }

        if ($acao === 'fixar') {
            alternarFixada($pdo, $conversa);
        } elseif ($acao === 'favoritar') {
            alternarFavorita($pdo, $conversa);
        } elseif ($acao === 'ler') {
            marcarComoLida($pdo, $id);
        } else {
            responderJson(['erro' => 'Ação inválida.'], 400);
        }
    } catch (DomainException $e) {
        responderJson(['erro' => $e->getMessage()], 422);
    }

    responderJson(['ok' => true]);
}

responderJson(['erro' => 'Método não permitido.'], 405);
