<?php
require_once __DIR__ . '/includes/conexao.php';
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina = 'WhatsApp Web — Nova conversa';
$cssPagina = 'css/nova-conversa.css';
$scriptsDefer = ['js/tema.js', 'js/nova-conversa.js'];
$tipoRodape = 'educacional';
$busca = limpar(filter_input(INPUT_GET, 'busca', FILTER_UNSAFE_RAW) ?? '');
$erros = [];
$nome = '';
$sobrenome = '';
$telefone = '';
$mensagemFlash = mensagem();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limpar(filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW) ?? '');
    $sobrenome = limpar(filter_input(INPUT_POST, 'sobrenome', FILTER_UNSAFE_RAW) ?? '');
    $telefone = limpar(filter_input(INPUT_POST, 'telefone', FILTER_UNSAFE_RAW) ?? '');
    $telefoneNormalizado = preg_replace('/\D+/', '', filter_var($telefone, FILTER_SANITIZE_NUMBER_INT));

    if ($nome === '' || comprimentoTexto($nome) > 100) {
        $erros['nome'] = 'Informe um nome com até 100 caracteres.';
    }
    if ($sobrenome !== '' && comprimentoTexto($sobrenome) > 100) {
        $erros['sobrenome'] = 'O sobrenome deve ter até 100 caracteres.';
    }
    if (!preg_match('/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', $telefone) || strlen($telefoneNormalizado) < 10 || strlen($telefoneNormalizado) > 11) {
        $erros['telefone'] = 'Use um telefone válido, por exemplo (49) 99999-0001.';
    }

    if (!$erros) {
        if (telefoneJaCadastrado($pdo, $telefoneNormalizado)) {
            $erros['telefone'] = 'Telefone já cadastrado.';
        }
    }

    if (!$erros) {
        $nomeCompleto = trim($nome . ' ' . $sobrenome);
        $telefoneFormatado = sprintf('(%s) %s-%s', substr($telefoneNormalizado, 0, 2), substr($telefoneNormalizado, 2, -4), substr($telefoneNormalizado, -4));
        criarContato($pdo, $nomeCompleto, $telefoneFormatado);
        mensagem('sucesso', 'Contato cadastrado com sucesso.');
        redirecionar('nova-conversa.php');
    }
}

$contatos = listarContatos($pdo, $busca);

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <header class="barra-superior">
    <div class="barra-superior-conteudo">
      <div class="titulo-com-voltar">
        <a href="conversas.php" class="btn-icone" aria-label="Voltar para conversas"><i class="bi bi-arrow-left" aria-hidden="true"></i></a>
        <div><h1>Nova conversa</h1><p class="subtitulo-contagem"><?= count($contatos) ?> contatos</p></div>
      </div>
      <nav class="nav-atalhos" aria-label="Navegação do site">
        <a href="conversas.php">Conversas</a><a href="configuracoes.php">Configurações</a>
      </nav>
    </div>
  </header>
  <main class="contatos-layout">
    <section class="contatos-painel" aria-label="Lista de contatos">
      <div class="contatos-busca-area"><form class="busca-box" method="get" action="nova-conversa.php">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input type="search" id="busca-contato" name="busca" value="<?= esc($busca) ?>" placeholder="Pesquisar nome ou número" aria-label="Pesquisar nome ou número">
        <button type="submit" class="btn-icone" aria-label="Buscar">Buscar</button>
      </form></div>
      <div class="acoes-rapidas">
        <button type="button" class="acao-rapida-item" id="btn-novo-grupo"><span class="icone-circular"><i class="bi bi-people-fill" aria-hidden="true"></i></span><span class="acao-rapida-texto">Novo grupo</span></button>
        <button type="button" class="acao-rapida-item" id="btn-abrir-novo-contato"><span class="icone-circular"><i class="bi bi-person-plus-fill" aria-hidden="true"></i></span><span class="acao-rapida-texto">Novo contato</span></button>
      </div>
      <h2 class="lista-secao-titulo">Contatos no WhatsApp</h2>
      <?php if ($mensagemFlash): ?><p class="msg-sucesso" role="status"><?= esc($mensagemFlash['texto']) ?></p><?php endif; ?>
      <div id="lista-contatos" class="lista-contatos" aria-live="polite">
        <?php if (!$contatos): ?><p class="lista-vazia">Nenhum contato encontrado.</p><?php endif; ?>
        <?php $letra = ''; foreach ($contatos as $contato): $inicial = inicialTexto($contato['nome']); ?>
          <?php if ($letra !== $inicial): $letra = $inicial; ?><div class="grupo-letra"><?= esc($letra) ?></div><?php endif; ?>
          <div class="contato-item">
            <a class="contato-principal" href="conversas.php?contato=<?= (int)$contato['id'] ?>">
              <img class="contato-avatar" src="img/avatar-padrao.svg" alt="Avatar padrão de <?= esc($contato['nome']) ?>" width="49" height="49">
              <div class="contato-info"><p class="contato-nome"><?= esc($contato['nome']) ?></p><p class="contato-recado"><?= esc($contato['recado'] ?: 'Disponível') ?> · <?= esc($contato['telefone']) ?></p></div>
            </a>
            <a class="contato-link-perfil" href="perfil.php?id=<?= (int)$contato['id'] ?>" aria-label="Ver perfil de <?= esc($contato['nome']) ?>">Perfil</a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <aside class="contatos-boas-vindas" aria-label="Instruções"><div class="boas-vindas-conteudo"><i class="bi bi-whatsapp" aria-hidden="true"></i><h2>WhatsApp Web</h2><p>Selecione um contato ao lado para começar a conversar ou cadastre um novo contato.</p></div></aside>
  </main>
  <div class="modal-overlay <?= $erros ? 'aberto' : '' ?>" id="modal-novo-contato" role="dialog" aria-modal="true" aria-labelledby="titulo-modal-contato">
    <div class="modal-caixa"><div class="modal-cabecalho"><h2 id="titulo-modal-contato">Novo contato</h2><button type="button" class="btn-fechar-modal" id="btn-fechar-modal-contato" aria-label="Fechar janela"><i class="bi bi-x-lg" aria-hidden="true"></i></button></div>
      <form id="form-novo-contato" action="nova-conversa.php" method="post" novalidate>
        <div class="campo-grupo"><label for="novo-nome">Nome *</label><input type="text" id="novo-nome" name="nome" class="campo-controle" value="<?= esc($nome) ?>" maxlength="100" required><span class="erro-campo" role="alert"><?= esc($erros['nome'] ?? '') ?></span></div>
        <div class="campo-grupo"><label for="novo-sobrenome">Sobrenome</label><input type="text" id="novo-sobrenome" name="sobrenome" class="campo-controle" value="<?= esc($sobrenome) ?>" maxlength="100"><span class="erro-campo" role="alert"><?= esc($erros['sobrenome'] ?? '') ?></span></div>
        <div class="campo-grupo"><label for="novo-telefone">Telefone *</label><input type="tel" id="novo-telefone" name="telefone" class="campo-controle" value="<?= esc($telefone) ?>" placeholder="(49) 99999-0001" required><span class="erro-campo" role="alert"><?= esc($erros['telefone'] ?? '') ?></span></div>
        <?php if ($erros): ?><p class="erro-geral" role="alert">Corrija os campos destacados para salvar.</p><?php endif; ?>
        <div class="modal-acoes"><button type="button" class="btn btn-secundario" id="btn-cancelar-contato">Cancelar</button><button type="submit" class="btn btn-primario">Salvar</button></div>
      </form>
    </div>
  </div>
  <div class="toast-aviso" id="toast-contatos" role="status" aria-live="polite"></div>
<?php require_once __DIR__ . '/includes/rodape.php'; ?>
