<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

exigirUsuarioApi();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conversas = array_map(function ($c) {
        return [
            'id'        => (int)$c['id'],
            'contatoId' => (int)$c['contato_id'],
            'foto'      => fotoContato((int)$c['contato_id']),
            'nome'      => $c['nome'],
            'telefone'  => $c['telefone'],
            'grupo'     => (bool)$c['grupo'],
            'fixada'    => (bool)$c['fixada'],
            'favorita'  => (bool)$c['favorita'],
            'bloqueado' => (bool)$c['bloqueado'],
            'naoLidas'  => (int)$c['nao_lidas'],
            'ultima'    => $c['ultima_em'] === null ? null : [
                'texto'   => $c['ultima_apagada'] ? '' : ($c['ultima_tipo'] === 'imagem' ? '📷 Foto' : ($c['ultima_tipo'] === 'documento' ? '📄 ' . $c['ultima_texto'] : $c['ultima_texto'])),
                'apagada' => (bool)$c['ultima_apagada'],
                'hora'    => date('H:i', strtotime($c['ultima_em'])),
            ],
        ];
    }, listarConversas($pdo));

    responderJson(['conversas' => $conversas]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = limpar($_POST['acao'] ?? '');
    $id = (int)filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $conversa = buscarConversaPorId($pdo, $id);

    if (!$conversa) {
        responderJson(['erro' => 'Conversa não encontrada.'], 404);
    }

    try {
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
