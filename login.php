<?php
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina = 'Entrar – WhatsApp Web';
$cssPagina    = 'css/login.css';
$scriptsHead  = ['js/tema.js'];
$scriptsDefer = ['js/login.js'];
$tipoRodape   = 'aviso';

$telefone  = '';
$erroGeral = '';
$msgFlash  = mensagem();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefone = limpar($_POST['telefone'] ?? '');
    $senha    = $_POST['senha'] ?? '';

    $usuario = buscarUsuarioPorTelefone($pdo, $telefone);

    // password_verify compara a senha digitada com o hash salvo no banco
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Troca o id da sessão para evitar roubo de sessão
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'       => $usuario['id'],
            'nome'     => $usuario['nome'],
            'telefone' => $usuario['telefone'],
        ];
        $destino = $_SESSION['voltar_para'] ?? 'conversas.php';
        unset($_SESSION['voltar_para']);
        redirecionar($destino);
    }

    // Mensagem genérica: não revela se foi o telefone ou a senha que errou
    $erroGeral = 'Telefone ou senha incorretos.';
}

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <div class="login-pagina">
    <header class="login-cabecalho">
      <i class="bi bi-whatsapp logo-icone" aria-hidden="true"></i>
      <nav aria-label="Navegação de acesso">
        <a href="index.php">Voltar ao início</a>
      </nav>
    </header>

    <main class="login-conteudo">
      <section class="cartao-login" aria-labelledby="titulo-login">
        <h1 id="titulo-login">Entrar</h1>

        <p class="msg-erro-geral<?= !$erroGeral && ($msgFlash['tipo'] ?? '') === 'sucesso' ? ' sucesso' : '' ?>" id="erro-geral" role="alert"><?= esc($erroGeral ?: ($msgFlash['texto'] ?? '')) ?></p>

        <!-- novalidate desliga a validação nativa; a validação fica no JS e no PHP -->
        <form id="form-login" action="login.php" method="post" novalidate>
          <div class="grupo-campo">
            <label for="telefone">Telefone</label>
            <input type="tel" class="campo" id="telefone" name="telefone" value="<?= esc($telefone) ?>" autocomplete="tel" placeholder="(49) 99999-0001" pattern="\([0-9]{2}\)\s[0-9]{5}-[0-9]{4}" required aria-describedby="erro-telefone">
            <p class="msg-erro" id="erro-telefone"></p>
          </div>

          <div class="grupo-campo">
            <label for="senha">Senha</label>
            <div class="senha-campo">
              <input type="password" class="campo" id="senha" name="senha" autocomplete="current-password" required aria-describedby="erro-senha">
              <button type="button" class="btn-mostrar-senha" id="btn-mostrar-senha" aria-label="Mostrar senha" aria-pressed="false" aria-controls="senha">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <p class="msg-erro" id="erro-senha"></p>
          </div>

          <button type="submit" class="btn btn-primario">Entrar</button>
        </form>

        <p class="login-rodape-link">
          Não tem uma conta? <a href="cadastro.php">Criar conta</a>
        </p>
      </section>
    </main>
  </div>
<?php require_once __DIR__ . '/includes/rodape.php'; ?>