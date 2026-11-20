// Referências aos elementos do formulário de login do administrador
const form = document.getElementById('form-admin-login');
const email = document.getElementById('email');
const senha = document.getElementById('senha');
const btnMostrarSenha = document.getElementById('btn-mostrar-senha');
const erroGeral = document.getElementById('erro-geral');

// Formato aceito para o e-mail: algo@dominio.extensao
const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Confere se o e-mail foi preenchido e tem formato válido
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

// Senha não pode ficar vazia (o mínimo de 8 caracteres é conferido também no servidor)
function validarSenha() {
  if (senha.value === '') {
    return 'Informe a senha.';
  }
  return '';
}

// Lista de regras validadas no envio e ao sair dos campos
const regras = [
  { campo: email, validar: validarEmail },
  { campo: senha, validar: validarSenha }
];

// Mostra a mensagem embaixo do campo e marca o campo como inválido
function mostrarErro(campo, mensagem) {
  document.getElementById('erro-' + campo.id).textContent = mensagem;
  campo.classList.add('campo-invalido');
  campo.setAttribute('aria-invalid', 'true');
}

// Remove a mensagem e a marcação de erro do campo
function limparErro(campo) {
  document.getElementById('erro-' + campo.id).textContent = '';
  campo.classList.remove('campo-invalido');
  campo.removeAttribute('aria-invalid');
}

// Executa a regra e mostra ou limpa o erro. Retorna true se o campo está válido
function validarCampo(regra) {
  const mensagem = regra.validar();
  if (mensagem) {
    mostrarErro(regra.campo, mensagem);
    return false;
  }
  limparErro(regra.campo);
  return true;
}

// Ao sair do campo, valida; ao digitar num campo já com erro, revalida
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

// Botão de olho: alterna o tipo do campo entre senha e texto
btnMostrarSenha.addEventListener('click', () => {
  const mostrar = senha.type === 'password';
  senha.type = mostrar ? 'text' : 'password';
  // aria-pressed informa a leitores de tela se o botão está ativado
  btnMostrarSenha.setAttribute('aria-pressed', String(mostrar));
  btnMostrarSenha.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');
  btnMostrarSenha.querySelector('i').className = mostrar ? 'bi bi-eye-slash' : 'bi bi-eye';
});

// Ao enviar: se houver erro, impede o envio e foca no primeiro campo inválido.
// Se estiver tudo certo, o formulário segue para o servidor (admin/login.php), que confere o login
form.addEventListener('submit', (evento) => {
  let primeiroInvalido = null;

  regras.forEach((regra) => {
    const valido = validarCampo(regra);
    if (!valido && primeiroInvalido === null) {
      primeiroInvalido = regra.campo;
    }
  });

  if (primeiroInvalido !== null) {
    // preventDefault impede o envio quando há erro
    evento.preventDefault();
    erroGeral.textContent = 'Revise os campos destacados.';
    primeiroInvalido.focus();
  }
});