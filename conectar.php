<?php
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

$token = limpar(filter_input(INPUT_GET, 'token', FILTER_UNSAFE_RAW) ?? '');

if (!usuarioLogado()) {
    if ($token !== '') {
        $_SESSION['voltar_para'] = 'conectar.php?token=' . urlencode($token);
        mensagem('erro', 'Entre na sua conta para conectar o computador.');
    }
    redirecionar('login.php');
}

$erro = '';
$conectado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        confirmarQr($pdo, $token, (int)usuarioLogado()['id']);
        $conectado = true;
    } catch (DomainException $e) {
        $erro = $e->getMessage();
    }
}

$qr = $token !== '' ? buscarQr($pdo, $token) : null;

if (!$conectado && $erro === '') {
    if ($token === '') {
        $erro = 'Aponte a câmera do celular para o QR code que aparece no computador.';
    } elseif (!$qr) {
        $erro = 'QR code inválido. Gere um novo no computador.';
    } elseif ($qr['status'] !== 'aguardando') {
        $erro = 'Este QR code já foi usado. Gere um novo no computador.';
    } elseif ($qr['restante'] <= 0) {
        $erro = 'QR code expirado. Gere um novo no computador.';
    }
}

$tituloPagina = 'Conectar computador – WhatsApp Web';
$cssPagina    = 'css/conectar.css';
$scriptsHead  = ['js/tema.js'];
$scriptsDefer = ['js/conectar.js'];
$tipoRodape   = 'aviso';

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <div class="conectar-pagina">
    <div class="conectar-celular">
      <header class="conectar-cabecalho">
        <i class="bi bi-whatsapp logo-icone" aria-hidden="true"></i>
        <nav aria-label="Navegação do celular">
          <a href="conversas.php">Minhas conversas</a>
        </nav>
      </header>

      <main class="conectar-conteudo">
        <section class="cartao-conectar" aria-labelledby="titulo-conectar">
          <?php if ($conectado): ?>
            <i class="bi bi-check-circle-fill conectar-sucesso" aria-hidden="true"></i>
            <h1 id="titulo-conectar">Computador conectado</h1>
            <p class="contagem">Suas conversas já estão abrindo no computador.</p>
            <div class="acoes-conectar">
              <a href="conversas.php" class="btn btn-primario">Voltar para as conversas</a>
            </div>
          <?php else: ?>
            <h1 id="titulo-conectar">Conectar a um computador?</h1>

            <?php if ($qr): ?>
              <article class="aparelho" aria-label="Aparelho que será conectado">
                <i class="bi bi-laptop aparelho-icone" aria-hidden="true"></i>
                <div class="aparelho-info">
                  <p class="aparelho-nome"><?= esc($qr['navegador'] ?: 'Computador') ?></p>
                  <p class="aparelho-detalhe">Conta: <?= esc(usuarioLogado()['nome']) ?></p>
                  <p class="aparelho-detalhe">QR gerado hoje às <?= esc(date('H:i', strtotime($qr['criado_em']))) ?></p>
                </div>
              </article>
            <?php endif; ?>

            <?php if ($erro === ''): ?>
              <p class="contagem" id="contagem" data-restante="<?= (int)$qr['restante'] ?>">O QR code expira em <?= (int)$qr['restante'] ?> s</p>
            <?php endif; ?>
            <p class="msg-erro-geral" id="erro-conexao" role="alert"><?= esc($erro) ?></p>

            <form class="acoes-conectar" action="conectar.php?token=<?= esc(urlencode($token)) ?>" method="post">
              <a href="conversas.php" class="btn btn-secundario">Cancelar</a>
              <button type="submit" class="btn btn-primario" id="btn-conectar" <?= $erro !== '' ? 'disabled' : '' ?>>Conectar</button>
            </form>
          <?php endif; ?>
        </section>
      </main>
    </div>
  </div>
<?php require_once __DIR__ . '/includes/rodape.php'; ?>
