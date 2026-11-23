(function () {
  const CHAVE_MANTER_CONECTADO = 'whatsapp_clone_manter_conectado';
  const INTERVALO_CONSULTA = 2000;

  const qrMoldura = document.getElementById('qr-moldura');
  const qrImg = document.getElementById('qr-img');
  const qrOverlay = document.getElementById('qr-expirado-overlay');
  const qrContador = document.getElementById('qr-contador');
  const qrErro = document.getElementById('qr-erro');
  const qrEstadoConexao = document.getElementById('qr-estado-conexao');
  const qrLink = document.getElementById('qr-link');
  const qrEndereco = document.getElementById('qr-endereco');
  const btnNovoQr = document.getElementById('btn-novo-qr');
  const checkboxManter = document.getElementById('manter-conectado');

  let token = null;
  let segundosRestantes = 0;
  let intervaloContagem = null;
  let intervaloConsulta = null;

  function inicializarPreferenciaConexao() {
    if (!checkboxManter) return;
    checkboxManter.checked = localStorage.getItem(CHAVE_MANTER_CONECTADO) === 'true';
    checkboxManter.addEventListener('change', function () {
      localStorage.setItem(CHAVE_MANTER_CONECTADO, String(checkboxManter.checked));
    });
  }

  function pararTudo() {
    clearInterval(intervaloContagem);
    clearInterval(intervaloConsulta);
  }

  function mostrarExpirado() {
    pararTudo();
    qrMoldura.classList.add('expirado');
    qrOverlay.hidden = false;
    qrContador.textContent = 'QR code expirado';
    qrContador.classList.add('expirado');
    qrEstadoConexao.textContent = 'Gere um novo QR code para conectar.';
    qrLink.classList.add('desativado');
    qrLink.setAttribute('aria-disabled', 'true');
  }

  function atualizarContador() {
    if (segundosRestantes <= 0) {
      mostrarExpirado();
      return;
    }
    qrContador.innerHTML = 'O QR code expira em <strong>' + segundosRestantes + ' s</strong>';
  }

  async function consultarStatus() {
    try {
      const resposta = await fetch('api/qr.php?token=' + encodeURIComponent(token));
      const dados = await resposta.json();

      if (dados.status === 'conectado') {
        pararTudo();
        qrEstadoConexao.textContent = 'Celular confirmado! Abrindo suas conversas...';
        window.location.href = 'conversas.php';
      } else if (dados.status === 'expirado' || dados.status === 'usado') {
        mostrarExpirado();
      } else if (dados.status === 'aguardando') {
        segundosRestantes = dados.restante;
      }
    } catch (erro) {
      qrErro.textContent = 'Não foi possível falar com o servidor.';
    }
  }

  async function gerarQr() {
    pararTudo();
    qrErro.textContent = '';
    qrMoldura.classList.remove('expirado');
    qrOverlay.hidden = true;
    qrContador.classList.remove('expirado');
    qrContador.textContent = 'Gerando QR code...';
    qrEstadoConexao.textContent = 'Aguardando o celular...';
    qrLink.classList.remove('desativado');
    qrLink.removeAttribute('aria-disabled');

    try {
      const resposta = await fetch('api/qr.php', { method: 'POST', body: new URLSearchParams({ acao: 'gerar' }) });
      const dados = await resposta.json();

      token = dados.token;
      segundosRestantes = dados.restante;
      qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=8&data=' + encodeURIComponent(dados.link);
      qrLink.href = dados.link;
      qrEndereco.textContent = dados.link.split('/conectar.php')[0].replace(/^https?:\/\//, '');

      atualizarContador();
      intervaloContagem = setInterval(function () {
        segundosRestantes -= 1;
        atualizarContador();
      }, 1000);
      intervaloConsulta = setInterval(consultarStatus, INTERVALO_CONSULTA);
    } catch (erro) {
      qrErro.textContent = 'Não foi possível gerar o QR code.';
    }
  }

  qrLink.addEventListener('click', function (evento) {
    if (qrLink.classList.contains('desativado')) {
      evento.preventDefault();
      qrErro.textContent = 'Este QR code expirou. Clique em "Gerar novo QR code".';
    }
  });

  btnNovoQr.addEventListener('click', gerarQr);

  inicializarPreferenciaConexao();
  gerarQr();
})();
