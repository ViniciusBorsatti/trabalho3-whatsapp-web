// Referências aos elementos do formulário de login
const form = document.getElementById('form-login');
const telefone = document.getElementById('telefone');
const senha = document.getElementById('senha');
const btnMostrarSenha = document.getElementById('btn-mostrar-senha');
const erroGeral = document.getElementById('erro-geral');

// Mesmo formato do cadastro: (49) 99999-0001
const REGEX_TELEFONE = /^\(\d{2}\) \d{5}-\d{4}$/;

// Confere se o telefone está preenchido e no formato certo
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

// Senha não pode ficar vazia (o mínimo de 8 caracteres é checado no servidor)
function validarSenha() {
  if (senha.value === '') {
    return 'Informe a senha.';
  }
  return '';
}

// Máscara do telefone (mesma lógica do cadastro)
function formatarTelefone(valor) {
  // Remove tudo que não é número e limita a 11 dígitos
  const numeros = valor.replace(/\D/g, '').slice(0, 11);

  if (numeros.length === 0) {
    return '';
  }
  // Monta a máscara conforme o tamanho já digitado
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

// Lista de regras validadas no envio e ao sair dos campos
const regras = [
  { campo: telefone, validar: validarTelefone },
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

// A máscara roda a cada tecla ou colagem no telefone
telefone.addEventListener('input', () => {
  telefone.value = formatarTelefone(telefone.value);
});

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
// Se estiver tudo certo, o formulário segue para o servidor (login.php)
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