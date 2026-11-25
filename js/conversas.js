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
const menuLista = document.getElementById("menu-lista");
const menuChat = document.getElementById("menu-chat");
const btnOpcoesLista = document.getElementById("btn-opcoes-lista");
const btnOpcoesChat = document.getElementById("btn-opcoes-chat");
const opcaoDados = document.getElementById("opcao-dados");
const opcaoChatFixar = document.getElementById("opcao-chat-fixar");
const opcaoChatFavoritar = document.getElementById("opcao-chat-favoritar");
const btnBuscarMensagens = document.getElementById("btn-buscar-mensagens");
const buscaMensagens = document.getElementById("busca-mensagens");
const btnEmoji = document.getElementById("btn-emoji");
const painelEmoji = document.getElementById("painel-emoji");
const btnAnexar = document.getElementById("btn-anexar");
const campoArquivo = document.getElementById("campo-arquivo");
const TAMANHO_MAXIMO = 5 * 1024 * 1024;
const btnConversaUsuario = document.getElementById("btn-conversa-usuario");
const modalUsuario = document.getElementById("modal-usuario");
const formUsuario = document.getElementById("form-usuario");
const campoTelefoneUsuario = document.getElementById("campo-telefone-usuario");
const erroTelefoneUsuario = document.getElementById("erro-telefone-usuario");
const usuarioCancelar = document.getElementById("usuario-cancelar");
const INTERVALO_ATUALIZAR = 3000;

const TEXTO_APAGADA = "Esta mensagem foi apagada";

let conversas = [];
let mensagensAtuais = [];
let idAtual = null;
let filtroAtual = "tudo";
let alvoMenu = null;
let idParaApagar = null;
let timerToast = null;
let assinaturaLista = "";
let assinaturaMensagens = "";

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

function lerId(valor) {
  return /^u\d+$/.test(String(valor)) ? String(valor) : Number(valor);
}

async function carregarConversas() {
  const json = await api("api/conversas.php");
  conversas = json.conversas;
  assinaturaLista = JSON.stringify(conversas);
  renderizarLista();
}

async function carregarMensagens() {
  const json = await api("api/mensagens.php?conversa=" + encodeURIComponent(idAtual));
  mensagensAtuais = json.mensagens;
  assinaturaMensagens = JSON.stringify(mensagensAtuais);
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

  if (conversas.length === 0) {
    lista.appendChild(criar("li", "lista-vazia", "Nenhuma conversa ainda. Toque no ícone de pessoa para falar com outro usuário do site."));
    return;
  }

  filtradas.forEach(function (c) {
    const ultima = c.ultima;

    const li = criar("li", "item-conversa");
    li.dataset.id = c.id;
    if (c.id === idAtual) li.classList.add("ativo");

    const avatar = criar("span", "avatar");
    const foto = criar("img");
    foto.src = c.foto;
    foto.alt = "Foto de " + c.nome;
    foto.loading = "lazy";
    avatar.appendChild(foto);

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

    if (m.arquivo && m.tipo === "imagem") {
      const link = criar("a", "mensagem-foto");
      link.href = m.arquivo;
      link.target = "_blank";
      link.rel = "noopener";
      const img = criar("img");
      img.src = m.arquivo;
      img.alt = "Foto enviada: " + m.texto;
      img.loading = "lazy";
      link.appendChild(img);
      div.appendChild(link);
      texto.classList.add("visualmente-oculto");
    }

    if (m.arquivo && m.tipo === "documento") {
      const link = criar("a", "mensagem-documento");
      link.href = m.arquivo;
      link.target = "_blank";
      link.rel = "noopener";
      link.append(criar("i", "bi bi-file-earmark-pdf-fill"), criar("span", "", m.texto));
      div.appendChild(link);
      texto.classList.add("visualmente-oculto");
    }

    const info = criar("span", "mensagem-info", m.hora);
    if (m.de === "eu") info.appendChild(criar("i", "bi bi-check2-all lida"));

    div.append(texto, info);
    mensagens.appendChild(div);
  });

  filtrarMensagens();

  mensagens.scrollTop = mensagens.scrollHeight;
}

async function abrirConversa(id) {
  const conversa = buscarConversa(id);
  idAtual = id;

  boasVindas.hidden = true;
  chat.hidden = false;
  chatNome.textContent = conversa.nome;
  const fotoChat = criar("img");
  fotoChat.src = conversa.foto;
  fotoChat.alt = "Foto de " + conversa.nome;
  document.getElementById("chat-avatar").replaceChildren(fotoChat);
  const linkContato = document.querySelector(".chat-contato");
  if (conversa.usuario) {
    linkContato.removeAttribute("href");
    opcaoDados.hidden = true;
  } else {
    linkContato.href = "perfil.php?id=" + conversa.contatoId;
    opcaoDados.href = "perfil.php?id=" + conversa.contatoId;
    opcaoDados.hidden = false;
  }
  fecharBuscaMensagens();
  painelEmoji.hidden = true;

  if (conversa.bloqueado) {
    chatStatus.textContent = "Você bloqueou este contato";
  } else {
    chatStatus.textContent = conversa.grupo ? "Grupo" : conversa.usuario ? conversa.telefone + " · usuário do site" : conversa.telefone;
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
    if (conversa.usuario) {
      alvoMenu = null;
      return;
    }
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
  if (item) abrirConversa(lerId(item.dataset.id));
});

lista.addEventListener("contextmenu", function (evento) {
  const item = evento.target.closest(".item-conversa");
  if (item) abrirMenu(evento, { tipo: "conversa", id: lerId(item.dataset.id) });
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

function filtrarMensagens() {
  const termo = buscaMensagens.hidden ? "" : buscaMensagens.value.trim().toLowerCase();

  mensagens.querySelectorAll(".mensagem").forEach(function (div) {
    const p = div.querySelector(".mensagem-texto");
    if (p.dataset.original === undefined) p.dataset.original = p.textContent;
    const original = p.dataset.original;
    const posicao = original.toLowerCase().indexOf(termo);

    p.textContent = original;
    div.classList.toggle("oculta", termo !== "" && posicao === -1);

    if (termo && posicao !== -1) {
      const marca = document.createElement("mark");
      marca.textContent = original.slice(posicao, posicao + termo.length);
      p.textContent = "";
      p.append(original.slice(0, posicao), marca, original.slice(posicao + termo.length));
    }
  });
}

function fecharBuscaMensagens() {
  buscaMensagens.value = "";
  buscaMensagens.hidden = true;
  btnBuscarMensagens.setAttribute("aria-expanded", "false");
  filtrarMensagens();
}

function abrirMenuNoBotao(menuAlvo, botao) {
  fecharMenus();
  const caixa = botao.getBoundingClientRect();
  menuAlvo.hidden = false;
  menuAlvo.style.top = caixa.bottom + 4 + "px";
  menuAlvo.style.left = Math.max(8, Math.min(caixa.right - menuAlvo.offsetWidth, window.innerWidth - menuAlvo.offsetWidth - 8)) + "px";
  botao.setAttribute("aria-expanded", "true");
}

function fecharMenus() {
  fecharMenu();
  menuLista.hidden = true;
  menuChat.hidden = true;
  btnOpcoesLista.setAttribute("aria-expanded", "false");
  btnOpcoesChat.setAttribute("aria-expanded", "false");
}

function fecharChat() {
  idAtual = null;
  chat.hidden = true;
  boasVindas.hidden = false;
  app.classList.remove("chat-aberto");
  renderizarLista();
}

btnOpcoesLista.addEventListener("click", function (evento) {
  evento.stopPropagation();
  if (menuLista.hidden) abrirMenuNoBotao(menuLista, btnOpcoesLista);
  else fecharMenus();
});

btnOpcoesChat.addEventListener("click", function (evento) {
  evento.stopPropagation();
  if (!menuChat.hidden) {
    fecharMenus();
    return;
  }
  const conversa = buscarConversa(idAtual);
  opcaoChatFixar.textContent = conversa.fixada ? "Desafixar conversa" : "Fixar conversa";
  opcaoChatFavoritar.textContent = conversa.favorita ? "Desfavoritar" : "Favoritar";
  opcaoChatFixar.hidden = conversa.usuario;
  opcaoChatFavoritar.hidden = conversa.usuario;
  abrirMenuNoBotao(menuChat, btnOpcoesChat);
});

menuChat.addEventListener("click", function (evento) {
  const botao = evento.target.closest("[data-chat]");
  if (!botao) return;
  fecharMenus();
  if (botao.dataset.chat === "fixar") alterarConversa("fixar", idAtual);
  if (botao.dataset.chat === "favoritar") alterarConversa("favoritar", idAtual);
  if (botao.dataset.chat === "fechar") fecharChat();
});

btnBuscarMensagens.addEventListener("click", function () {
  if (buscaMensagens.hidden) {
    buscaMensagens.hidden = false;
    btnBuscarMensagens.setAttribute("aria-expanded", "true");
    buscaMensagens.focus();
  } else {
    fecharBuscaMensagens();
  }
});

buscaMensagens.addEventListener("input", filtrarMensagens);

btnEmoji.addEventListener("click", function (evento) {
  evento.stopPropagation();
  if (campoMensagem.disabled) return;
  painelEmoji.hidden = !painelEmoji.hidden;
  btnEmoji.setAttribute("aria-expanded", painelEmoji.hidden ? "false" : "true");
});

painelEmoji.addEventListener("click", function (evento) {
  const botao = evento.target.closest("[data-emoji]");
  if (!botao) return;
  evento.stopPropagation();
  const inicio = campoMensagem.selectionStart ?? campoMensagem.value.length;
  const fim = campoMensagem.selectionEnd ?? campoMensagem.value.length;
  campoMensagem.value = campoMensagem.value.slice(0, inicio) + botao.dataset.emoji + campoMensagem.value.slice(fim);
  campoMensagem.focus();
  campoMensagem.setSelectionRange(inicio + botao.dataset.emoji.length, inicio + botao.dataset.emoji.length);
});

btnAnexar.addEventListener("click", function () {
  if (!idAtual || campoMensagem.disabled) return;
  campoArquivo.click();
});

campoArquivo.addEventListener("change", async function () {
  const arquivo = campoArquivo.files[0];
  campoArquivo.value = "";
  if (!arquivo) return;

  if (arquivo.size > TAMANHO_MAXIMO) {
    mostrarToast("O arquivo pode ter no máximo 5 MB.");
    return;
  }

  const dados = new FormData();
  dados.append("acao", "arquivo");
  dados.append("conversa", idAtual);
  dados.append("arquivo", arquivo);

  try {
    mostrarToast("Enviando arquivo...");
    const resposta = await fetch("api/mensagens.php", { method: "POST", body: dados });
    const json = await resposta.json();
    if (!resposta.ok) throw new Error(json.erro || "Não foi possível enviar o arquivo.");
    await carregarMensagens();
    await carregarConversas();
    mostrarToast("Arquivo enviado.");
  } catch (erro) {
    mostrarToast(erro.message);
  }
});

document.querySelectorAll("[data-aviso]").forEach(function (botao) {
  botao.addEventListener("click", function () {
    mostrarToast(botao.dataset.aviso + " não está disponível nesta versão do clone.");
  });
});

document.addEventListener("click", function (evento) {
  if (!menu.contains(evento.target) && !menuLista.contains(evento.target) && !menuChat.contains(evento.target)) fecharMenus();
  if (!painelEmoji.contains(evento.target) && evento.target !== btnEmoji) {
    painelEmoji.hidden = true;
    btnEmoji.setAttribute("aria-expanded", "false");
  }
});

document.addEventListener("keydown", function (evento) {
  if (evento.key === "Escape") {
    fecharMenus();
    fecharModalUsuario();
    painelEmoji.hidden = true;
  }
});

modalCancelar.addEventListener("click", fecharModal);

modalConfirmar.addEventListener("click", async function () {
  const id = idParaApagar;
  fecharModal();
  try {
    await api("api/mensagens.php", { acao: "apagar", id: id, conversa: idAtual });
    await carregarMensagens();
    await carregarConversas();
  } catch (erro) {
    mostrarToast(erro.message);
  }
});

function formatarTelefone(valor) {
  const d = valor.replace(/\D/g, "").slice(0, 11);
  if (d.length <= 2) return d.length ? "(" + d : "";
  if (d.length <= 7) return "(" + d.slice(0, 2) + ") " + d.slice(2);
  return "(" + d.slice(0, 2) + ") " + d.slice(2, 7) + "-" + d.slice(7);
}

function abrirModalUsuario() {
  fecharMenus();
  campoTelefoneUsuario.value = "";
  campoTelefoneUsuario.classList.remove("invalido");
  erroTelefoneUsuario.textContent = "";
  modalUsuario.hidden = false;
  campoTelefoneUsuario.focus();
}

function fecharModalUsuario() {
  modalUsuario.hidden = true;
}

btnConversaUsuario.addEventListener("click", abrirModalUsuario);

menuLista.addEventListener("click", function (evento) {
  if (evento.target.closest('[data-lista="usuario"]')) abrirModalUsuario();
});

usuarioCancelar.addEventListener("click", fecharModalUsuario);

campoTelefoneUsuario.addEventListener("input", function () {
  campoTelefoneUsuario.value = formatarTelefone(campoTelefoneUsuario.value);
});

formUsuario.addEventListener("submit", async function (evento) {
  evento.preventDefault();
  const telefone = campoTelefoneUsuario.value.trim();
  if (!/^\(\d{2}\) \d{5}-\d{4}$/.test(telefone)) {
    campoTelefoneUsuario.classList.add("invalido");
    erroTelefoneUsuario.textContent = "Use o formato (49) 99999-0001.";
    return;
  }
  try {
    const json = await api("api/conversas.php", { acao: "iniciar", telefone: telefone });
    fecharModalUsuario();
    await carregarConversas();
    abrirConversa(json.id);
  } catch (erro) {
    campoTelefoneUsuario.classList.add("invalido");
    erroTelefoneUsuario.textContent = erro.message;
  }
});

async function atualizar() {
  if (document.hidden) return;
  try {
    const json = await api("api/conversas.php");
    if (JSON.stringify(json.conversas) !== assinaturaLista) {
      conversas = json.conversas;
      assinaturaLista = JSON.stringify(conversas);
      if (menu.hidden) renderizarLista();
    }
    if (idAtual === null) return;
    const atual = buscarConversa(idAtual);
    const resposta = await api("api/mensagens.php?conversa=" + encodeURIComponent(idAtual));
    if (JSON.stringify(resposta.mensagens) !== assinaturaMensagens) {
      mensagensAtuais = resposta.mensagens;
      assinaturaMensagens = JSON.stringify(mensagensAtuais);
      renderizarMensagens();
    }
    if (atual && atual.naoLidas > 0) {
      await api("api/conversas.php", { acao: "ler", id: idAtual });
      atual.naoLidas = 0;
      renderizarLista();
    }
  } catch (erro) {
    return;
  }
}

setInterval(atualizar, INTERVALO_ATUALIZAR);

carregarConversas()
  .then(function () {
    const abrir = lerId(app.dataset.abrir);
    if (abrir && buscarConversa(abrir)) abrirConversa(abrir);
  })
  .catch(function (erro) {
    mostrarToast(erro.message);
  });
