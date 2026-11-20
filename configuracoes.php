<?php
require_once __DIR__ . '/includes/funcoes.php';

$tituloPagina = 'WhatsApp Web — Configurações';
$cssPagina    = 'css/configuracoes.css';
$caminhoBase  = '';
$scriptsHead  = ['js/tema.js'];
$scriptsDefer = ['js/configuracoes.js'];
$tipoRodape   = 'educacional';

require_once __DIR__ . '/includes/cabecalho.php';
?>
  <header class="barra-superior">
    <div class="barra-superior-conteudo">
      <div class="titulo-com-voltar">
        <a href="conversas.php" class="btn-icone" aria-label="Voltar para conversas">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
        </a>
        <h1>Configurações</h1>
      </div>
      <nav class="nav-atalhos" aria-label="Navegação do site">
        <a href="conversas.php">Conversas</a>
        <a href="nova-conversa.php">Contatos</a>
        <a href="perfil.php">Perfil</a>
      </nav>
    </div>
  </header>

  <main class="config-layout">
    <section class="config-painel" aria-label="Menu de configurações">
      <div class="config-busca-area">
        <div class="busca-box">
          <i class="bi bi-search" aria-hidden="true"></i>
          <input
            type="search"
            id="busca-config"
            placeholder="Pesquisar configurações"
            aria-label="Pesquisar configurações"
          >
        </div>
      </div>

      <button type="button" class="perfil-usuario-card" id="btn-editar-perfil">
        <img
          src="img/avatar-padrao.svg"
          alt="Avatar padrão do usuário Jadson Butzk"
          width="60"
          height="60"
        >
        <div class="perfil-usuario-texto">
          <strong id="usuario-nome">Jadson Butzk</strong>
          <span id="usuario-recado">Disponível</span>
        </div>
        <i class="bi bi-pencil-square icone-editar" aria-hidden="true"></i>
      </button>

      <ul class="config-lista" id="config-lista">
        <li class="config-item-wrapper" data-termo="conta segurança dados">
          <button type="button" class="config-item" data-info="Conta">
            <i class="bi bi-key" aria-hidden="true"></i>
            <div>
              <strong>Conta</strong>
              <span>Segurança, dados da conta</span>
            </div>
          </button>
        </li>

        <li class="config-item-wrapper" data-termo="privacidade bloqueados leitura">
          <button type="button" class="config-item" data-info="Privacidade">
            <i class="bi bi-lock" aria-hidden="true"></i>
            <div>
              <strong>Privacidade</strong>
              <span>Contatos bloqueados, confirmação de leitura</span>
            </div>
          </button>
        </li>

        <li class="config-item-wrapper" data-termo="tema claro escuro aparencia">
          <div class="config-item config-item-tema">
            <div class="config-tema-rotulo">
              <i class="bi bi-moon" aria-hidden="true"></i>
              <div>
                <strong>Tema</strong>
                <span id="status-tema">Claro / Escuro</span>
              </div>
            </div>
            <label class="switch-tema" for="toggle-tema" aria-label="Alternar entre tema claro e escuro">
              <input type="checkbox" id="toggle-tema" role="switch">
              <span class="switch-trilho"></span>
            </label>
          </div>
        </li>

        <li class="config-item-wrapper" data-termo="conversas papel de parede fonte">
          <button type="button" class="config-item" data-info="Conversas">
            <i class="bi bi-chat-square-text" aria-hidden="true"></i>
            <div>
              <strong>Conversas</strong>
              <span>Papel de parede, tamanho da fonte</span>
            </div>
          </button>
        </li>

        <li class="config-item-wrapper" data-termo="notificacoes sons alertas">
          <button type="button" class="config-item" data-info="Notificações">
            <i class="bi bi-bell" aria-hidden="true"></i>
            <div>
              <strong>Notificações</strong>
              <span>Sons e alertas</span>
            </div>
          </button>
        </li>

        <li class="config-item-wrapper" data-termo="atalhos teclado lista">
          <button type="button" class="config-item" id="btn-abrir-atalhos">
            <i class="bi bi-keyboard" aria-hidden="true"></i>
            <div>
              <strong>Atalhos do teclado</strong>
              <span>Lista de atalhos</span>
            </div>
          </button>
        </li>

        <li class="config-item-wrapper" data-termo="ajuda sobre clone">
          <button type="button" class="config-item" data-info="Ajuda">
            <i class="bi bi-question-circle" aria-hidden="true"></i>
            <div>
              <strong>Ajuda</strong>
              <span>Central de ajuda, sobre o clone</span>
            </div>
          </button>
        </li>

        <li class="config-item-wrapper" data-termo="dispositivos conectados computadores">
          <a href="conectar.php" class="config-item">
            <i class="bi bi-laptop" aria-hidden="true"></i>
            <div>
              <strong>Dispositivos conectados</strong>
              <span>Computadores conectados</span>
            </div>
          </a>
        </li>

        <li class="config-item-wrapper" data-termo="desconectar sair encerrar sessao">
          <button type="button" class="config-item config-desconectar" id="btn-desconectar">
            <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
            <div>
              <strong>Desconectar</strong>
              <span>Encerrar sessão neste navegador</span>
            </div>
          </button>
        </li>
      </ul>
    </section>

    <section class="atalhos-painel" id="secao-atalhos" aria-labelledby="titulo-atalhos">
      <div class="atalhos-cartao">
        <div class="atalhos-cabecalho">
          <h2 id="titulo-atalhos">Atalhos do teclado</h2>
          <span class="atalhos-subtitulo">Atalhos padronizados do WhatsApp Web</span>
        </div>

        <div class="tabela-responsiva">
          <table class="tabela-atalhos">
            <thead>
              <tr>
                <th scope="col">Ação</th>
                <th scope="col">Atalho</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Nova conversa</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>N</kbd></td>
              </tr>
              <tr>
                <td>Pesquisar</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>/</kbd></td>
              </tr>
              <tr>
                <td>Pesquisar na conversa</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>F</kbd></td>
              </tr>
              <tr>
                <td>Próxima conversa</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>Tab</kbd></td>
              </tr>
              <tr>
                <td>Conversa anterior</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>Tab</kbd></td>
              </tr>
              <tr>
                <td>Marcar como não lida</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>U</kbd></td>
              </tr>
              <tr>
                <td>Fixar conversa</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>P</kbd></td>
              </tr>
              <tr>
                <td>Apagar conversa</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>Backspace</kbd></td>
              </tr>
              <tr>
                <td>Abrir perfil</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>P</kbd></td>
              </tr>
              <tr>
                <td>Configurações</td>
                <td><kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>,</kbd></td>
              </tr>
              <tr>
                <td>Fechar</td>
                <td><kbd>Esc</kbd></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <div
    class="modal-overlay"
    id="modal-perfil-usuario"
    role="dialog"
    aria-modal="true"
    aria-labelledby="titulo-modal-usuario"
  >
    <div class="modal-caixa">
      <div class="modal-cabecalho">
        <h2 id="titulo-modal-usuario">Editar perfil</h2>
        <button type="button" class="btn-fechar-modal" id="btn-fechar-modal-usuario" aria-label="Fechar janela">
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>

      <form id="form-perfil-usuario" novalidate>
        <div class="campo-grupo">
          <label for="input-usuario-nome">Seu nome *</label>
          <input type="text" id="input-usuario-nome" class="campo-controle" required>
          <span class="erro-campo" id="erro-usuario-nome" role="alert"></span>
        </div>

        <div class="campo-grupo">
          <label for="input-usuario-recado">Recado</label>
          <input type="text" id="input-usuario-recado" class="campo-controle" maxlength="120">
        </div>

        <div class="modal-acoes">
          <button type="button" class="btn btn-secundario" id="btn-cancelar-usuario">Cancelar</button>
          <button type="submit" class="btn btn-primario">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="toast-aviso" id="toast-config" role="status" aria-live="polite"></div>

<?php require_once __DIR__ . '/includes/rodape.php'; ?>