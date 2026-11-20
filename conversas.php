<?php
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina = 'Conversas – WhatsApp Web';
$cssPagina    = 'css/conversas.css';
$caminhoBase  = '';
$scriptsHead  = ['js/tema.js'];
$scriptsDefer = ['js/conversas.js'];
$tipoRodape   = 'aviso';

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <div class="app" id="app">
    <nav class="barra-icones" aria-label="Menu principal">
      <div class="barra-icones-grupo">
        <a href="conversas.php" class="btn-icone" aria-label="Conversas" aria-current="page"><i class="bi bi-chat-left-text-fill"></i></a>
        <button type="button" class="btn-icone" aria-label="Status"><i class="bi bi-record-circle"></i></button>
        <button type="button" class="btn-icone" aria-label="Canais"><i class="bi bi-chat-square-dots"></i></button>
        <button type="button" class="btn-icone" aria-label="Comunidades"><i class="bi bi-people"></i></button>
      </div>
      <div class="barra-icones-grupo">
        <a href="configuracoes.php" class="avatar avatar-barra" aria-label="Perfil e configurações"><i class="bi bi-person-fill"></i></a>
      </div>
    </nav>

    <aside class="painel-lista" aria-label="Lista de conversas">
      <header class="painel-cabecalho">
        <h1>Conversas</h1>
        <div class="painel-acoes">
          <a href="nova-conversa.php" class="btn-icone" aria-label="Nova conversa"><i class="bi bi-pencil-square"></i></a>
          <button type="button" class="btn-icone" aria-label="Mais opções"><i class="bi bi-three-dots-vertical"></i></button>
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
            <button type="button" class="btn-icone" aria-label="Pesquisar na conversa"><i class="bi bi-search"></i></button>
            <button type="button" class="btn-icone" aria-label="Mais opções da conversa"><i class="bi bi-three-dots-vertical"></i></button>
          </div>
        </header>

        <div class="mensagens" id="mensagens"></div>

        <footer class="chat-rodape">
          <form class="form-mensagem" id="form-mensagem">
            <button type="button" class="btn-icone" aria-label="Emojis"><i class="bi bi-emoji-smile"></i></button>
            <button type="button" class="btn-icone" aria-label="Anexar"><i class="bi bi-plus-lg"></i></button>
            <label class="visualmente-oculto" for="campo-mensagem">Mensagem</label>
            <input type="text" class="campo-mensagem" id="campo-mensagem" placeholder="Digite uma mensagem" autocomplete="off">
            <button type="submit" class="btn-icone" aria-label="Enviar"><i class="bi bi-send-fill"></i></button>
          </form>
        </footer>
      </section>
    </main>

    <?php require_once __DIR__ . '/includes/rodape.php'; ?>
  </div>

  <div class="menu-contexto" id="menu-contexto" role="menu" hidden>
    <button type="button" role="menuitem" data-acao="fixar" id="opcao-fixar">Fixar conversa</button>
    <button type="button" role="menuitem" data-acao="favoritar" id="opcao-favoritar">Favoritar</button>
    <button type="button" role="menuitem" data-acao="apagar" class="opcao-perigo">Apagar para todos</button>
  </div>

  <div class="modal" id="modal-confirmar" role="dialog" aria-modal="true" aria-labelledby="modal-titulo" hidden>
    <div class="modal-conteudo">
      <h2 class="modal-titulo" id="modal-titulo">Apagar conversa?</h2>
      <p class="modal-texto" id="modal-texto">Esta conversa será apagada para todos.</p>
      <div class="modal-acoes">
        <button type="button" class="btn btn-secundario" id="modal-cancelar">Cancelar</button>
        <button type="button" class="btn btn-perigo" id="modal-confirmar-btn">Apagar</button>
      </div>
    </div>
  </div>

  <div class="toast" id="toast" role="status" aria-live="polite"></div>
</body>
</html>