const btnMenu = document.getElementById("btn-menu");
const menuLateral = document.getElementById("menu-lateral");
const totalContatos = document.getElementById("total-contatos");
const totalMensagens = document.getElementById("total-mensagens");
const totalBloqueados = document.getElementById("total-bloqueados");
const busca = document.getElementById("busca-contatos");
const corpo = document.getElementById("tabela-corpo");
const modalEditar = document.getElementById("modal-editar");
const formEditar = document.getElementById("form-editar");
const campoNome = document.getElementById("editar-nome");
const campoTelefone = document.getElementById("editar-telefone");
const campoBloqueado = document.getElementById("editar-bloqueado");
const erroNome = document.getElementById("erro-nome");
const erroTelefone = document.getElementById("erro-telefone");
const editarCancelar = document.getElementById("editar-cancelar");
const modalExcluir = document.getElementById("modal-excluir");
const textoExcluir = document.getElementById("texto-excluir");
const excluirCancelar = document.getElementById("excluir-cancelar");
const excluirConfirmar = document.getElementById("excluir-confirmar");
const toast = document.getElementById("toast");

const FORMATO_TELEFONE = /^\(\d{2}\) \d{5}-\d{4}$/;

let conversas = [];
let idEditando = null;
let idExcluindo = null;
let timerToast = null;

function salvar() {
  localStorage.setItem("conversas", JSON.stringify(conversas));
}

function mostrarToast(texto) {
  toast.textContent = texto;
  toast.classList.add("visivel");
  clearTimeout(timerToast);
  timerToast = setTimeout(function () {
    toast.classList.remove("visivel");
  }, 3000);
}

function buscarConversa(id) {
  return conversas.find(function (c) {
    return c.id === id;
  });
}

function celula(texto) {
  const td = document.createElement("td");
  td.textContent = texto;
  return td;
}

function botao(texto, classe, acao, id) {
  const b = document.createElement("button");
  b.type = "button";
  b.className = "btn " + classe;
  b.textContent = texto;
  b.dataset.acao = acao;
  b.dataset.id = id;
  return b;
}

function renderizarResumo() {
  totalContatos.textContent = conversas.length;
  totalMensagens.textContent = conversas.reduce(function (soma, c) {
    return soma + c.mensagens.length;
  }, 0);
  totalBloqueados.textContent = conversas.filter(function (c) {
    return c.bloqueado;
  }).length;
}

function renderizarTabela() {
  const termo = busca.value.trim().toLowerCase();
  const filtrados = conversas.filter(function (c) {
    return c.nome.toLowerCase().includes(termo) || c.telefone.includes(termo);
  });

  corpo.innerHTML = "";

  if (filtrados.length === 0) {
    const tr = document.createElement("tr");
    const td = celula("Nenhum contato encontrado");
    td.colSpan = 5;
    tr.appendChild(td);
    corpo.appendChild(tr);
    return;
  }

  filtrados.forEach(function (c) {
    const tr = document.createElement("tr");

    const situacao = document.createElement("td");
    const span = document.createElement("span");
    span.className = "situacao" + (c.bloqueado ? " bloqueado" : "");
    span.textContent = c.bloqueado ? "Bloqueado" : "Ativo";
    situacao.appendChild(span);

    const acoes = document.createElement("td");
    const div = document.createElement("div");
    div.className = "acoes";
    div.append(botao("Editar", "btn-secundario", "editar", c.id), botao("Excluir", "btn-perigo", "excluir", c.id));
    acoes.appendChild(div);

    tr.append(celula(c.nome), celula(c.telefone), celula(c.mensagens.length), situacao, acoes);
    corpo.appendChild(tr);
  });
}

function renderizar() {
  renderizarResumo();
  renderizarTabela();
}

function mostrarErro(campo, erro, texto) {
  campo.classList.add("invalido");
  erro.textContent = texto;
}

function limparErro(campo, erro) {
  campo.classList.remove("invalido");
  erro.textContent = "";
}

function abrirEditar(id) {
  const conversa = buscarConversa(id);
  idEditando = id;
  campoNome.value = conversa.nome;
  campoTelefone.value = conversa.telefone;
  campoBloqueado.checked = conversa.bloqueado;
  limparErro(campoNome, erroNome);
  limparErro(campoTelefone, erroTelefone);
  modalEditar.hidden = false;
  campoNome.focus();
}

function fecharEditar() {
  modalEditar.hidden = true;
  idEditando = null;
}

function abrirExcluir(id) {
  const conversa = buscarConversa(id);
  idExcluindo = id;
  textoExcluir.textContent = "O contato " + conversa.nome + " e suas mensagens serão removidos.";
  modalExcluir.hidden = false;
}

function fecharExcluir() {
  modalExcluir.hidden = true;
  idExcluindo = null;
}

btnMenu.addEventListener("click", function () {
  const aberto = menuLateral.classList.toggle("aberto");
  btnMenu.setAttribute("aria-expanded", aberto ? "true" : "false");
  btnMenu.setAttribute("aria-label", aberto ? "Fechar menu" : "Abrir menu");
});

busca.addEventListener("input", renderizarTabela);

corpo.addEventListener("click", function (evento) {
  const b = evento.target.closest("[data-acao]");
  if (!b) return;
  const id = Number(b.dataset.id);
  if (b.dataset.acao === "editar") abrirEditar(id);
  if (b.dataset.acao === "excluir") abrirExcluir(id);
});

formEditar.addEventListener("submit", function (evento) {
  evento.preventDefault();
  const nome = campoNome.value.trim();
  const telefone = campoTelefone.value.trim();
  let valido = true;

  limparErro(campoNome, erroNome);
  limparErro(campoTelefone, erroTelefone);

  if (!nome) {
    mostrarErro(campoNome, erroNome, "Informe o nome");
    valido = false;
  }

  if (!FORMATO_TELEFONE.test(telefone)) {
    mostrarErro(campoTelefone, erroTelefone, "Use o formato (49) 99999-0001");
    valido = false;
  }

  if (!valido) return;

  const conversa = buscarConversa(idEditando);
  conversa.nome = nome;
  conversa.telefone = telefone;
  conversa.bloqueado = campoBloqueado.checked;
  salvar();
  renderizar();
  fecharEditar();
  mostrarToast("Contato atualizado");
});

editarCancelar.addEventListener("click", fecharEditar);

excluirCancelar.addEventListener("click", fecharExcluir);

excluirConfirmar.addEventListener("click", function () {
  conversas = conversas.filter(function (c) {
    return c.id !== idExcluindo;
  });
  salvar();
  renderizar();
  fecharExcluir();
  mostrarToast("Contato excluído");
});

async function carregar() {
  const salvas = localStorage.getItem("conversas");
  if (salvas) {
    conversas = JSON.parse(salvas);
  } else {
    try {
      const resposta = await fetch("../dados/conversas.json");
      conversas = await resposta.json();
      salvar();
    } catch (erro) {
      mostrarToast("Não foi possível carregar os contatos");
    }
  }
  renderizar();
}

carregar();
