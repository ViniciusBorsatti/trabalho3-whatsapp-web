const form = document.getElementById('form-admin-login');
const email = document.getElementById('email');
const senha = document.getElementById('senha');
const btnMostrarSenha = document.getElementById('btn-mostrar-senha');
const erroGeral = document.getElementById('erro-geral');
const toast = document.getElementById('toast');

const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

let temporizadorToast = null;

function validarEmail() {
  const valor = email.value.trim();
  if (valor === '') {
    return 'Informe o e-mail.';
  }
  if (!REGEX_EMAIL.test(valor)) {
    return 'Digite um e-mail válido.';
  }
  return '';
}

function validarSenha() {
  if (senha.value === '') {
    return 'Informe a senha.';
  }
  if (senha.value.length < 8) {
    return 'A senha precisa ter no mínimo 8 caracteres.';
  }
  return '';
}

const regras = [
  { campo: email, validar: validarEmail },
  { campo: senha, validar: validarSenha }
];

function mostrarErro(campo, mensagem) {
  document.getElementById('erro-' + campo.id).textContent = mensagem;
  campo.classList.add('campo-invalido');
  campo.setAttribute('aria-invalid', 'true');
}

function limparErro(campo) {
  document.getElementById('erro-' + campo.id).textContent = '';
  campo.classList.remove('campo-invalido');
  campo.removeAttribute('aria-invalid');
}

function validarCampo(regra) {
  const mensagem = regra.validar();
  if (mensagem) {
    mostrarErro(regra.campo, mensagem);
    return false;
  }
  limparErro(regra.campo);
  return true;
}

function mostrarToast(texto) {
  toast.textContent = texto;
  toast.classList.add('visivel');
  clearTimeout(temporizadorToast);
  temporizadorToast = setTimeout(() => {
    toast.classList.remove('visivel');
  }, 3000);
}

regras.forEach((regra) => {
  regra.campo.addEventListener('blur', () => {
    validarCampo(regra);
  });

  regra.campo.addEventListener('input', () => {
    if (regra.campo.classList.contains('campo-invalido')) {
      validarCampo(regra);
    }
  });
});

btnMostrarSenha.addEventListener('click', () => {
  const mostrar = senha.type === 'password';
  senha.type = mostrar ? 'text' : 'password';
  btnMostrarSenha.setAttribute('aria-pressed', String(mostrar));
  btnMostrarSenha.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');
  btnMostrarSenha.querySelector('i').className = mostrar ? 'bi bi-eye-slash' : 'bi bi-eye';
});

form.addEventListener('submit', (evento) => {
  evento.preventDefault();

  let primeiroInvalido = null;

  regras.forEach((regra) => {
    const valido = validarCampo(regra);
    if (!valido && primeiroInvalido === null) {
      primeiroInvalido = regra.campo;
    }
  });

  if (primeiroInvalido !== null) {
    erroGeral.textContent = 'Revise os campos destacados.';
    primeiroInvalido.focus();
    return;
  }

  erroGeral.textContent = '';
  mostrarToast('Login de administrador realizado!');
  setTimeout(() => {
    window.location.href = 'painel.html';
  }, 1000);
});