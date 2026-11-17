/* ==========================================================================
   configuracoes.js — Interruptor de tema, busca, perfil e desconectar
   ========================================================================== */

(function () {
  const CHAVE_PERFIL_USUARIO = 'whatsapp_clone_perfil_usuario';

  const inputBusca = document.getElementById('busca-config');
  const itensConfig = document.querySelectorAll('.config-item-wrapper');
  const toggleTema = document.getElementById('toggle-tema');
  const statusTema = document.getElementById('status-tema');
  const btnAbrirAtalhos = document.getElementById('btn-abrir-atalhos');
  const secaoAtalhos = document.getElementById('secao-atalhos');
  const btnDesconectar = document.getElementById('btn-desconectar');

  const btnEditarPerfil = document.getElementById('btn-editar-perfil');
  const usuarioNomeEl = document.getElementById('usuario-nome');
  const usuarioRecadoEl = document.getElementById('usuario-recado');

  const modalUsuario = document.getElementById('modal-perfil-usuario');
  const btnFecharModalUsuario = document.getElementById('btn-fechar-modal-usuario');
  const btnCancelarUsuario = document.getElementById('btn-cancelar-usuario');
  const formUsuario = document.getElementById('form-perfil-usuario');
  const inputUsuarioNome = document.getElementById('input-usuario-nome');
  const inputUsuarioRecado = document.getElementById('input-usuario-recado');
  const erroUsuarioNome = document.getElementById('erro-usuario-nome');
  const toastEl = document.getElementById('toast-config');

  let toastTimer = null;

  function mostrarToast(msg) {
    toastEl.textContent = msg;
    toastEl.classList.add('visivel');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () {
      toastEl.classList.remove('visivel');
    }, 3500);
  }

  function sincronizarTema() {
    const temaAtual = localStorage.getItem('tema') === 'escuro' ? 'escuro' : 'claro';
    toggleTema.checked = temaAtual === 'escuro';
    statusTema.textContent = temaAtual === 'escuro' ? 'Escuro ativado' : 'Claro ativado';
  }

  toggleTema.addEventListener('change', function () {
    const novoTema = toggleTema.checked ? 'escuro' : 'claro';
    localStorage.setItem('tema', novoTema);
    if (novoTema === 'escuro') {
      document.documentElement.setAttribute('data-tema', 'escuro');
    } else {
      document.documentElement.removeAttribute('data-tema');
    }
    sincronizarTema();
    mostrarToast(`Tema ${novoTema} aplicado.`);
  });

  // Busca nas configurações
  inputBusca.addEventListener('input', function () {
    const termo = inputBusca.value.trim().toLowerCase();
    itensConfig.forEach(function (item) {
      const palavras = item.getAttribute('data-termo') || '';
      const texto = item.textContent.toLowerCase();
      item.hidden = !(palavras.includes(termo) || texto.includes(termo));
    });
  });

  // Rolar/focar tabela de atalhos (útil especialmente no mobile)
  btnAbrirAtalhos.addEventListener('click', function () {
    secaoAtalhos.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  // Itens ilustrativos da lista
  document.querySelectorAll('.config-item[data-info]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const nome = btn.getAttribute('data-info');
      mostrarToast(`${nome}: opção demonstrativa no protótipo.`);
    });
  });

  // Desconectar e voltar para a página de Entrada
  btnDesconectar.addEventListener('click', function () {
    localStorage.removeItem('whatsapp_clone_manter_conectado');
    mostrarToast('Sessão encerrada. Redirecionando...');
    setTimeout(function () {
      window.location.href = 'index.html';
    }, 600);
  });

  // Carregar e editar perfil do usuário localmente
  function carregarPerfilUsuario() {
    try {
      const salvo = JSON.parse(localStorage.getItem(CHAVE_PERFIL_USUARIO));
      if (salvo && salvo.nome) {
        usuarioNomeEl.textContent = salvo.nome;
        usuarioRecadoEl.textContent = salvo.recado || 'Disponível';
      }
    } catch (e) {
      // Mantém padrão
    }
  }

  function abrirModalUsuario() {
    erroUsuarioNome.textContent = '';
    inputUsuarioNome.classList.remove('campo-invalido');
    inputUsuarioNome.value = usuarioNomeEl.textContent;
    inputUsuarioRecado.value = usuarioRecadoEl.textContent;
    modalUsuario.classList.add('aberto');
    inputUsuarioNome.focus();
  }

  function fecharModalUsuario() {
    modalUsuario.classList.remove('aberto');
    btnEditarPerfil.focus();
  }

  btnEditarPerfil.addEventListener('click', abrirModalUsuario);
  btnFecharModalUsuario.addEventListener('click', fecharModalUsuario);
  btnCancelarUsuario.addEventListener('click', fecharModalUsuario);

  modalUsuario.addEventListener('click', function (evento) {
    if (evento.target === modalUsuario) {
      fecharModalUsuario();
    }
  });

  document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape' && modalUsuario.classList.contains('aberto')) {
      fecharModalUsuario();
    }
  });

  formUsuario.addEventListener('submit', function (evento) {
    evento.preventDefault();
    const nome = inputUsuarioNome.value.trim();
    const recado = inputUsuarioRecado.value.trim() || 'Disponível';

    if (!nome) {
      erroUsuarioNome.textContent = 'O nome não pode ficar vazio.';
      inputUsuarioNome.classList.add('campo-invalido');
      inputUsuarioNome.focus();
      return;
    }

    localStorage.setItem(CHAVE_PERFIL_USUARIO, JSON.stringify({ nome: nome, recado: recado }));
    carregarPerfilUsuario();
    fecharModalUsuario();
    mostrarToast('Perfil atualizado.');
  });

  sincronizarTema();
  carregarPerfilUsuario();
})();