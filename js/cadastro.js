// Referências aos elementos do formulário
const form = document.getElementById('form-cadastro');
const nome = document.getElementById('nome');
const telefone = document.getElementById('telefone');
const email = document.getElementById('email');
const senha = document.getElementById('senha');
const confirmaSenha = document.getElementById('confirma-senha');
const toast = document.getElementById('toast');

// Formatos aceitos: telefone (49) 99999-0001 e e-mail com @ e domínio
const REGEX_TELEFONE = /^\(\d{2}\) \d{5}-\d{4}$/;
const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Guarda o timer do toast para poder reiniciá-lo
let temporizadorToast = null;

// Nome não pode ficar vazio nem só com espaços
function validarNome() {
  if (nome.value.trim() === '') {
    return 'Informe seu nome completo.';
  }
  return '';
}

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

// Máscara: recebe o texto digitado e devolve no formato (49) 99999-0001
function formatarTelefone(valor) {
  // Remove tudo que não é número e limita a 11 dígitos (DDD + número)
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

// A máscara roda a cada tecla ou colagem, antes da validação
telefone.addEventListener('input', () => {
  telefone.value = formatarTelefone(telefone.value);
});

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

// Senha com pelo menos 8 caracteres
function validarSenha() {
  if (senha.value === '') {
    return 'Crie uma senha.';
  }
  if (senha.value.length < 8) {
    return 'A senha precisa ter no mínimo 8 caracteres.';
  }
  return '';
}

// Confirmação precisa ser igual à senha
function validarConfirmacao() {
  if (confirmaSenha.value === '') {
    return 'Confirme a senha.';
  }
  if (confirmaSenha.value !== senha.value) {
    return 'As senhas não conferem.';
  }
  return '';
}

// Regra separada para a confirmação, usada também ao digitar na senha
const regraConfirmacao = { campo: confirmaSenha, validar: validarConfirmacao };

// Lista de todas as regras, validadas uma a uma no envio
const regras = [
  { campo: nome, validar: validarNome },
  { campo: telefone, validar: validarTelefone },
  { campo: email, validar: validarEmail },
  { campo: senha, validar: validarSenha },
  regraConfirmacao
];

// Mostra a mensagem embaixo do campo e marca o campo como inválido
function mostrarErro(campo, mensagem) {
  const erro = document.getElementById('erro-' + campo.id);
  erro.textContent = mensagem;
  campo.classList.add('campo-invalido');
  campo.setAttribute('aria-invalid', 'true');
}

// Remove a mensagem e a marcação de erro do campo
function limparErro(campo) {
  const erro = document.getElementById('erro-' + campo.id);
  erro.textContent = '';
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

// Exibe o aviso de sucesso por 3 segundos
function mostrarToast(texto) {
  toast.textContent = texto;
  toast.classList.add('visivel');
  clearTimeout(temporizadorToast);
  temporizadorToast = setTimeout(() => {
    toast.classList.remove('visivel');
  }, 3000);
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

// Se a confirmação já estiver preenchida, revalida ao mudar a senha
senha.addEventListener('input', () => {
  if (confirmaSenha.value !== '') {
    validarCampo(regraConfirmacao);
  }
});

// Ao enviar: impede o recarregamento da página e valida tudo
form.addEventListener('submit', (evento) => {
  // preventDefault evita que a página recarregue e apague os erros
  evento.preventDefault();

  let primeiroInvalido = null;

  // Valida todos os campos para mostrar todos os erros de uma vez
  regras.forEach((regra) => {
    const valido = validarCampo(regra);
    if (!valido && primeiroInvalido === null) {
      primeiroInvalido = regra.campo;
    }
  });

  // Foca no primeiro campo com erro
  if (primeiroInvalido !== null) {
    primeiroInvalido.focus();
    return;
  }

  // Formulário válido: mostra o aviso e limpa os campos
  mostrarToast('Conta criada com sucesso!');
  form.reset();
  nome.focus();
});