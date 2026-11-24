<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/verifica-admin.php';

$motivos = [
    'spam'               => 'Spam',
    'assedio'            => 'Assédio',
    'conteudo-improprio' => 'Conteúdo impróprio',
    'perfil-falso'       => 'Perfil falso',
    'outro'              => 'Outro',
];

$busca = limpar(filter_input(INPUT_GET, 'busca', FILTER_UNSAFE_RAW) ?? '');
$erros = [];
$abrirModal = false;
$form = ['acao' => 'criar', 'id' => 0, 'nome' => '', 'telefone' => '', 'recado' => '', 'bloqueado' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = limpar($_POST['acao'] ?? '');
    $id = (int)filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($acao === 'excluir') {
        if ($id > 0) {
            excluirContatoPainel($pdo, $id);
            mensagem('sucesso', 'Contato excluído.');
        }
        redirecionar('painel.php');
    }

    if ($acao === 'criar' || $acao === 'editar') {
        $form = [
            'acao'      => $acao,
            'id'        => $id,
            'nome'      => limpar($_POST['nome'] ?? ''),
            'telefone'  => limpar($_POST['telefone'] ?? ''),
            'recado'    => limpar($_POST['recado'] ?? ''),
            'bloqueado' => isset($_POST['bloqueado']) ? 1 : 0,
        ];
        $erros = validarContato($pdo, $form, $acao === 'editar' ? $id : 0);

        if (!$erros) {
            if ($acao === 'criar') {
                inserirContatoPainel($pdo, $form);
                mensagem('sucesso', 'Contato cadastrado.');
            } else {
                atualizarContatoPainel($pdo, $id, $form);
                mensagem('sucesso', 'Contato atualizado.');
            }
            redirecionar('painel.php');
        }
        $abrirModal = true;
    }
}

$resumo = resumoPainel($pdo);
$contatos = listarContatosPainel($pdo, $busca);
$denuncias = listarDenuncias($pdo);
$flash = mensagem();

$tituloPagina = 'Painel do administrador – WhatsApp Web';
$cssPagina    = 'css/admin-painel.css';
$caminhoBase  = '../';
$scriptsHead  = ['js/tema.js'];
$scriptsDefer = ['js/admin-painel.js'];
$tipoRodape   = 'aviso';

require_once __DIR__ . '/../includes/cabecalho.php';
?>
  <div class="admin">
    <header class="admin-topo">
      <button type="button" class="btn-icone btn-menu" id="btn-menu" aria-label="Abrir menu" aria-expanded="false" aria-controls="menu-lateral"><i class="bi bi-list"></i></button>
      <h1>Painel do administrador</h1>
      <span class="admin-usuario"><i class="bi bi-person-circle"></i> <?= esc(adminLogado()['nome'] ?? '') ?></span>
    </header>

    <nav class="admin-menu" id="menu-lateral" aria-label="Menu do administrador">
      <ul>
        <li><a href="#contatos" aria-current="page"><i class="bi bi-person-lines-fill"></i> Contatos</a></li>
        <li><a href="#denuncias"><i class="bi bi-flag"></i> Denúncias</a></li>
        <li><a href="logout.php"><i class="bi bi-box-arrow-left"></i> Sair</a></li>
      </ul>
    </nav>

    <main class="admin-conteudo">
      <section class="resumo" id="resumo" aria-label="Resumo">
        <article class="cartao-resumo">
          <i class="bi bi-people-fill"></i>
          <div>
            <p class="cartao-resumo-numero"><?= $resumo['contatos'] ?></p>
            <p class="cartao-resumo-titulo">Total de contatos</p>
          </div>
        </article>
        <article class="cartao-resumo">
          <i class="bi bi-chat-dots-fill"></i>
          <div>
            <p class="cartao-resumo-numero"><?= $resumo['mensagens'] ?></p>
            <p class="cartao-resumo-titulo">Total de mensagens</p>
          </div>
        </article>
        <article class="cartao-resumo">
          <i class="bi bi-slash-circle-fill"></i>
          <div>
            <p class="cartao-resumo-numero"><?= $resumo['bloqueados'] ?></p>
            <p class="cartao-resumo-titulo">Contatos bloqueados</p>
          </div>
        </article>
        <article class="cartao-resumo">
          <i class="bi bi-flag-fill"></i>
          <div>
            <p class="cartao-resumo-numero"><?= $resumo['denuncias'] ?></p>
            <p class="cartao-resumo-titulo">Denúncias</p>
          </div>
        </article>
      </section>

      <section class="contatos" id="contatos" aria-labelledby="titulo-contatos">
        <div class="contatos-topo">
          <h2 id="titulo-contatos">Contatos</h2>
          <form class="busca" action="painel.php" method="get" role="search">
            <i class="bi bi-search"></i>
            <label class="visualmente-oculto" for="busca-contatos">Pesquisar contatos</label>
            <input type="search" id="busca-contatos" name="busca" value="<?= esc($busca) ?>" placeholder="Pesquisar por nome ou telefone">
          </form>
          <button type="button" class="btn btn-primario btn-novo" id="btn-novo"><i class="bi bi-plus-lg"></i> Novo contato</button>
        </div>

        <table class="tabela">
          <caption>
            <?= $busca !== '' ? 'Resultado da busca por "' . esc($busca) . '"' : 'Contatos cadastrados' ?>
            <?php if ($busca !== ''): ?> · <a href="painel.php">Limpar busca</a><?php endif; ?>
          </caption>
          <thead>
            <tr>
              <th scope="col">Nome</th>
              <th scope="col">Telefone</th>
              <th scope="col">Recado</th>
              <th scope="col">Mensagens</th>
              <th scope="col">Situação</th>
              <th scope="col">Ações</th>
            </tr>
          </thead>
          <tbody id="tabela-corpo">
            <?php if (!$contatos): ?>
              <tr><td colspan="6">Nenhum contato encontrado</td></tr>
            <?php endif; ?>
            <?php foreach ($contatos as $c): ?>
              <tr>
                <td><span class="painel-contato"><img src="../<?= esc(fotoContato((int)$c['id'])) ?>" alt="" width="32" height="32"><?= esc($c['nome']) ?></span></td>
                <td><?= esc($c['telefone']) ?></td>
                <td><?= esc($c['recado']) ?></td>
                <td><?= (int)$c['total_mensagens'] ?></td>
                <td><span class="situacao<?= $c['bloqueado'] ? ' bloqueado' : '' ?>"><?= $c['bloqueado'] ? 'Bloqueado' : 'Ativo' ?></span></td>
                <td>
                  <div class="acoes">
                    <button type="button" class="btn btn-secundario" data-acao="editar"
                      data-id="<?= (int)$c['id'] ?>"
                      data-nome="<?= esc($c['nome']) ?>"
                      data-telefone="<?= esc($c['telefone']) ?>"
                      data-recado="<?= esc($c['recado']) ?>"
                      data-bloqueado="<?= (int)$c['bloqueado'] ?>">Editar</button>
                    <button type="button" class="btn btn-perigo" data-acao="excluir"
                      data-id="<?= (int)$c['id'] ?>"
                      data-nome="<?= esc($c['nome']) ?>">Excluir</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>

      <section class="contatos" id="denuncias" aria-labelledby="titulo-denuncias">
        <div class="contatos-topo">
          <h2 id="titulo-denuncias">Denúncias recebidas</h2>
        </div>
        <table class="tabela">
          <caption>Denúncias enviadas pelos usuários na página de perfil</caption>
          <thead>
            <tr>
              <th scope="col">Contato</th>
              <th scope="col">Motivo</th>
              <th scope="col">Descrição</th>
              <th scope="col">Bloqueou</th>
              <th scope="col">Data</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$denuncias): ?>
              <tr><td colspan="5">Nenhuma denúncia recebida</td></tr>
            <?php endif; ?>
            <?php foreach ($denuncias as $d): ?>
              <tr>
                <td><?= esc($d['contato']) ?></td>
                <td><?= esc($motivos[$d['motivo']] ?? $d['motivo']) ?></td>
                <td><?= esc($d['descricao'] ?: '—') ?></td>
                <td><?= $d['bloqueou'] ? 'Sim' : 'Não' ?></td>
                <td><?= esc(date('d/m/Y H:i', strtotime($d['criada_em']))) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <div class="modal" id="modal-contato" role="dialog" aria-modal="true" aria-labelledby="titulo-modal" <?= $abrirModal ? '' : 'hidden' ?>>
    <div class="modal-conteudo">
      <h2 class="modal-titulo" id="titulo-modal"><?= $form['acao'] === 'editar' ? 'Editar contato' : 'Novo contato' ?></h2>
      <form id="form-contato" action="painel.php" method="post" novalidate>
        <input type="hidden" name="acao" id="campo-acao" value="<?= esc($form['acao']) ?>">
        <input type="hidden" name="id" id="campo-id" value="<?= (int)$form['id'] ?>">
        <div class="grupo-campo">
          <label for="campo-nome">Nome</label>
          <input type="text" class="campo<?= isset($erros['nome']) ? ' invalido' : '' ?>" id="campo-nome" name="nome" maxlength="100" required value="<?= esc($form['nome']) ?>">
          <p class="msg-erro" id="erro-nome"><?= esc($erros['nome'] ?? '') ?></p>
        </div>
        <div class="grupo-campo">
          <label for="campo-telefone">Telefone</label>
          <input type="tel" class="campo<?= isset($erros['telefone']) ? ' invalido' : '' ?>" id="campo-telefone" name="telefone" placeholder="(49) 99999-0001" pattern="\([0-9]{2}\) [0-9]{5}-[0-9]{4}" required value="<?= esc($form['telefone']) ?>">
          <p class="msg-erro" id="erro-telefone"><?= esc($erros['telefone'] ?? '') ?></p>
        </div>
        <div class="grupo-campo">
          <label for="campo-recado">Recado</label>
          <input type="text" class="campo<?= isset($erros['recado']) ? ' invalido' : '' ?>" id="campo-recado" name="recado" maxlength="150" value="<?= esc($form['recado']) ?>">
          <p class="msg-erro" id="erro-recado"><?= esc($erros['recado'] ?? '') ?></p>
        </div>
        <label class="grupo-check">
          <input type="checkbox" id="campo-bloqueado" name="bloqueado" <?= $form['bloqueado'] ? 'checked' : '' ?>>
          Bloqueado
        </label>
        <div class="modal-acoes">
          <button type="button" class="btn btn-secundario" id="contato-cancelar">Cancelar</button>
          <button type="submit" class="btn btn-primario">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="modal-excluir" role="dialog" aria-modal="true" aria-labelledby="titulo-excluir" hidden>
    <div class="modal-conteudo">
      <h2 class="modal-titulo" id="titulo-excluir">Excluir contato?</h2>
      <p class="modal-texto" id="texto-excluir"></p>
      <form action="painel.php" method="post" class="modal-acoes">
        <input type="hidden" name="acao" value="excluir">
        <input type="hidden" name="id" id="excluir-id" value="">
        <button type="button" class="btn btn-secundario" id="excluir-cancelar">Cancelar</button>
        <button type="submit" class="btn btn-perigo">Excluir</button>
      </form>
    </div>
  </div>

  <div class="toast<?= $flash ? ' visivel' : '' ?>" id="toast" role="status" aria-live="polite"><?= esc($flash['texto'] ?? '') ?></div>
<?php require_once __DIR__ . '/../includes/rodape.php'; ?>
