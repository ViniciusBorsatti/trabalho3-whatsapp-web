<?php
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina = 'Criar conta – WhatsApp Web';
$cssPagina    = 'css/cadastro.css';
$scriptsHead  = ['js/tema.js'];
$scriptsDefer = ['js/cadastro.js'];
$tipoRodape   = 'aviso';

$erros = [];
$nome = '';
$telefone = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = limpar($_POST['nome'] ?? '');
    $telefone = limpar($_POST['telefone'] ?? '');
    $email    = limpar($_POST['email'] ?? '');
    $senha    = $_POST['senha'] ?? '';
    $confirma = $_POST['confirma-senha'] ?? '';

    // Validação no servidor, sem depender do JavaScript
    if ($nome === '') {
        $erros[] = 'Informe seu nome completo.';
    }
    if (!preg_match('/^\(\d{2}\) \d{5}-\d{4}$/', $telefone)) {
        $erros[] = 'Use o formato (49) 99999-0001.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Digite um e-mail válido.';
    }
    if (mb_strlen($senha) < 8) {
        $erros[] = 'A senha precisa ter no mínimo 8 caracteres.';
    }
    if ($senha !== $confirma) {
        $erros[] = 'As senhas não conferem.';
    }

    // Impede dois cadastros com o mesmo telefone
    if (!$erros && buscarUsuarioPorTelefone($pdo, $telefone)) {
        $erros[] = 'Já existe uma conta com esse telefone.';
    }

    if (!$erros) {
        // A senha é guardada como hash, nunca em texto puro
        $stmt = $pdo->prepare('INSERT INTO usuarios (nome, telefone, email, senha) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nome, $telefone, $email, password_hash($senha, PASSWORD_DEFAULT)]);

        mensagem('sucesso', 'Conta criada com sucesso! Entre para continuar.');
        redirecionar('login.php');
    }
}

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <div class="cadastro-pagina">
    <header class="cadastro-cabecalho">
      <i class="bi bi-whatsapp logo-icone" aria-hidden="true"></i>
      <nav aria-label="Navegação de acesso">
        <a href="index.php">Voltar ao início</a>
      </nav>
    </header>

    <main class="cadastro-conteudo">
      <section class="cartao-cadastro" aria-labelledby="titulo-cadastro">
        <h1 id="titulo-cadastro">Criar conta</h1>

        <?php if ($erros): ?>
          <p class="msg-erro-geral" role="alert"><?= esc(implode(' ', $erros)) ?></p>
        <?php endif; ?>

        <!-- novalidate desliga a validação nativa; a validação fica no JS e no PHP -->
        <form id="form-cadastro" action="cadastro.php" method="post" novalidate>
          <div class="grupo-campo">
            <label for="nome">Nome completo</label>
            <input type="text" class="campo" id="nome" name="nome" value="<?= esc($nome) ?>" autocomplete="name" required aria-describedby="erro-nome">
            <p class="msg-erro" id="erro-nome"></p>
          </div>

          <div class="grupo-campo">
            <label for="telefone">Telefone</label>
            <input type="tel" class="campo" id="telefone" name="telefone" value="<?= esc($telefone) ?>" autocomplete="tel" placeholder="(49) 99999-0001" pattern="\([0-9]{2}\)\s[0-9]{5}-[0-9]{4}" required aria-describedby="erro-telefone">
            <p class="msg-erro" id="erro-telefone"></p>
          </div>

          <div class="grupo-campo">
            <label for="email">E-mail</label>
            <input type="email" class="campo" id="email" name="email" value="<?= esc($email) ?>" autocomplete="email" required aria-describedby="erro-email">
            <p class="msg-erro" id="erro-email"></p>
          </div>

          <div class="grupo-campo">
            <label for="senha">Senha</label>
            <input type="password" class="campo" id="senha" name="senha" autocomplete="new-password" minlength="8" required aria-describedby="erro-senha">
            <p class="msg-erro" id="erro-senha"></p>
          </div>

          <div class="grupo-campo">
            <label for="confirma-senha">Confirmar senha</label>
            <input type="password" class="campo" id="confirma-senha" name="confirma-senha" autocomplete="new-password" required aria-describedby="erro-confirma-senha">
            <p class="msg-erro" id="erro-confirma-senha"></p>
          </div>

          <button type="submit" class="btn btn-primario">Cadastrar</button>
        </form>

        <p class="cadastro-rodape-link">
          Já tem uma conta? <a href="login.php">Entrar</a>
        </p>
      </section>
    </main>
  </div>
<?php require_once __DIR__ . '/includes/rodape.php'; ?>