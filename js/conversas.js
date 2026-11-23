const app = document.getElementById("app");
const lista = document.getElementById("lista-conversas");
const busca = document.getElementById("busca-conversas");
const chips = document.querySelectorAll("[data-filtro]");
const boasVindas = document.getElementById("boas-vindas");
const chat = document.getElementById("chat");
const chatNome = document.getElementById("chat-nome");
const chatStatus = document.getElementById("chat-status");
const mensagens = document.getElementById("mensagens");
const btnVoltar = document.getElementById("btn-voltar");
const formMensagem = document.getElementById("form-mensagem");
const campoMensagem = document.getElementById("campo-mensagem");
const btnEnviar = formMensagem.querySelector('button[type="submit"]');
const menu = document.getElementById("menu-contexto");
const opcaoFixar = document.getElementById("opcao-fixar");
const opcaoFavoritar = document.getElementById("opcao-favoritar");
const opcaoApagar = menu.querySelector('[data-acao="apagar"]');
const modal = document.getElementById("modal-confirmar");
const modalCancelar = document.getElementById("modal-cancelar");
const modalConfirmar = document.getElementById("modal-confirmar-btn");
const toast = document.getElementById("toast");

const TEXTO_APAGADA = "Esta mensagem foi apagada";

let conversas = [];
let mensagensAtuais = [];
let idAtual = null;
let filtroAtual = "tudo";
let alvoMenu = null;
let idParaApagar = null;
let timerToast = null;

async function api(url, dados) {
  const opcoes = dados ? { method: "POST", body: new URLSearchParams(dados) } : {};
  const resposta = await fetch(url, opcoes);
  const json = await resposta.json();
  if (resposta.status === 401) {
    window.location.href = "login.php";
  }
  if (!resposta.ok) {
    throw new Error(json.erro || "Algo deu errado. Tente de novo.");
  }
  return json;
}

async function carregarConversas() {
  const json = await api("api/conversas.php");
  conversas = json.conversas;
  renderizarLista();
}

async function carregarMensagens() {
  const json = await api("api/mensagens.php?conversa=" + idAtual);
  mensagensAtuais = json.mensagens;
  renderizarMensagens();
}

function mostrarToast(texto) {
  toast.textContent = texto;
  toast.classList.add("visivel");
  clearTimeout(timerToast);
  timerToast = setTimeout(function () {
    toast.classList.remove("visivel");
  }, 3000);
}

function criar(tag, classe, texto) {
  const elemento = document.createElement(tag);
  if (classe) elemento.className = classe;
  if (texto !== undefined) elemento.textContent = texto;
  return elemento;
}

function buscarConversa(id) {
  return conversas.find(function (c) {
    return c.id === id;
  });
}

function renderizarLista() {
  const termo = busca.value.trim().toLowerCase();

  const filtradas = conversas.filter(function (c) {
    if (!c.nome.toLowerCase().includes(termo)) return false;
    if (filtroAtual === "nao-lidas") return c.naoLidas > 0;
    if (filtroAtual === "favoritas") return c.favorita;
    if (filtroAtual === "grupos") return c.grupo;
    return true;
  });

  lista.innerHTML = "";

  filtradas.forEach(function (c) {
    const ultima = c.ultima;

    const li = criar("li", "item-conversa");
    li.dataset.id = c.id;
    if (c.id === idAtual) li.classList.add("ativo");

    const avatar = criar("span", "avatar");
    avatar.appendChild(criar("i", c.grupo ? "bi bi-people-fill" : "bi bi-person-fill"));

    const info = criar("div", "item-info");

    const linha1 = criar("div", "item-linha");
    const hora = criar("span", "item-hora", ultima ? ultima.hora : "");
    if (c.naoLidas > 0) hora.classList.add("nao-lida");
    linha1.append(criar("span", "item-nome", c.nome), hora);

    const linha2 = criar("div", "item-linha");
    const previa = ultima ? (ultima.apagada ? TEXTO_APAGADA : ultima.texto) : "";
    const icones = criar("span", "item-icones");
    if (c.fixada) icones.appendChild(criar("i", "bi bi-pin-angle-fill"));
    if (c.naoLidas > 0) icones.appendChild(criar("span", "badge", c.naoLidas));
    linha2.append(criar("span", "item-previa", previa), icones);

    info.append(linha1, linha2);
    li.append(avatar, info);
    lista.appendChild(li);
  });
}

function renderizarMensagens() {
  mensagens.innerHTML = "";

  mensagensAtuais.forEach(function (m) {
    const div = criar("div", "mensagem " + (m.de === "eu" ? "enviada" : "recebida"));
    div.dataset.id = m.id;

    const texto = criar("p", "mensagem-texto", m.apagada ? TEXTO_APAGADA : m.texto);
    if (m.apagada) texto.style.fontStyle = "italic";

    const info = criar("span", "mensagem-info", m.hora);
    if (m.de === "eu") info.appendChild(criar("i", "bi bi-check2-all lida"));

    div.append(texto, info);
    mensagens.appendChild(div);
  });

  mensagens.scrollTop = mensagens.scrollHeight;
}

async function abrirConversa(id) {
  const conversa = buscarConversa(id);
  idAtual = id;

  boasVindas.hidden = true;
  chat.hidden = false;
  chatNome.textContent = conversa.nome;
    // aponta o link do cabeçalho para o perfil do contato aberto
  document.querySelector(".chat-contato").href = "perfil.php?id=" + conversa.contatoId;

  if (conversa.bloqueado) {
    chatStatus.textContent = "Você bloqueou este contato";
  } else {
    chatStatus.textContent = conversa.grupo ? "Grupo" : conversa.telefone;
  }
  campoMensagem.disabled = conversa.bloqueado;
  btnEnviar.disabled = conversa.bloqueado;
  campoMensagem.value = "";

  app.classList.add("chat-aberto");
  mensagensAtuais = [];
  renderizarLista();

  try {
    await carregarMensagens();
    if (conversa.naoLidas > 0) {
      await api("api/conversas.php", { acao: "ler", id: id });
      conversa.naoLidas = 0;
      renderizarLista();
    }
  } catch (erro) {
    mostrarToast(erro.message);
  }
}

function abrirMenu(evento, alvo) {
  evento.preventDefault();
  alvoMenu = alvo;

  if (alvo.tipo === "conversa") {
    const conversa = buscarConversa(alvo.id);
    opcaoFixar.textContent = conversa.fixada ? "Desafixar conversa" : "Fixar conversa";
    opcaoFavoritar.textContent = conversa.favorita ? "Desfavoritar" : "Favoritar";
    opcaoFixar.style.display = "";
    opcaoFavoritar.style.display = "";
    opcaoApagar.style.display = "none";
  } else {
    opcaoFixar.style.display = "none";
    opcaoFavoritar.style.display = "none";
    opcaoApagar.style.display = "";
  }

  menu.hidden = false;
  const x = Math.min(evento.clientX, window.innerWidth - menu.offsetWidth);
  const y = Math.min(evento.clientY, window.innerHeight - menu.offsetHeight);
  menu.style.left = x + "px";
  menu.style.top = y + "px";
}

function fecharMenu() {
  menu.hidden = true;
}

async function alterarConversa(acao, id) {
  try {
    await api("api/conversas.php", { acao: acao, id: id });
    await carregarConversas();
  } catch (erro) {
    mostrarToast(erro.message);
  }
}

function pedirApagar(idMensagem) {
  idParaApagar = idMensagem;
  modal.hidden = false;
}

function fecharModal() {
  modal.hidden = true;
  idParaApagar = null;
}

lista.addEventListener("click", function (evento) {
  const item = evento.target.closest(".item-conversa");
  if (item) abrirConversa(Number(item.dataset.id));
});

lista.addEventListener("contextmenu", function (evento) {
  const item = evento.target.closest(".item-conversa");
  if (item) abrirMenu(evento, { tipo: "conversa", id: Number(item.dataset.id) });
});

mensagens.addEventListener("contextmenu", function (evento) {
  const elemento = evento.target.closest(".mensagem.enviada");
  if (!elemento) return;
  const mensagem = mensagensAtuais.find(function (m) {
    return m.id === Number(elemento.dataset.id);
  });
  if (!mensagem || mensagem.apagada) return;
  abrirMenu(evento, { tipo: "mensagem", id: mensagem.id });
});

busca.addEventListener("input", renderizarLista);

chips.forEach(function (chip) {
  chip.addEventListener("click", function () {
    filtroAtual = chip.dataset.filtro;
    chips.forEach(function (c) {
      c.classList.toggle("ativo", c === chip);
      c.setAttribute("aria-pressed", c === chip ? "true" : "false");
    });
    renderizarLista();
  });
});

btnVoltar.addEventListener("click", function () {
  app.classList.remove("chat-aberto");
});

formMensagem.addEventListener("submit", async function (evento) {
  evento.preventDefault();
  const texto = campoMensagem.value.trim();
  if (!texto || !idAtual) return;

  try {
    await api("api/mensagens.php", { acao: "enviar", conversa: idAtual, texto: texto });
    campoMensagem.value = "";
    await carregarMensagens();
    await carregarConversas();
  } catch (erro) {
    mostrarToast(erro.message);
  }
});

menu.addEventListener("click", function (evento) {
  const botao = evento.target.closest("[data-acao]");
  if (!botao || !alvoMenu) return;
  fecharMenu();

  if (botao.dataset.acao === "fixar") alterarConversa("fixar", alvoMenu.id);
  if (botao.dataset.acao === "favoritar") alterarConversa("favoritar", alvoMenu.id);
  if (botao.dataset.acao === "apagar") pedirApagar(alvoMenu.id);
});

document.addEventListener("click", function (evento) {
  if (!menu.contains(evento.target)) fecharMenu();
});

document.addEventListener("keydown", function (evento) {
  if (evento.key === "Escape") fecharMenu();
});

modalCancelar.addEventListener("click", fecharModal);

modalConfirmar.addEventListener("click", async function () {
  const id = idParaApagar;
  fecharModal();
  try {
    await api("api/mensagens.php", { acao: "apagar", id: id });
    await carregarMensagens();
    await carregarConversas();
  } catch (erro) {
    mostrarToast(erro.message);
  }
});

carregarConversas().catch(function (erro) {
  mostrarToast(erro.message);
});
