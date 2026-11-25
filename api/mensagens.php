<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

exigirUsuarioApi();

$meuId = (int)usuarioLogado()['id'];
$valorConversa = limpar($_SERVER['REQUEST_METHOD'] === 'GET' ? ($_GET['conversa'] ?? '') : ($_POST['conversa'] ?? ''));
[$tipo, $conversaId] = idDaConversa($valorConversa);

if ($tipo === 'usuario' && $valorConversa !== '' && !buscarChat($pdo, $conversaId, $meuId)) {
    responderJson(['erro' => 'Conversa não encontrada.'], 404);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($tipo === 'usuario') {
        responderJson(['mensagens' => array_map('formatarMensagem', listarMensagensChat($pdo, $conversaId, $meuId))]);
    }
    if (!buscarConversaPorId($pdo, $conversaId, $meuId)) {
        responderJson(['erro' => 'Conversa não encontrada.'], 404);
    }
    responderJson(['mensagens' => array_map('formatarMensagem', listarMensagens($pdo, $conversaId))]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = limpar($_POST['acao'] ?? '');
    $conversa = $tipo === 'contato' ? buscarConversaPorId($pdo, $conversaId, $meuId) : null;

    try {
        if ($acao === 'enviar') {
            $texto = limpar($_POST['texto'] ?? '');

            if ($tipo === 'contato' && !$conversa) {
                responderJson(['erro' => 'Conversa não encontrada.'], 404);
            }
            if ($texto === '' || comprimentoTexto($texto) > 1000) {
                responderJson(['erro' => 'Digite uma mensagem com até 1000 caracteres.'], 422);
            }

            $id = $tipo === 'usuario' ? enviarMensagemChat($pdo, $conversaId, $meuId, $texto) : enviarMensagem($pdo, $conversa, $texto);
            responderJson(['ok' => true, 'id' => $id], 201);
        }

        if ($acao === 'arquivo') {
            if ($tipo === 'contato' && !$conversa) {
                responderJson(['erro' => 'Conversa não encontrada.'], 404);
            }
            if (!isset($_FILES['arquivo'])) {
                responderJson(['erro' => 'Escolha um arquivo para enviar.'], 422);
            }
            $id = $tipo === 'usuario' ? enviarArquivoChat($pdo, $conversaId, $meuId, $_FILES['arquivo']) : enviarArquivo($pdo, $conversa, $_FILES['arquivo']);
            responderJson(['ok' => true, 'id' => $id], 201);
        }

        if ($acao === 'apagar') {
            $mensagemId = (int)filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($tipo === 'usuario') {
                apagarMensagemChat($pdo, $mensagemId, $meuId);
            } else {
                apagarParaTodos($pdo, $mensagemId, $meuId);
            }
            responderJson(['ok' => true]);
        }
    } catch (DomainException $e) {
        responderJson(['erro' => $e->getMessage()], 422);
    }

    responderJson(['erro' => 'Ação inválida.'], 400);
}

responderJson(['erro' => 'Método não permitido.'], 405);
