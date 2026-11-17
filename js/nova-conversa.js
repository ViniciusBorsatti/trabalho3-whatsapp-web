(function () {
  const CHAVE_CONTATOS_EXTRAS = 'whatsapp_clone_contatos_extras';

  const listaEl = document.getElementById('lista-contatos');
  const contagemEl = document.getElementById('contagem-contatos');
  const inputBusca = document.getElementById('busca-contato');
  const btnNovoGrupo = document.getElementById('btn-novo-grupo');
  const btnAbrirModal = document.getElementById('btn-abrir-novo-contato');

  const modal = document.getElementById('modal-novo-contato');
  const btnFecharModal = document.getElementById('btn-fechar-modal-contato');
  const btnCancelar = document.getElementById('btn-cancelar-contato');
  const form = document.getElementById('form-novo-contato');

  const inputNome = document.getElementById('novo-nome');
  const inputSobrenome = document.getElementById('novo-sobrenome');
  const inputTelefone = document.getElementById('novo-telefone');
  const inputEmail = document.getElementById('novo-email');

  const erroNome = document.getElementById('erro-nome');
  const erroTelefone = document.getElementById('erro-telefone');
  const erroEmail = document.getElementById('erro-email');
  const erroGeral = document.getElementById('erro-geral-contato');
  const toastEl = document.getElementById('toast-contatos');

  let todosContatos = [];
  let toastTimer = null;

  function mostrarToast(msg) {
    toastEl.textContent = msg;
    toastEl.classList.add('visivel');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () {
      toastEl.classList.remove('visivel');
    }, 3500);
  }

  function normalizarTelefone(tel) {
    return tel.replace(/\D/g, '');
  }

  function lerContatosExtras() {
    try {
      return JSON.parse(localStorage.getItem(CHAVE_CONTATOS_EXTRAS)) || [];
    } catch (e) {
      return [];
    }
  }

  /**
   * Renderiza a lista de contatos em ordem alfabética, agrupada pela inicial.
   */
  function renderizarLista(filtro) {
    const termo = (filtro || '').trim().toLowerCase();
    const filtrados = todosContatos
      .filter(function (c) {
        const nomeMatch = c.nome.toLowerCase().includes(termo);
        const telMatch = c.telefone.toLowerCase().includes(termo);
        return nomeMatch || telMatch;
      })
      .sort(function (a, b) {
        return a.nome.localeCompare(b.nome, 'pt-BR');
      });

    contagemEl.textContent = `${todosContatos.length} contatos`;
    listaEl.innerHTML = '';

    if (filtrados.length === 0) {
      const vazio = document.createElement('p');
      vazio.className = 'lista-vazia';
      vazio.textContent = 'Nenhum contato encontrado.';
      listaEl.appendChild(vazio);
      return;
    }

    let letraAtual = '';

    filtrados.forEach(function (contato) {
      const inicial = contato.nome.charAt(0).toUpperCase();
      if (inicial !== letraAtual) {
        letraAtual = inicial;
        const divisor = document.createElement('div');
        divisor.className = 'grupo-letra';
        divisor.textContent = letraAtual;
        listaEl.appendChild(divisor);
      }

      const item = document.createElement('div');
      item.className = 'contato-item';

      const linkConversa = document.createElement('a');
      linkConversa.className = 'contato-principal';
      linkConversa.href = `conversas.html?contato=${encodeURIComponent(contato.id)}`;

      const img = document.createElement('img');
      img.className = 'contato-avatar';
      img.src = 'img/avatar-padrao.svg';
      img.alt = `Avatar padrão de ${contato.nome}`;
      img.width = 49;
      img.height = 49;

      const info = document.createElement('div');
      info.className = 'contato-info';

      const nomeEl = document.createElement('p');
      nomeEl.className = 'contato-nome';
      nomeEl.textContent = contato.nome;

      const recadoEl = document.createElement('p');
      recadoEl.className = 'contato-recado';
      recadoEl.textContent = `${contato.recado || 'Disponível'} · ${contato.telefone}`;

      info.appendChild(nomeEl);
      info.appendChild(recadoEl);
      linkConversa.appendChild(img);
      linkConversa.appendChild(info);

      const linkPerfil = document.createElement('a');
      linkPerfil.className = 'contato-link-perfil';
      linkPerfil.href = `perfil.html?id=${encodeURIComponent(contato.id)}`;
      linkPerfil.textContent = 'Perfil';
      linkPerfil.setAttribute('aria-label', `Ver perfil de ${contato.nome}`);

      item.appendChild(linkConversa);
      item.appendChild(linkPerfil);
      listaEl.appendChild(item);
    });
  }

  /**
   * Busca os contatos fictícios em dados/contatos.json via fetch.
   */
  async function carregarContatos() {
    let base = [];
    try {
      const resposta = await fetch('dados/contatos.json');
      if (resposta.ok) {
        base = await resposta.json();
      }
    } catch (e) {
      base = [];
    }

    const extras = lerContatosExtras();
    todosContatos = base.concat(extras);
    renderizarLista('');
  }

  // Busca em tempo real
  inputBusca.addEventListener('input', function () {
    renderizarLista(inputBusca.value);
  });

  // Ação visual de Novo grupo
  btnNovoGrupo.addEventListener('click', function () {
    mostrarToast('Novo grupo é apenas visual neste protótipo.');
  });

  // Controle do modal de novo contato
  function limparErrosFormulario() {
    erroNome.textContent = '';
    erroTelefone.textContent = '';
    erroEmail.textContent = '';
    erroGeral.hidden = true;
    [inputNome, inputTelefone, inputEmail].forEach(function (campo) {
      campo.classList.remove('campo-invalido');
    });
  }

  function abrirModal() {
    limparErrosFormulario();
    modal.classList.add('aberto');
    inputNome.focus();
  }

  function fecharModal() {
    modal.classList.remove('aberto');
    btnAbrirModal.focus();
  }

  btnAbrirModal.addEventListener('click', abrirModal);
  btnFecharModal.addEventListener('click', fecharModal);
  btnCancelar.addEventListener('click', fecharModal);

  modal.addEventListener('click', function (evento) {
    if (evento.target === modal) {
      fecharModal();
    }
  });

  document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape' && modal.classList.contains('aberto')) {
      fecharModal();
    }
  });

  // Validação e cadastro do novo contato (sem alert)
  form.addEventListener('submit', function (evento) {
    evento.preventDefault();
    limparErrosFormulario();

    let valido = true;
    const nome = inputNome.value.trim();
    const sobrenome = inputSobrenome.value.trim();
    const telefone = inputTelefone.value.trim();
    const email = inputEmail.value.trim();

    if (!nome) {
      erroNome.textContent = 'Informe o nome do contato.';
      inputNome.classList.add('campo-invalido');
      valido = false;
    }

    const regexTelefone = /^\(\d{2}\)\s?\d{4,5}-\d{4}$/;
    if (!telefone) {
      erroTelefone.textContent = 'Informe o telefone no formato (49) 99999-0001.';
      inputTelefone.classList.add('campo-invalido');
      valido = false;
    } else if (!regexTelefone.test(telefone)) {
      erroTelefone.textContent = 'Telefone inválido. Use o formato (49) 99999-0001.';
      inputTelefone.classList.add('campo-invalido');
      valido = false;
    } else {
      const telDigitos = normalizarTelefone(telefone);
      const duplicado = todosContatos.some(function (c) {
        return normalizarTelefone(c.telefone) === telDigitos;
      });
      if (duplicado) {
        erroTelefone.textContent = 'Este telefone já está na sua lista de contatos (telefone já cadastrado).';
        inputTelefone.classList.add('campo-invalido');
        valido = false;
      }
    }

    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      erroEmail.textContent = 'Informe um e-mail válido.';
      inputEmail.classList.add('campo-invalido');
      valido = false;
    }

    if (!valido) {
      erroGeral.hidden = false;
      return;
    }

    const nomeCompleto = sobrenome ? `${nome} ${sobrenome}` : nome;
    const novoContato = {
      id: Date.now(),
      nome: nomeCompleto,
      telefone: telefone,
      email: email,
      recado: 'Disponível',
      favorito: false,
      bloqueado: false
    };

    const extras = lerContatosExtras();
    extras.push(novoContato);
    localStorage.setItem(CHAVE_CONTATOS_EXTRAS, JSON.stringify(extras));

    todosContatos.push(novoContato);
    form.reset();
    fecharModal();
    renderizarLista(inputBusca.value);
    mostrarToast(`Contato ${nomeCompleto} salvo com sucesso.`);
  });

  carregarContatos();
})();
