if (localStorage.getItem("tema") === "escuro") {
  document.documentElement.setAttribute("data-tema", "escuro");
}

function atualizarBotoesTema() {
  const escuro = document.documentElement.getAttribute("data-tema") === "escuro";
  document.querySelectorAll("[data-alternar-tema]").forEach(function (botao) {
    botao.setAttribute("aria-checked", escuro ? "true" : "false");
  });
}

function alternarTema() {
  if (document.documentElement.getAttribute("data-tema") === "escuro") {
    document.documentElement.removeAttribute("data-tema");
    localStorage.setItem("tema", "claro");
  } else {
    document.documentElement.setAttribute("data-tema", "escuro");
    localStorage.setItem("tema", "escuro");
  }
  atualizarBotoesTema();
}

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll("[data-alternar-tema]").forEach(function (botao) {
    botao.addEventListener("click", alternarTema);
  });
  atualizarBotoesTema();
});
