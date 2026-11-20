<?php
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina = 'WhatsApp Web — Perfil do contato';
$cssPagina    = 'css/perfil.css';
$caminhoBase  = '';
$scriptsDefer = ['js/tema.js', 'js/perfil.js'];
$tipoRodape   = 'educacional';

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <header class="barra-superior">
    <div class="barra-superior-conteudo">
      <div class="titulo-com-voltar">
        <a href="conversas.php" class="btn-icone" aria-label="Voltar para conversas">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
        </a>
        <h1>Dados do contato</h1>
      </div>
      <nav class="nav-atalhos" aria-label="Navegação do site">
        <a href="conversas.php">Conversas</a>
        <a href="nova-conversa.php">Contatos</a>
        <a href="configuracoes.php">Configurações</a>
      </nav>
    </div>
  </header>

  <main class="perfil-container">
    <article class="perfil-painel">
      <section class="perfil-cabecalho" aria-label="Informações principais do contato">
        <img
          id="perfil-avatar"
          src="img/avatar-padrao.svg"
          alt="Avatar padrão de Ana Souza"
          class="perfil-avatar"
          width="140"
          height="140"
        >
        <h2 id="perfil-nome">Ana Souza</h2>
        <p id="perfil-telefone" class="perfil-telefone">+55 49 99999-0001</p>

        <div class="perfil-badges" id="perfil-badges">
          <span class="badge-status favorito" id="badge-favorito" hidden>
            <i class="bi bi-star-fill" aria-hidden="true"></i> Favorito
          </span>
          <span class="badge-status bloqueado" id="badge-bloqueado" hidden>
            <i class="bi bi-slash-circle" aria-hidden="true"></i> Contato bloqueado (RN03)
          </span>
        </div>

        <div class="perfil-atalhos-rapidos" aria-label="Atalhos visuais do contato">
          <button type="button" class="atalho-visual" data-visual="Áudio">
            <i class="bi bi-telephone" aria-hidden="true"></i>
            <span>Áudio</span>
          </button>
          <button type="button" class="atalho-visual" data-visual="Vídeo">
            <i class="bi bi-camera-video" aria-hidden="true"></i>
            <span>Vídeo</span>
          </button>
          <button type="button" class="atalho-visual" data-visual="Pesquisar">
            <i class="bi bi-search" aria-hidden="true"></i>
            <span>Pesquisar</span>
          </button>
        </div>
      </section>

      <section class="perfil-bloco" aria-labelledby="titulo-recado">
        <h3 id="titulo-recado" class="bloco-rotulo">Recado</h3>
        <p id="perfil-recado" class="bloco-valor">Disponível</p>
      </section>

      <section class="perfil-bloco" aria-labelledby="titulo-midias">
        <div class="bloco-linha">
          <h3 id="titulo-midias" class="bloco-rotulo">Mídia, links e docs</h3>
          <span class="midia-contador">3 <i class="bi bi-chevron-right" aria-hidden="true"></i></span>
        </div>
        <div class="midia-grade" aria-label="Miniaturas ilustrativas de mídia">
          <div class="midia-item"><i class="bi bi-image" aria-hidden="true"></i></div>
          <div class="midia-item"><i class="bi bi-file-earmark-text" aria-hidden="true"></i></div>
          <div class="midia-item"><i class="bi bi-link-45deg" aria-hidden="true"></i></div>
        </div>
      </section>

      <section class="perfil-bloco perfil-acoes" aria-label="Ações sobre o contato">
        <button type="button" class="acao-item" id="btn-favoritar">
          <i class="bi bi-star" id="icone-favoritar" aria-hidden="true"></i>
          <span id="texto-favoritar">Favoritar</span>
        </button>

        <button type="button" class="acao-item acao-perigo" id="btn-bloquear">
          <i class="bi bi-slash-circle" aria-hidden="true"></i>
          <span id="texto-bloquear">Bloquear Ana Souza</span>
        </button>

        <button type="button" class="acao-item acao-perigo" id="btn-abrir-denuncia">
          <i class="bi bi-hand-thumbs-down" aria-hidden="true"></i>
          <span id="texto-denunciar">Denunciar Ana Souza</span>
        </button>

        <button type="button" class="acao-item acao-perigo" id="btn-apagar-conversa">
          <i class="bi bi-trash" aria-hidden="true"></i>
          <span>Apagar conversa</span>
        </button>
      </section>
    </article>
  </main>

  <div
    class="modal-overlay"
    id="modal-denuncia"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-denuncia-titulo"
  >
    <div class="modal-caixa">
      <div class="modal-cabecalho">
        <h2 id="modal-denuncia-titulo">Denunciar Ana Souza?</h2>
        <button type="button" class="btn-fechar-modal" id="btn-fechar-modal" aria-label="Fechar janela">
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>

      <p class="modal-subtitulo">
        A denúncia é enviada ao administrador do sistema. A pessoa não é avisada.
      </p>

      <form id="form-denuncia" novalidate>
        <div class="campo-grupo">
          <label for="motivo-denuncia">Motivo *</label>
          <select id="motivo-denuncia" name="motivo" class="campo-controle" required>
            <option value="">Selecione...</option>
            <option value="spam">Spam ou mensagens indesejadas</option>
            <option value="assedio">Assédio ou intimidação</option>
            <option value="conteudo-improprio">Conteúdo impróprio</option>
            <option value="perfil-falso">Perfil falso ou fraude</option>
            <option value="outro">Outro motivo</option>
          </select>
          <span class="erro-campo" id="erro-motivo" role="alert"></span>
        </div>

        <div class="campo-grupo">
          <label for="descricao-denuncia">Descrição (opcional)</label>
          <textarea
            id="descricao-denuncia"
            name="descricao"
            class="campo-controle"
            rows="4"
            maxlength="500"
            placeholder="Conte o que aconteceu..."
          ></textarea>
          <div class="contador-linha">
            <span id="contador-caracteres">0/500</span>
          </div>
        </div>

        <label class="checkbox-bloquear" for="bloquear-tambem">
          <input type="checkbox" id="bloquear-tambem" name="bloquearTambem">
          <span>Bloquear o contato também</span>
        </label>

        <div class="modal-acoes">
          <button type="button" class="btn btn-secundario" id="btn-cancelar-denuncia">Cancelar</button>
          <button type="submit" class="btn btn-perigo-solido">Denunciar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="toast-aviso" id="toast-perfil" role="status" aria-live="polite"></div>

<?php require_once __DIR__ . '/includes/rodape.php'; ?>