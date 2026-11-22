<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'gerar') {
    $token = gerarQr($pdo);
    $_SESSION['qr_token'] = $token;
    responderJson([
        'token'    => $token,
        'link'     => linkConectar($token),
        'restante' => VALIDADE_QR_SEGUNDOS,
    ], 201);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = limpar(filter_input(INPUT_GET, 'token', FILTER_UNSAFE_RAW) ?? '');

    if ($token === '' || $token !== ($_SESSION['qr_token'] ?? '')) {
        responderJson(['erro' => 'QR code inválido.'], 404);
    }

    $qr = buscarQr($pdo, $token);
    if (!$qr || $qr['status'] === 'usado') {
        responderJson(['status' => 'usado']);
    }

    if ($qr['status'] === 'confirmado') {
        if (entrarPeloQr($pdo, $qr)) {
            unset($_SESSION['qr_token']);
            responderJson(['status' => 'conectado']);
        }
        responderJson(['status' => 'usado']);
    }

    if ($qr['restante'] <= 0) {
        responderJson(['status' => 'expirado']);
    }

    responderJson(['status' => 'aguardando', 'restante' => $qr['restante']]);
}

responderJson(['erro' => 'Requisição inválida.'], 400);
