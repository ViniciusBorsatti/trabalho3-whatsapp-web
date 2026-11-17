/* ==========================================================================
   perfil.js — Perfil do contato, favoritar, bloquear (RN03) e modal denúncia
   ========================================================================== */

(function () {
  const CHAVE_FAVORITOS = 'whatsapp_clone_favoritos';
  const CHAVE_BLOQUEADOS = 'whatsapp_clone_bloqueados';
  const CHAVE_DENUNCIAS = 'whatsapp_clone_denuncias';

  const avatarEl = document.getElementById('perfil-avatar');
  const nomeEl = document.getElementById('perfil-nome');
  const telefoneEl = document.getElementById('perfil-telefone');
  const recadoEl = document.getElementById('perfil-recado');
  const badgeFavorito = document.getElementById('badge-favorito');
  const badgeBloqueado = document.getElementById('badge-bloqueado');

  const btnFavoritar = document.getElementById('btn-favoritar');
  const iconeFavoritar = document.getElementById('icone-favoritar');
  const textoFavoritar = document.getElementById('texto-favoritar');
  const btnBloquear = document.getElementById('btn-bloquear');
  const textoBloquear = document.getElementById('texto-bloquear');
  const btnAbrirDenuncia = document.getElementById('btn-abrir-denuncia');
  const textoDenunciar = document.getElementById('texto-denunciar');
  const btnApagarConversa = document.getElementById('btn-apagar-conversa');

  const modalDenuncia = document.getElementById('modal-denuncia');
  const modalTitulo = document.getElementById('modal-denuncia-titulo');
  const btnFecharModal = document.getElementById('btn-fechar-modal');
  const btnCancelarDenuncia = document.getElementById('btn-cancelar-denuncia');
  const formDenuncia = document.getElementById('form-denuncia');
  const selectMotivo = document.getElementById('motivo-denuncia');
  const erroMotivo = document.getElementById('erro-motivo');
  const textareaDesc = document.getElementById('descricao-denuncia');
  const contadorCaracteres = document.getElementById('contador-caracteres');
  const checkboxBloquearTambem = document.getElementById('bloquear-tambem');
  const toastEl = document.getElementById('toast-perfil');

  let contatoAtual = {
    id: 1,
    nome: 'Ana Souza',
    telefone: '(49) 99999-0001',
    recado: 'Disponível',
    favorito: true,
    bloqueado: false
  };

  let toastTimeout = null;

  /**
   * Exibe aviso escuro temporário no canto da tela (some sozinho).
   */
  function mostrarToast(mensagem) {
    toastEl.textContent = mensagem;
    toastEl.classList.add('visivel');
    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(function () {
      toastEl.classList.remove('visivel');
    }, 3500);
  }

  function lerListaStorage(chave) {
    try {
      return JSON.parse(localStorage.getItem(chave)) || [];
    } catch (e) {
      return [];
    }
  }

  function salvarListaStorage(chave, lista) {
    localStorage.setItem(chave, JSON.stringify(lista));
  }

  /**
   * Atualiza a interface com os dados e estados (favorito / bloqueado) do contato.
   */
  function renderizarContato() {
    const favoritos = lerListaStorage(CHAVE_FAVORITOS);
    const bloqueados = lerListaStorage(CHAVE_BLOQUEADOS);

    const ehFavorito = favoritos.includes(contatoAtual.id);
    const estaBloqueado = bloqueados.includes(contatoAtual.id);

    nomeEl.textContent = contatoAtual.nome;
    telefoneEl.textContent = `+55 ${contatoAtual.telefone.replace(/[()]/g, '')}`;
    recadoEl.textContent = contatoAtual.recado || 'Disponível';
    avatarEl.alt = `Avatar padrão de ${contatoAtual.nome}`;

    badgeFavorito.hidden = !ehFavorito;
    badgeBloqueado.hidden = !estaBloqueado;

    iconeFavoritar.className = ehFavorito ? 'bi bi-star-fill' : 'bi bi-star';
    textoFavoritar.textContent = ehFavorito ? 'Remover dos favoritos' : 'Favoritar';
    textoBloquear.textContent = estaBloqueado
      ? `Desbloquear ${contatoAtual.nome}`
      : `Bloquear ${contatoAtual.nome}`;
    textoDenunciar.textContent = `Denunciar ${contatoAtual.nome}`;
    modalTitulo.textContent = `Denunciar ${contatoAtual.nome}?`;
  }

  /**
   * Carrega o contato selecionado via ?id= a partir de dados/contatos.json.
   */
  async function carregarContato() {
    const params = new URLSearchParams(window.location.search);
    const idParam = Number(params.get('id')) || 1;

    try {
      const resposta = await fetch('dados/contatos.json');
      if (resposta.ok) {
        const contatos = await resposta.json();
        const encontrado = contatos.find(function (c) {
          return Number(c.id) === idParam;
        });
        if (encontrado) {
          contatoAtual = encontrado;
        }
      }
    } catch (e) {
      // Mantém contato padrão caso não esteja rodando via servidor HTTP
    }

    // Inicializa estado padrão no localStorage na primeira visita se não existir
    if (localStorage.getItem(CHAVE_FAVORITOS) === null && contatoAtual.favorito) {
      salvarListaStorage(CHAVE_FAVORITOS, [contatoAtual.id]);
    }
    if (localStorage.getItem(CHAVE_BLOQUEADOS) === null && contatoAtual.bloqueado) {
      salvarListaStorage(CHAVE_BLOQUEADOS, [contatoAtual.id]);
    }

    renderizarContato();
  }

  // Alternar favorito
  btnFavoritar.addEventListener('click', function () {
    let favoritos = lerListaStorage(CHAVE_FAVORITOS);
    if (favoritos.includes(contatoAtual.id)) {
      favoritos = favoritos.filter(function (id) { return id !== contatoAtual.id; });
      mostrarToast(`${contatoAtual.nome} removido dos favoritos.`);
    } else {
      favoritos.push(contatoAtual.id);
      mostrarToast(`${contatoAtual.nome} adicionado aos favoritos.`);
    }
    salvarListaStorage(CHAVE_FAVORITOS, favoritos);
    renderizarContato();
  });

  // Alternar bloqueio (RN03)
  btnBloquear.addEventListener('click', function () {
    let bloqueados = lerListaStorage(CHAVE_BLOQUEADOS);
    if (bloqueados.includes(contatoAtual.id)) {
      bloqueados = bloqueados.filter(function (id) { return id !== contatoAtual.id; });
      mostrarToast(`${contatoAtual.nome} foi desbloqueado.`);
    } else {
      bloqueados.push(contatoAtual.id);
      mostrarToast(`Você bloqueou ${contatoAtual.nome}.`);
    }
    salvarListaStorage(CHAVE_BLOQUEADOS, bloqueados);
    renderizarContato();
  });

  // Botões ilustrativos
  document.querySelectorAll('.atalho-visual').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const acao = btn.getAttribute('data-visual');
      mostrarToast(`${acao} é apenas ilustrativo neste protótipo.`);
    });
  });

  btnApagarConversa.addEventListener('click', function () {
    mostrarToast(`Conversa com ${contatoAtual.nome} limpa localmente.`);
  });

  // Abrir e fechar modal de denúncia
  function abrirModal() {
    erroMotivo.textContent = '';
    selectMotivo.classList.remove('campo-invalido');
    modalDenuncia.classList.add('aberto');
    selectMotivo.focus();
  }

  function fecharModal() {
    modalDenuncia.classList.remove('aberto');
    btnAbrirDenuncia.focus();
  }

  btnAbrirDenuncia.addEventListener('click', abrirModal);
  btnFecharModal.addEventListener('click', fecharModal);
  btnCancelarDenuncia.addEventListener('click', fecharModal);

  modalDenuncia.addEventListener('click', function (evento) {
    if (evento.target === modalDenuncia) {
      fecharModal();
    }
  });

  document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape' && modalDenuncia.classList.contains('aberto')) {
      fecharModal();
    }
  });

  // Contador de caracteres (maxlength="500")
  textareaDesc.addEventListener('input', function () {
    contadorCaracteres.textContent = `${textareaDesc.value.length}/500`;
  });

  // Validação e envio do formulário de denúncia (sem alert)
  formDenuncia.addEventListener('submit', function (evento) {
    evento.preventDefault();

    if (!selectMotivo.value) {
      erroMotivo.textContent = 'Selecione um motivo para enviar a denúncia.';
      selectMotivo.classList.add('campo-invalido');
      selectMotivo.focus();
      return;
    }

    erroMotivo.textContent = '';
    selectMotivo.classList.remove('campo-invalido');

    if (checkboxBloquearTambem.checked) {
      const bloqueados = lerListaStorage(CHAVE_BLOQUEADOS);
      if (!bloqueados.includes(contatoAtual.id)) {
        bloqueados.push(contatoAtual.id);
        salvarListaStorage(CHAVE_BLOQUEADOS, bloqueados);
      }
    }

    const denuncias = lerListaStorage(CHAVE_DENUNCIAS);
    denuncias.push({
      contatoId: contatoAtual.id,
      contatoNome: contatoAtual.nome,
      motivo: selectMotivo.value,
      descricao: textareaDesc.value.trim(),
      data: new Date().toISOString()
    });
    salvarListaStorage(CHAVE_DENUNCIAS, denuncias);

    formDenuncia.reset();
    contadorCaracteres.textContent = '0/500';
    fecharModal();
    renderizarContato();
    mostrarToast('Denúncia enviada com sucesso.');
  });

  carregarContato();
})();