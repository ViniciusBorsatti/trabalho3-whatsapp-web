const btnMenu = document.getElementById("btn-menu");
const menuLateral = document.getElementById("menu-lateral");
const corpo = document.getElementById("tabela-corpo");
const btnNovo = document.getElementById("btn-novo");
const modalContato = document.getElementById("modal-contato");
const formContato = document.getElementById("form-contato");
const tituloModal = document.getElementById("titulo-modal");
const campoAcao = document.getElementById("campo-acao");
const campoId = document.getElementById("campo-id");
const campoNome = document.getElementById("campo-nome");
const campoTelefone = document.getElementById("campo-telefone");
const campoRecado = document.getElementById("campo-recado");
const campoBloqueado = document.getElementById("campo-bloqueado");
const erroNome = document.getElementById("erro-nome");
const erroTelefone = document.getElementById("erro-telefone");
const erroRecado = document.getElementById("erro-recado");
const contatoCancelar = document.getElementById("contato-cancelar");
const modalExcluir = document.getElementById("modal-excluir");
const textoExcluir = document.getElementById("texto-excluir");
const excluirId = document.getElementById("excluir-id");
const excluirCancelar = document.getElementById("excluir-cancelar");
const toast = document.getElementById("toast");

const FORMATO_TELEFONE = /^\(\d{2}\) \d{5}-\d{4}$/;

// Esconde o aviso vindo do PHP depois de alguns segundos
if (toast.classList.contains("visivel")) {
  setTimeout(function () {
    toast.classList.remove("visivel");
  }, 3000);
}

function mostrarErro(campo, erro, texto) {
  campo.classList.add("invalido");
  erro.textContent = texto;
}

function limparErros() {
  [[campoNome, erroNome], [campoTelefone, erroTelefone], [campoRecado, erroRecado]].forEach(function (par) {
    par[0].classList.remove("invalido");
    par[1].textContent = "";
  });
}

function abrirContato(dados) {
  limparErros();
  campoAcao.value = dados ? "editar" : "criar";
  campoId.value = dados ? dados.id : 0;
  campoNome.value = dados ? dados.nome : "";
  campoTelefone.value = dados ? dados.telefone : "";
  campoRecado.value = dados ? dados.recado : "";
  campoBloqueado.checked = dados ? dados.bloqueado === "1" : false;
  tituloModal.textContent = dados ? "Editar contato" : "Novo contato";
  modalContato.hidden = false;
  campoNome.focus();
}

function abrirExcluir(dados) {
  excluirId.value = dados.id;
  textoExcluir.textContent = "O contato " + dados.nome + " e as conversas dele serão removidos.";
  modalExcluir.hidden = false;
}

function fecharModais() {
  modalContato.hidden = true;
  modalExcluir.hidden = true;
}

btnMenu.addEventListener("click", function () {
  const aberto = menuLateral.classList.toggle("aberto");
  btnMenu.setAttribute("aria-expanded", aberto ? "true" : "false");
  btnMenu.setAttribute("aria-label", aberto ? "Fechar menu" : "Abrir menu");
});

btnNovo.addEventListener("click", function () {
  abrirContato(null);
});

// Os dados de cada linha vêm do PHP nos atributos data-*
corpo.addEventListener("click", function (evento) {
  const botao = evento.target.closest("[data-acao]");
  if (!botao) return;
  if (botao.dataset.acao === "editar") abrirContato(botao.dataset);
  if (botao.dataset.acao === "excluir") abrirExcluir(botao.dataset);
});

// Validação no navegador; o PHP valida de novo ao receber
formContato.addEventListener("submit", function (evento) {
  limparErros();
  let valido = true;

  if (!campoNome.value.trim()) {
    mostrarErro(campoNome, erroNome, "Informe o nome.");
    valido = false;
  }
  if (!FORMATO_TELEFONE.test(campoTelefone.value.trim())) {
    mostrarErro(campoTelefone, erroTelefone, "Use o formato (49) 99999-0001.");
    valido = false;
  }
  if (campoRecado.value.trim().length > 150) {
    mostrarErro(campoRecado, erroRecado, "O recado deve ter até 150 caracteres.");
    valido = false;
  }

  if (!valido) evento.preventDefault();
});

contatoCancelar.addEventListener("click", fecharModais);
excluirCancelar.addEventListener("click", fecharModais);

document.addEventListener("keydown", function (evento) {
  if (evento.key === "Escape") fecharModais();
});
