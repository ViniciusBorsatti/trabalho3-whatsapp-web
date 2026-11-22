const contagem = document.getElementById('contagem');
const erroConexao = document.getElementById('erro-conexao');
const btnConectar = document.getElementById('btn-conectar');

// Só mostra o tempo que falta; quem decide se o QR ainda vale é o PHP
if (contagem) {
  let restante = Number(contagem.dataset.restante);

  const intervalo = setInterval(function () {
    restante -= 1;
    if (restante <= 0) {
      clearInterval(intervalo);
      contagem.textContent = 'QR code expirado';
      contagem.classList.add('expirado');
      erroConexao.textContent = 'QR code expirado. Gere um novo no computador.';
      btnConectar.disabled = true;
      return;
    }
    contagem.textContent = 'O QR code expira em ' + restante + ' s';
  }, 1000);
}
