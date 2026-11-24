<?php
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';
require_once __DIR__ . '/includes/verifica-usuario.php';

$abrirConversa = 0;
$contatoId = filter_input(INPUT_GET, 'contato', FILTER_VALIDATE_INT);
if ($contatoId) {
    $abrirConversa = abrirConversaComContato($pdo, $contatoId) ?? 0;
}

$tituloPagina = 'Conversas – WhatsApp Web';
$cssPagina    = 'css/conversas.css';
$caminhoBase  = '';
$scriptsHead  = ['js/tema.js'];
$scriptsDefer = ['js/conversas.js'];
$tipoRodape   = 'aviso';

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <div class="app" id="app" data-abrir="<?= (int)$abrirConversa ?>">
    <nav class="barra-icones" aria-label="Menu principal">
      <div class="barra-icones-grupo">
        <a href="conversas.php" class="btn-icone" aria-label="Conversas" aria-current="page"><i class="bi bi-chat-left-text-fill"></i></a>
        <button type="button" class="btn-icone" aria-label="Status" data-aviso="Status"><i class="bi bi-record-circle"></i></button>
        <button type="button" class="btn-icone" aria-label="Canais" data-aviso="Canais"><i class="bi bi-chat-square-dots"></i></button>
        <button type="button" class="btn-icone" aria-label="Comunidades" data-aviso="Comunidades"><i class="bi bi-people"></i></button>
      </div>
      <div class="barra-icones-grupo">
        <a href="configuracoes.php" class="avatar avatar-barra" aria-label="Perfil e configurações"><img src="img/avatares/usuario.svg" alt="Sua foto de perfil"></a>
      </div>
    </nav>

    <aside class="painel-lista" aria-label="Lista de conversas">
      <header class="painel-cabecalho">
        <h1>Conversas</h1>
        <div class="painel-acoes">
          <a href="nova-conversa.php" class="btn-icone" aria-label="Nova conversa"><i class="bi bi-pencil-square"></i></a>
          <button type="button" class="btn-icone" id="btn-opcoes-lista" aria-label="Mais opções" aria-haspopup="menu" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
        </div>
      </header>

      <div class="painel-busca">
        <label class="busca">
          <i class="bi bi-search"></i>
          <span class="visualmente-oculto">Pesquisar conversas</span>
          <input type="search" id="busca-conversas" placeholder="Pesquisar ou começar uma nova conversa">
        </label>
      </div>

      <div class="filtros" role="group" aria-label="Filtros">
        <button type="button" class="chip ativo" data-filtro="tudo" aria-pressed="true">Tudo</button>
        <button type="button" class="chip" data-filtro="nao-lidas" aria-pressed="false">Não lidas</button>
        <button type="button" class="chip" data-filtro="favoritas" aria-pressed="false">Favoritas</button>
        <button type="button" class="chip" data-filtro="grupos" aria-pressed="false">Grupos</button>
      </div>

      <ul class="lista-conversas" id="lista-conversas"></ul>
    </aside>

    <main class="area-chat">
      <section class="boas-vindas" id="boas-vindas" aria-label="Área vazia">
        <p class="boas-vindas-aviso">Clone do WhatsApp Web para fins educacionais. Dados fictícios.</p>
      </section>

      <section class="chat" id="chat" aria-label="Conversa" hidden>
        <header class="chat-cabecalho">
          <button type="button" class="btn-icone btn-voltar" id="btn-voltar" aria-label="Voltar"><i class="bi bi-arrow-left"></i></button>
          <a href="perfil.php" class="chat-contato">
            <span class="avatar" id="chat-avatar"><i class="bi bi-person-fill"></i></span>
            <span class="chat-contato-info">
              <span class="chat-nome" id="chat-nome"></span>
              <span class="chat-status" id="chat-status"></span>
            </span>
          </a>
          <div class="chat-acoes">
            <label class="visualmente-oculto" for="busca-mensagens">Pesquisar mensagens</label>
            <input type="search" class="busca-mensagens" id="busca-mensagens" placeholder="Pesquisar mensagens" hidden>
            <button type="button" class="btn-icone" id="btn-buscar-mensagens" aria-label="Pesquisar na conversa" aria-expanded="false"><i class="bi bi-search"></i></button>
            <button type="button" class="btn-icone" id="btn-opcoes-chat" aria-label="Mais opções da conversa" aria-haspopup="menu" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
          </div>
        </header>

        <div class="mensagens" id="mensagens"></div>

        <footer class="chat-rodape">
          <div class="painel-emoji" id="painel-emoji" role="listbox" aria-label="Emojis" hidden>
            <?php foreach (['😀', '😂', '😍', '😎', '😢', '😡', '👍', '👏', '🙏', '🎉', '❤️', '🔥', '✅', '📚', '💻', '☕'] as $emoji): ?>
              <button type="button" role="option" data-emoji="<?= $emoji ?>" aria-label="Inserir <?= $emoji ?>"><?= $emoji ?></button>
            <?php endforeach; ?>
          </div>
          <form class="form-mensagem" id="form-mensagem">
            <button type="button" class="btn-icone" id="btn-emoji" aria-label="Emojis" aria-expanded="false"><i class="bi bi-emoji-smile"></i></button>
            <button type="button" class="btn-icone" id="btn-anexar" aria-label="Enviar foto ou documento"><i class="bi bi-plus-lg"></i></button>
            <label class="visualmente-oculto" for="campo-arquivo">Foto ou documento</label>
            <input type="file" id="campo-arquivo" accept="image/jpeg,image/png,image/gif,image/webp,application/pdf" hidden>
            <label class="visualmente-oculto" for="campo-mensagem">Mensagem</label>
            <input type="text" class="campo-mensagem" id="campo-mensagem" placeholder="Digite uma mensagem" autocomplete="off">
            <button type="submit" class="btn-icone" aria-label="Enviar"><i class="bi bi-send-fill"></i></button>
          </form>
        </footer>
      </section>
    </main>

  </div>

  <div class="menu-contexto" id="menu-lista" role="menu" hidden>
    <a href="nova-conversa.php" role="menuitem">Nova conversa</a>
    <a href="configuracoes.php" role="menuitem">Configurações</a>
    <a href="logout.php" role="menuitem" class="opcao-perigo">Desconectar</a>
  </div>

  <div class="menu-contexto" id="menu-chat" role="menu" hidden>
    <a href="perfil.php" role="menuitem" id="opcao-dados">Dados do contato</a>
    <button type="button" role="menuitem" data-chat="fixar" id="opcao-chat-fixar">Fixar conversa</button>
    <button type="button" role="menuitem" data-chat="favoritar" id="opcao-chat-favoritar">Favoritar</button>
    <button type="button" role="menuitem" data-chat="fechar">Fechar conversa</button>
  </div>

  <div class="menu-contexto" id="menu-contexto" role="menu" hidden>
    <button type="button" role="menuitem" data-acao="fixar" id="opcao-fixar">Fixar conversa</button>
    <button type="button" role="menuitem" data-acao="favoritar" id="opcao-favoritar">Favoritar</button>
    <button type="button" role="menuitem" data-acao="apagar" class="opcao-perigo">Apagar para todos</button>
  </div>

  <div class="modal" id="modal-confirmar" role="dialog" aria-modal="true" aria-labelledby="modal-titulo" hidden>
    <div class="modal-conteudo">
      <h2 class="modal-titulo" id="modal-titulo">Apagar mensagem?</h2>
      <p class="modal-texto" id="modal-texto">Esta mensagem será apagada para todos na conversa.</p>
      <div class="modal-acoes">
        <button type="button" class="btn btn-secundario" id="modal-cancelar">Cancelar</button>
        <button type="button" class="btn btn-perigo" id="modal-confirmar-btn">Apagar</button>
      </div>
    </div>
  </div>

  <div class="toast" id="toast" role="status" aria-live="polite"></div>
<?php require_once __DIR__ . '/includes/rodape.php'; ?>
