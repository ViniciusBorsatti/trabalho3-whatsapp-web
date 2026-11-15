const form = document.getElementById('form-cadastro');
const nome = document.getElementById('nome');
const telefone = document.getElementById('telefone');
const email = document.getElementById('email');
const senha = document.getElementById('senha');
const confirmaSenha = document.getElementById('confirma-senha');
const toast = document.getElementById('toast');

const REGEX_TELEFONE = /^\(\d{2}\) \d{5}-\d{4}$/;
const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

let temporizadorToast = null;

function validarNome() {
  if (nome.value.trim() === '') {
    return 'Informe seu nome completo.';
  }
  return '';
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

telefone.addEventListener('input', () => {
  telefone.value = formatarTelefone(telefone.value);
});

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
    return 'Crie uma senha.';
  }
  if (senha.value.length < 8) {
    return 'A senha precisa ter no mínimo 8 caracteres.';
  }
  return '';
}

function validarConfirmacao() {
  if (confirmaSenha.value === '') {
    return 'Confirme a senha.';
  }
  if (confirmaSenha.value !== senha.value) {
    return 'As senhas não conferem.';
  }
  return '';
}

const regraConfirmacao = { campo: confirmaSenha, validar: validarConfirmacao };

const regras = [
  { campo: nome, validar: validarNome },
  { campo: telefone, validar: validarTelefone },
  { campo: email, validar: validarEmail },
  { campo: senha, validar: validarSenha },
  regraConfirmacao
];

function mostrarErro(campo, mensagem) {
  const erro = document.getElementById('erro-' + campo.id);
  erro.textContent = mensagem;
  campo.classList.add('campo-invalido');
  campo.setAttribute('aria-invalid', 'true');
}

function limparErro(campo) {
  const erro = document.getElementById('erro-' + campo.id);
  erro.textContent = '';
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

senha.addEventListener('input', () => {
  if (confirmaSenha.value !== '') {
    validarCampo(regraConfirmacao);
  }
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
    primeiroInvalido.focus();
    return;
  }

  mostrarToast('Conta criada com sucesso!');
  form.reset();
  nome.focus();
});