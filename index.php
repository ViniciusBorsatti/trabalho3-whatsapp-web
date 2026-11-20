<?php
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina     = 'WhatsApp Web — Entrada';
$cssPagina        = 'css/entrada.css';
$caminhoBase      = '';
$scriptsDefer     = ['js/tema.js', 'js/entrada.js'];
$tipoRodape       = 'educacional';
$incluirLinkAdmin = true;

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <header class="entrada-topo">
    <div class="entrada-topo-conteudo">
      <div class="marca-logo">
        <i class="bi bi-whatsapp" aria-hidden="true"></i>
        <span>WhatsApp Web</span>
      </div>
      <nav class="entrada-nav" aria-label="Acesso rápido">
        <a href="login.php">Entrar no celular</a>
        <a href="cadastro.php">Criar conta</a>
      </nav>
    </div>
  </header>

  <main class="entrada-principal">
    <section class="entrada-cartao" aria-labelledby="titulo-entrada">
      <div class="entrada-desktop">
        <article class="entrada-instrucoes">
          <h1 id="titulo-entrada">Use o WhatsApp no seu computador</h1>
          <ol class="lista-passos">
            <li>Entre no app pelo seu celular (é preciso ter conta)</li>
            <li>Abra a câmera do celular</li>
            <li>Aponte a câmera para o QR code ao lado</li>
            <li>No celular, toque em <strong>Conectar</strong></li>
          </ol>

          <label class="opcao-manter" for="manter-conectado">
            <input type="checkbox" id="manter-conectado">
            <span>Manter conectado neste computador</span>
          </label>

          <div class="entrada-links-auxiliares">
            <p>
              Ainda não tem conta?
              <a href="cadastro.php">Crie pelo celular.</a>
            </p>
            <p class="texto-apoio">
              Acesse este mesmo endereço no navegador do celular ou
              <a href="conectar.php">simule a tela do celular ao ler o QR</a>.
            </p>
          </div>
        </article>

        <aside class="entrada-qr-area" aria-label="Código QR para conexão">
          <div class="qr-moldura" id="qr-moldura">
            <img
              id="qr-img"
              src="img/qrcode.svg"
              alt="Código QR para conectar o WhatsApp Web no computador"
              width="220"
              height="220"
            >
            <div class="qr-expirado-overlay" id="qr-expirado-overlay" hidden>
              <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
              <p>Este QR code expirou</p>
            </div>
          </div>

          <p class="qr-status" id="qr-contador" aria-live="polite">
            O QR code expira em <strong>60 s</strong>
          </p>
          <p class="erro-campo" id="qr-erro" role="alert"></p>

          <div class="qr-botoes">
            <button type="button" class="btn btn-primario" id="btn-simular-leitura">
              <i class="bi bi-qr-code-scan" aria-hidden="true"></i>
              Simular leitura
            </button>
            <button type="button" class="btn btn-secundario" id="btn-novo-qr">
              <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
              Gerar novo QR code
            </button>
          </div>

          <p class="qr-estado-conexao" id="qr-estado-conexao">
            Estado da espera: Aguardando o celular...
          </p>
        </aside>
      </div>

      <div class="entrada-mobile">
        <div class="mobile-icone" aria-hidden="true">
          <i class="bi bi-whatsapp"></i>
        </div>
        <p class="mobile-titulo">WhatsApp Web</p>
        <p class="mobile-aviso">
          Esta página foi feita para o computador. No celular, entre no app para conversar e conectar o seu PC.
        </p>

        <div class="mobile-acoes">
          <a href="login.php" class="btn btn-primario">Entrar no app</a>
          <a href="cadastro.php" class="btn btn-secundario">Criar conta</a>
        </div>

        <div class="mobile-dica">
          <strong>Como conectar o computador:</strong>
          abra o site no PC e aponte a câmera do celular para o QR code.
        </div>
      </div>
    </section>
  </main>

<?php require_once __DIR__ . '/includes/rodape.php'; ?>