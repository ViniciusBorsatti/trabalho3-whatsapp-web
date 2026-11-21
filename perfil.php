<?php
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina = 'WhatsApp Web — Perfil do contato';
$cssPagina = 'css/perfil.css';
$scriptsDefer = ['js/tema.js'];
$tipoRodape = 'educacional';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$contato = null;
$erro = '';
$flash = mensagem();

if (!$id || $id < 1) {
    $erro = 'Contato não encontrado. Informe um identificador válido.';
} else {
    $contato = buscarContatoPorId($pdo, $id);
    if (!$contato) $erro = 'Contato não encontrado.';
}

if ($contato && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = limpar(filter_input(INPUT_POST, 'acao', FILTER_UNSAFE_RAW) ?? '');
    if ($acao === 'favoritar' || $acao === 'bloquear') {
        $campo = $acao === 'favoritar' ? 'favorito' : 'bloqueado';
        $novoEstado = (int)!((bool)$contato[$campo]);
        alternarEstadoContato($pdo, $id, $campo, (bool)$contato[$campo]);
        mensagem('sucesso', $acao === 'favoritar'
            ? ($novoEstado ? 'Contato adicionado aos favoritos.' : 'Contato removido dos favoritos.')
            : ($novoEstado ? 'Contato bloqueado. Ele não poderá receber mensagens.' : 'Contato desbloqueado.'));
        redirecionar('perfil.php?id=' . $id);
    }

    if ($acao === 'denunciar') {
        $motivo = limpar(filter_input(INPUT_POST, 'motivo', FILTER_UNSAFE_RAW) ?? '');
        $descricao = limpar(filter_input(INPUT_POST, 'descricao', FILTER_UNSAFE_RAW) ?? '');
        $bloquearTambem = isset($_POST['bloquearTambem']);
        $motivosPermitidos = ['spam', 'assedio', 'conteudo-improprio', 'perfil-falso', 'outro'];
        if (!in_array($motivo, $motivosPermitidos, true)) {
            $erro = 'Selecione um motivo válido para a denúncia.';
        } elseif (comprimentoTexto($descricao) > 500) {
            $erro = 'A descrição deve ter no máximo 500 caracteres.';
        } else {
            try {
                registrarDenuncia($pdo, $id, $motivo, $descricao, $bloquearTambem);
                mensagem('sucesso', 'Denúncia enviada com sucesso.');
                redirecionar('perfil.php?id=' . $id);
            } catch (Throwable $e) {
                $erro = 'Não foi possível enviar a denúncia. Tente novamente.';
            }
        }
    } else {
        $erro = $erro ?: 'Ação inválida.';
    }
}

if ($contato) {
    $contato = buscarContatoPorId($pdo, $id) ?: $contato;
}

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <header class="barra-superior"><div class="barra-superior-conteudo">
    <div class="titulo-com-voltar"><a href="nova-conversa.php" class="btn-icone" aria-label="Voltar para contatos"><i class="bi bi-arrow-left" aria-hidden="true"></i></a><h1>Dados do contato</h1></div>
    <nav class="nav-atalhos" aria-label="Navegação do site"><a href="conversas.php">Conversas</a><a href="nova-conversa.php">Contatos</a><a href="configuracoes.php">Configurações</a></nav>
  </div></header>
  <main class="perfil-container">
    <?php if ($erro): ?><p class="erro-geral" role="alert"><?= esc($erro) ?></p><?php endif; ?>
    <?php if ($flash): ?><p class="msg-sucesso" role="status"><?= esc($flash['texto']) ?></p><?php endif; ?>
    <?php if ($contato): ?>
    <article class="perfil-painel">
      <section class="perfil-cabecalho" aria-label="Informações principais do contato">
        <img id="perfil-avatar" src="img/avatar-padrao.svg" alt="Avatar padrão de <?= esc($contato['nome']) ?>" class="perfil-avatar" width="140" height="140">
        <h2 id="perfil-nome"><?= esc($contato['nome']) ?></h2>
        <p id="perfil-telefone" class="perfil-telefone"><?= esc($contato['grupo'] ? 'Grupo' : '+55 ' . $contato['telefone']) ?></p>
        <div class="perfil-badges" id="perfil-badges">
          <?php if ($contato['favorito']): ?><span class="badge-status favorito" id="badge-favorito"><i class="bi bi-star-fill" aria-hidden="true"></i> Favorito</span><?php endif; ?>
          <?php if ($contato['bloqueado']): ?><span class="badge-status bloqueado" id="badge-bloqueado"><i class="bi bi-slash-circle" aria-hidden="true"></i> Contato bloqueado (RN03)</span><?php endif; ?>
        </div>
        <div class="perfil-atalhos-rapidos" aria-label="Atalhos visuais do contato"><span class="atalho-visual"><i class="bi bi-telephone" aria-hidden="true"></i><span>Áudio</span></span><span class="atalho-visual"><i class="bi bi-camera-video" aria-hidden="true"></i><span>Vídeo</span></span></div>
      </section>
      <section class="perfil-bloco" aria-labelledby="titulo-recado"><h3 id="titulo-recado" class="bloco-rotulo">Recado</h3><p id="perfil-recado" class="bloco-valor"><?= esc($contato['recado'] ?: 'Disponível') ?></p></section>
      <section class="perfil-bloco perfil-acoes" aria-label="Ações sobre o contato">
        <form method="post"><input type="hidden" name="acao" value="favoritar"><button type="submit" class="acao-item"><i class="bi <?= $contato['favorito'] ? 'bi-star-fill' : 'bi-star' ?>" aria-hidden="true"></i><span><?= $contato['favorito'] ? 'Remover dos favoritos' : 'Favoritar' ?></span></button></form>
        <form method="post"><input type="hidden" name="acao" value="bloquear"><button type="submit" class="acao-item acao-perigo"><i class="bi bi-slash-circle" aria-hidden="true"></i><span><?= $contato['bloqueado'] ? 'Desbloquear contato' : 'Bloquear contato' ?></span></button></form>
        <button type="button" class="acao-item acao-perigo" id="btn-abrir-denuncia" onclick="document.getElementById('modal-denuncia').classList.add('aberto')"><i class="bi bi-hand-thumbs-down" aria-hidden="true"></i><span>Denunciar <?= esc($contato['nome']) ?></span></button>
      </section>
    </article>
    <div class="modal-overlay <?= isset($_POST['acao']) && $_POST['acao'] === 'denunciar' && $erro ? 'aberto' : '' ?>" id="modal-denuncia" role="dialog" aria-modal="true" aria-labelledby="modal-denuncia-titulo">
      <div class="modal-caixa"><div class="modal-cabecalho"><h2 id="modal-denuncia-titulo">Denunciar <?= esc($contato['nome']) ?>?</h2><button type="button" class="btn-fechar-modal" onclick="document.getElementById('modal-denuncia').classList.remove('aberto')" aria-label="Fechar janela"><i class="bi bi-x-lg" aria-hidden="true"></i></button></div>
        <p class="modal-subtitulo">A denúncia é enviada ao administrador do sistema. A pessoa não é avisada.</p>
        <form method="post" action="perfil.php?id=<?= (int)$id ?>">
          <input type="hidden" name="acao" value="denunciar">
          <div class="campo-grupo"><label for="motivo-denuncia">Motivo *</label><select id="motivo-denuncia" name="motivo" class="campo-controle" required><option value="">Selecione...</option><option value="spam" <?= ($_POST['motivo'] ?? '') === 'spam' ? 'selected' : '' ?>>Spam ou mensagens indesejadas</option><option value="assedio" <?= ($_POST['motivo'] ?? '') === 'assedio' ? 'selected' : '' ?>>Assédio ou intimidação</option><option value="conteudo-improprio" <?= ($_POST['motivo'] ?? '') === 'conteudo-improprio' ? 'selected' : '' ?>>Conteúdo impróprio</option><option value="perfil-falso" <?= ($_POST['motivo'] ?? '') === 'perfil-falso' ? 'selected' : '' ?>>Perfil falso ou fraude</option><option value="outro" <?= ($_POST['motivo'] ?? '') === 'outro' ? 'selected' : '' ?>>Outro motivo</option></select></div>
          <div class="campo-grupo"><label for="descricao-denuncia">Descrição (opcional, até 500 caracteres)</label><textarea id="descricao-denuncia" name="descricao" class="campo-controle" rows="4" maxlength="500"><?= esc($_POST['descricao'] ?? '') ?></textarea></div>
          <label class="checkbox-bloquear" for="bloquear-tambem"><input type="checkbox" id="bloquear-tambem" name="bloquearTambem" <?= isset($_POST['bloquearTambem']) ? 'checked' : '' ?>><span>Bloquear o contato também</span></label>
          <div class="modal-acoes"><button type="button" class="btn btn-secundario" onclick="document.getElementById('modal-denuncia').classList.remove('aberto')">Cancelar</button><button type="submit" class="btn btn-perigo-solido">Denunciar</button></div>
        </form>
      </div>
    </div>
    <?php endif; ?>
  </main>
<?php require_once __DIR__ . '/includes/rodape.php'; ?>
