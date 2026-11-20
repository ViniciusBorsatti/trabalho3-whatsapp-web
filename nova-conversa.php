<?php
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina = 'WhatsApp Web — Nova conversa';
$cssPagina    = 'css/nova-conversa.css';
$caminhoBase  = '';
$scriptsDefer = ['js/tema.js', 'js/nova-conversa.js'];
$tipoRodape   = 'educacional';

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <header class="barra-superior">
    <div class="barra-superior-conteudo">
      <div class="titulo-com-voltar">
        <a href="conversas.php" class="btn-icone" aria-label="Voltar para conversas">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
        </a>
        <div>
          <h1>Nova conversa</h1>
          <p class="subtitulo-contagem" id="contagem-contatos">Carregando contatos...</p>
        </div>
      </div>
      <nav class="nav-atalhos" aria-label="Navegação do site">
        <a href="conversas.php">Conversas</a>
        <a href="perfil.php">Perfil</a>
        <a href="configuracoes.php">Configurações</a>
      </nav>
    </div>
  </header>

  <main class="contatos-layout">
    <section class="contatos-painel" aria-label="Lista de contatos">
      <div class="contatos-busca-area">
        <div class="busca-box">
          <i class="bi bi-search" aria-hidden="true"></i>
          <input
            type="search"
            id="busca-contato"
            placeholder="Pesquisar nome ou número"
            aria-label="Pesquisar nome ou número"
          >
        </div>
      </div>

      <div class="acoes-rapidas">
        <button type="button" class="acao-rapida-item" id="btn-novo-grupo">
          <span class="icone-circular"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
          <span class="acao-rapida-texto">Novo grupo</span>
        </button>

        <button type="button" class="acao-rapida-item" id="btn-abrir-novo-contato">
          <span class="icone-circular"><i class="bi bi-person-plus-fill" aria-hidden="true"></i></span>
          <span class="acao-rapida-texto">Novo contato</span>
        </button>
      </div>

      <h2 class="lista-secao-titulo">Contatos no WhatsApp</h2>

      <div id="lista-contatos" class="lista-contatos" aria-live="polite"></div>
    </section>

    <aside class="contatos-boas-vindas" aria-label="Instruções">
      <div class="boas-vindas-conteudo">
        <i class="bi bi-whatsapp" aria-hidden="true"></i>
        <h2>WhatsApp Web</h2>
        <p>Selecione um contato ao lado para começar a conversar ou cadastre um novo contato.</p>
      </div>
    </aside>
  </main>

  <div
    class="modal-overlay"
    id="modal-novo-contato"
    role="dialog"
    aria-modal="true"
    aria-labelledby="titulo-modal-contato"
  >
    <div class="modal-caixa">
      <div class="modal-cabecalho">
        <h2 id="titulo-modal-contato">Novo contato</h2>
        <button type="button" class="btn-fechar-modal" id="btn-fechar-modal-contato" aria-label="Fechar janela">
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>

      <form id="form-novo-contato" novalidate>
        <div class="campo-grupo">
          <label for="novo-nome">Nome *</label>
          <input
            type="text"
            id="novo-nome"
            name="nome"
            class="campo-controle"
            placeholder="Ex.: Lucas"
            required
          >
          <span class="erro-campo" id="erro-nome" role="alert"></span>
        </div>

        <div class="campo-grupo">
          <label for="novo-sobrenome">Sobrenome</label>
          <input
            type="text"
            id="novo-sobrenome"
            name="sobrenome"
            class="campo-controle"
            placeholder="Ex.: Martins"
          >
          <span class="erro-campo" id="erro-sobrenome" role="alert"></span>
        </div>

        <div class="campo-grupo">
          <label for="novo-telefone">Telefone *</label>
          <input
            type="tel"
            id="novo-telefone"
            name="telefone"
            class="campo-controle"
            placeholder="(49) 99999-0001"
            pattern="^\(\d{2}\)\s?\d{4,5}-\d{4}$"
            required
          >
          <span class="erro-campo" id="erro-telefone" role="alert"></span>
        </div>

        <div class="campo-grupo">
          <label for="novo-email">E-mail</label>
          <input
            type="email"
            id="novo-email"
            name="email"
            class="campo-controle"
            placeholder="lucas@email.com"
          >
          <span class="erro-campo" id="erro-email" role="alert"></span>
        </div>

        <p class="erro-geral" id="erro-geral-contato" role="alert" hidden>
          Corrija os campos destacados para salvar.
        </p>

        <div class="modal-acoes">
          <button type="button" class="btn btn-secundario" id="btn-cancelar-contato">Cancelar</button>
          <button type="submit" class="btn btn-primario">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="toast-aviso" id="toast-contatos" role="status" aria-live="polite"></div>

<?php require_once __DIR__ . '/includes/rodape.php'; ?>