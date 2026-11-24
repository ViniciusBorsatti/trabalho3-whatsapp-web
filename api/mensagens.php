<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

exigirUsuarioApi();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conversaId = (int)filter_input(INPUT_GET, 'conversa', FILTER_VALIDATE_INT);
    if (!buscarConversaPorId($pdo, $conversaId)) {
        responderJson(['erro' => 'Conversa não encontrada.'], 404);
    }
    responderJson(['mensagens' => array_map('formatarMensagem', listarMensagens($pdo, $conversaId))]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = limpar($_POST['acao'] ?? '');

    try {
        if ($acao === 'enviar') {
            $conversa = buscarConversaPorId($pdo, (int)filter_input(INPUT_POST, 'conversa', FILTER_VALIDATE_INT));
            $texto = limpar($_POST['texto'] ?? '');

            if (!$conversa) {
                responderJson(['erro' => 'Conversa não encontrada.'], 404);
            }
            if ($texto === '' || comprimentoTexto($texto) > 1000) {
                responderJson(['erro' => 'Digite uma mensagem com até 1000 caracteres.'], 422);
            }

            $id = enviarMensagem($pdo, $conversa, $texto);
            responderJson(['ok' => true, 'id' => $id], 201);
        }

        if ($acao === 'arquivo') {
            $conversa = buscarConversaPorId($pdo, (int)filter_input(INPUT_POST, 'conversa', FILTER_VALIDATE_INT));
            if (!$conversa) {
                responderJson(['erro' => 'Conversa não encontrada.'], 404);
            }
            if (!isset($_FILES['arquivo'])) {
                responderJson(['erro' => 'Escolha um arquivo para enviar.'], 422);
            }
            $id = enviarArquivo($pdo, $conversa, $_FILES['arquivo']);
            responderJson(['ok' => true, 'id' => $id], 201);
        }

        if ($acao === 'apagar') {
            apagarParaTodos($pdo, (int)filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));
            responderJson(['ok' => true]);
        }
    } catch (DomainException $e) {
        responderJson(['erro' => $e->getMessage()], 422);
    }

    responderJson(['erro' => 'Ação inválida.'], 400);
}

responderJson(['erro' => 'Método não permitido.'], 405);
