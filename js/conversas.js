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

const LIMITE_FIXADAS = 3;
const LIMITE_APAGAR = 15 * 60 * 1000;
const TEXTO_APAGADA = "Esta mensagem foi apagada";

let conversas = [];
let idAtual = null;
let filtroAtual = "tudo";
let alvoMenu = null;
let idParaApagar = null;
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

function formatarHora(iso) {
  const data = new Date(iso);
  return String(data.getHours()).padStart(2, "0") + ":" + String(data.getMinutes()).padStart(2, "0");
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

function ultimaMensagem(conversa) {
  return conversa.mensagens[conversa.mensagens.length - 1];
}

function tempoUltima(conversa) {
  const ultima = ultimaMensagem(conversa);
  return ultima ? new Date(ultima.hora).getTime() : 0;
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

  filtradas.sort(function (a, b) {
    if (a.fixada !== b.fixada) return a.fixada ? -1 : 1;
    return tempoUltima(b) - tempoUltima(a);
  });

  lista.innerHTML = "";

  filtradas.forEach(function (c) {
    const ultima = ultimaMensagem(c);

    const li = criar("li", "item-conversa");
    li.dataset.id = c.id;
    if (c.id === idAtual) li.classList.add("ativo");

    const avatar = criar("span", "avatar");
    avatar.appendChild(criar("i", c.grupo ? "bi bi-people-fill" : "bi bi-person-fill"));

    const info = criar("div", "item-info");

    const linha1 = criar("div", "item-linha");
    const hora = criar("span", "item-hora", ultima ? formatarHora(ultima.hora) : "");
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
  const conversa = buscarConversa(idAtual);
  mensagens.innerHTML = "";

  conversa.mensagens.forEach(function (m) {
    const div = criar("div", "mensagem " + (m.de === "eu" ? "enviada" : "recebida"));
    div.dataset.id = m.id;

    const texto = criar("p", "mensagem-texto", m.apagada ? TEXTO_APAGADA : m.texto);
    if (m.apagada) texto.style.fontStyle = "italic";

    const info = criar("span", "mensagem-info", formatarHora(m.hora));
    if (m.de === "eu") info.appendChild(criar("i", "bi bi-check2-all lida"));

    div.append(texto, info);
    mensagens.appendChild(div);
  });

  mensagens.scrollTop = mensagens.scrollHeight;
}

function abrirConversa(id) {
  const conversa = buscarConversa(id);
  idAtual = id;
  conversa.naoLidas = 0;
  salvar();

  boasVindas.hidden = true;
  chat.hidden = false;
  chatNome.textContent = conversa.nome;
    // aponta o link do cabeçalho para o perfil do contato aberto
  document.querySelector(".chat-contato").href = "perfil.php?id=" + conversa.id;

  if (conversa.bloqueado) {
    chatStatus.textContent = "Você bloqueou este contato";
  } else {
    chatStatus.textContent = conversa.grupo ? "Grupo" : conversa.telefone;
  }
  campoMensagem.disabled = conversa.bloqueado;
  btnEnviar.disabled = conversa.bloqueado;
  campoMensagem.value = "";

  renderizarMensagens();
  renderizarLista();
  app.classList.add("chat-aberto");
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

function fixar(conversa) {
  if (!conversa.fixada) {
    const fixadas = conversas.filter(function (c) {
      return c.fixada;
    }).length;
    if (fixadas >= LIMITE_FIXADAS) {
      mostrarToast("Você só pode fixar até 3 conversas");
      return;
    }
  }
  conversa.fixada = !conversa.fixada;
  salvar();
  renderizarLista();
}

function favoritar(conversa) {
  conversa.favorita = !conversa.favorita;
  salvar();
  renderizarLista();
}

function pedirApagar(idMensagem) {
  const conversa = buscarConversa(idAtual);
  const mensagem = conversa.mensagens.find(function (m) {
    return m.id === idMensagem;
  });

  if (Date.now() - new Date(mensagem.hora).getTime() > LIMITE_APAGAR) {
    mostrarToast("Só é possível apagar para todos mensagens com até 15 minutos");
    return;
  }

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
  const conversa = buscarConversa(idAtual);
  const mensagem = conversa.mensagens.find(function (m) {
    return m.id === Number(elemento.dataset.id);
  });
  if (mensagem.apagada) return;
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

formMensagem.addEventListener("submit", function (evento) {
  evento.preventDefault();
  const conversa = buscarConversa(idAtual);
  const texto = campoMensagem.value.trim();
  if (!texto || !conversa || conversa.bloqueado) return;

  conversa.mensagens.push({
    id: Date.now(),
    de: "eu",
    texto: texto,
    hora: new Date().toISOString()
  });

  campoMensagem.value = "";
  salvar();
  renderizarMensagens();
  renderizarLista();
});

menu.addEventListener("click", function (evento) {
  const botao = evento.target.closest("[data-acao]");
  if (!botao || !alvoMenu) return;
  fecharMenu();

  if (botao.dataset.acao === "fixar") fixar(buscarConversa(alvoMenu.id));
  if (botao.dataset.acao === "favoritar") favoritar(buscarConversa(alvoMenu.id));
  if (botao.dataset.acao === "apagar") pedirApagar(alvoMenu.id);
});

document.addEventListener("click", function (evento) {
  if (!menu.contains(evento.target)) fecharMenu();
});

document.addEventListener("keydown", function (evento) {
  if (evento.key === "Escape") fecharMenu();
});

modalCancelar.addEventListener("click", fecharModal);

modalConfirmar.addEventListener("click", function () {
  const conversa = buscarConversa(idAtual);
  const mensagem = conversa.mensagens.find(function (m) {
    return m.id === idParaApagar;
  });
  if (mensagem) mensagem.apagada = true;
  salvar();
  fecharModal();
  renderizarMensagens();
  renderizarLista();
});

async function carregar() {
  const salvas = localStorage.getItem("conversas");
  if (salvas) {
    conversas = JSON.parse(salvas);
  } else {
    try {
      const resposta = await fetch("dados/conversas.json");
      conversas = await resposta.json();
      salvar();
    } catch (erro) {
      mostrarToast("Não foi possível carregar as conversas");
    }
  }
  renderizarLista();
}

carregar();
