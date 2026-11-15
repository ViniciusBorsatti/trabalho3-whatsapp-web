const form = document.getElementById('form-login');
const telefone = document.getElementById('telefone');
const senha = document.getElementById('senha');
const btnMostrarSenha = document.getElementById('btn-mostrar-senha');
const erroGeral = document.getElementById('erro-geral');
const toast = document.getElementById('toast');

const REGEX_TELEFONE = /^\(\d{2}\) \d{5}-\d{4}$/;

let temporizadorToast = null;

function formatarTelefone(valor) {
  const numeros = valor.replace(/\D/g, '').slice(0, 11);

  if (numeros.length === 0) {
    return '';
  }
  if (numeros.length <= 2) {
    return '(' + numeros;
  }
  if (numeros.length <= 6) {
    return '(' + numeros.slice(0, 2) + ') ' + numeros.slice(2);
  }
  if (numeros.length <= 10) {
    return '(' + numeros.slice(0, 2) + ') ' + numeros.slice(2, 6) + '-' + numeros.slice(6);
  }
  return '(' + numeros.slice(0, 2) + ') ' + numeros.slice(2, 7) + '-' + numeros.slice(7);
}

function validarTelefone() {
  const valor = telefone.value.trim();
  if (valor === '') {
    return 'Informe o telefone.';
  }
  if (!REGEX_TELEFONE.test(valor)) {
    return 'Use o formato (49) 99999-0001.';
  }
  return '';
}

function validarSenha() {
  if (senha.value === '') {
    return 'Informe a senha.';
  }
  return '';
}

const regras = [
  { campo: telefone, validar: validarTelefone },
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

telefone.addEventListener('input', () => {
  telefone.value = formatarTelefone(telefone.value);
});

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
  mostrarToast('Login realizado com sucesso!');
  setTimeout(() => {
    window.location.href = 'conversas.html';
  }, 1000);
});