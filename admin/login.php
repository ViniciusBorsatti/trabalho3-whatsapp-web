<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';

$erroGeral = '';
$erros = [
    'email' => '',
    'senha' => '',
];
$emailValor = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailLimpo = limpar($_POST['email'] ?? '');
    $emailValor = filter_var($emailLimpo, FILTER_SANITIZE_EMAIL);
    $senha      = (string)($_POST['senha'] ?? '');

    if ($emailLimpo === '') {
        $erros['email'] = 'Informe o e-mail.';
    } elseif ($emailValor === '' || !filter_var($emailValor, FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Digite um e-mail válido.';
    }

    if ($senha === '') {
        $erros['senha'] = 'Informe a senha.';
    }

    if ($erros['email'] === '' && $erros['senha'] === '') {
        $admin = buscarAdminPorEmail($pdo, $emailValor);

        if ($admin && password_verify($senha, $admin['senha'])) {
            session_regenerate_id(true);
            $_SESSION['admin'] = [
                'id'    => (int)$admin['id'],
                'nome'  => $admin['nome'],
                'email' => $admin['email'],
            ];
            redirecionar('painel.php');
        } else {
            $erroGeral = 'E-mail ou senha inválidos.';
        }
    } else {
        $erroGeral = 'Revise os campos destacados.';
    }
}

$tituloPagina = 'Entrar como administrador – WhatsApp Web';
$cssPagina    = 'css/admin-login.css';
$caminhoBase  = '../';
$scriptsHead  = ['js/tema.js'];
$scriptsDefer = ['js/admin-login.js'];
$tipoRodape   = 'aviso';

require_once __DIR__ . '/../includes/cabecalho.php';
?>
  <div class="login-pagina">
    <header class="login-cabecalho">
      <i class="bi bi-whatsapp logo-icone" aria-hidden="true"></i>
      <nav aria-label="Navegação de acesso">
        <a href="../index.php">Voltar ao site</a>
      </nav>
    </header>

    <main class="login-conteudo">
      <section class="cartao-login" aria-labelledby="titulo-admin-login">
        <h1 id="titulo-admin-login">Entrar como administrador</h1>

        <p class="msg-erro-geral" id="erro-geral" role="alert"><?= esc($erroGeral) ?></p>

        <form id="form-admin-login" method="POST" action="login.php" novalidate>
          <div class="grupo-campo">
            <label for="email">E-mail</label>
            <input
              type="email"
              class="campo<?= $erros['email'] !== '' ? ' campo-invalido' : '' ?>"
              id="email"
              name="email"
              autocomplete="email"
              value="<?= esc($emailValor) ?>"
              <?= $erros['email'] !== '' ? ' aria-invalid="true"' : '' ?>
              required
              aria-describedby="erro-email"
            >
            <p class="msg-erro" id="erro-email"><?= esc($erros['email']) ?></p>
          </div>

          <div class="grupo-campo">
            <label for="senha">Senha</label>
            <div class="senha-campo">
              <input
                type="password"
                class="campo<?= $erros['senha'] !== '' ? ' campo-invalido' : '' ?>"
                id="senha"
                name="senha"
                autocomplete="current-password"
                minlength="8"
                <?= $erros['senha'] !== '' ? ' aria-invalid="true"' : '' ?>
                required
                aria-describedby="erro-senha"
              >
              <button type="button" class="btn-mostrar-senha" id="btn-mostrar-senha" aria-label="Mostrar senha" aria-pressed="false" aria-controls="senha">
                <i class="bi bi-eye"></i>
              </button>
            </div>
            <p class="msg-erro" id="erro-senha"><?= esc($erros['senha']) ?></p>
          </div>

          <button type="submit" class="btn btn-primario">Entrar</button>
        </form>
      </section>
    </main>
  </div>

  <div class="toast" id="toast" role="status" aria-live="polite"></div>
<?php require_once __DIR__ . '/../includes/rodape.php'; ?>