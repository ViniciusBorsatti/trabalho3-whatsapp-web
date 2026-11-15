const TEMPO_TOTAL = 60;

const contagem = document.getElementById('contagem');
const erroConexao = document.getElementById('erro-conexao');
const btnNovoQr = document.getElementById('btn-novo-qr');
const btnConectar = document.getElementById('btn-conectar');
const btnCancelar = document.getElementById('btn-cancelar');
const toast = document.getElementById('toast');

let restante = TEMPO_TOTAL;
let intervalo = null;
let temporizadorToast = null;

function atualizarContagem() {
  contagem.textContent = 'Expira em ' + restante + ' s';
}

function expirarQr() {
  clearInterval(intervalo);
  intervalo = null;
  restante = 0;
  contagem.textContent = 'QR expirado';
  contagem.classList.add('expirado');
  erroConexao.textContent = 'QR expirado. Gere um novo código para continuar.';
  btnConectar.disabled = true;
  btnNovoQr.hidden = false;
}

function iniciarContagem() {
  clearInterval(intervalo);
  restante = TEMPO_TOTAL;
  erroConexao.textContent = '';
  contagem.classList.remove('expirado');
  btnConectar.disabled = false;
  btnNovoQr.hidden = true;
  atualizarContagem();

  intervalo = setInterval(() => {
    restante -= 1;
    if (restante <= 0) {
      expirarQr();
    } else {
      atualizarContagem();
    }
  }, 1000);
}

function mostrarToast(texto) {
  toast.textContent = texto;
  toast.classList.add('visivel');
  clearTimeout(temporizadorToast);
  temporizadorToast = setTimeout(() => {
    toast.classList.remove('visivel');
  }, 3000);
}

btnConectar.addEventListener('click', () => {
  if (restante <= 0) {
    erroConexao.textContent = 'QR expirado. Gere um novo código para continuar.';
    return;
  }

  clearInterval(intervalo);
  erroConexao.textContent = '';
  mostrarToast('Computador conectado!');
  setTimeout(() => {
    window.location.href = 'conversas.html';
  }, 1000);
});

btnNovoQr.addEventListener('click', iniciarContagem);

btnCancelar.addEventListener('click', () => {
  clearInterval(intervalo);
  window.location.href = 'index.html';
});

iniciarContagem();