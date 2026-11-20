<?php
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina = 'Conectar computador – WhatsApp Web';
$cssPagina    = 'css/conectar.css';
$scriptsHead  = ['js/tema.js'];
$scriptsDefer = ['js/conectar.js'];
$tipoRodape   = 'aviso';

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <div class="conectar-pagina">
    <header class="conectar-cabecalho">
      <i class="bi bi-whatsapp logo-icone" aria-hidden="true"></i>
      <nav aria-label="Navegação de acesso">
        <a href="index.php">Voltar ao início</a>
      </nav>
    </header>

    <main class="conectar-conteudo">
      <section class="cartao-conectar" aria-labelledby="titulo-conectar">
        <div class="conectar-icone" aria-hidden="true">
          <i class="bi bi-laptop"></i>
        </div>

        <h1 id="titulo-conectar">Conectar computador?</h1>

        <p class="conectar-descricao">
          Confirme para conectar o WhatsApp Web neste computador. Suas conversas vão aparecer nele.
        </p>

        <p class="contagem" id="contagem">Expira em 60 s</p>
        <p class="msg-erro-geral" id="erro-conexao" role="alert"></p>

        <div class="acoes-conectar">
          <button type="button" class="btn btn-primario" id="btn-conectar">Sim, conectar</button>
          <button type="button" class="btn btn-secundario" id="btn-cancelar">Cancelar</button>
        </div>

        <button type="button" class="btn btn-secundario btn-novo-qr" id="btn-novo-qr" hidden>Gerar novo QR code</button>
      </section>
    </main>
  </div>

  <div class="toast" id="toast" role="status" aria-live="polite"></div>
<?php require_once __DIR__ . '/includes/rodape.php'; ?>