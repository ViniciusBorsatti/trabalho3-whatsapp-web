/* ==========================================================================
   entrada.js — Lógica da página de Entrada (RN04: QR code de 60 segundos)
   ========================================================================== */

(function () {
  const TEMPO_INICIAL_QR = 60;
  const CHAVE_MANTER_CONECTADO = 'whatsapp_clone_manter_conectado';

  const qrMoldura = document.getElementById('qr-moldura');
  const qrImg = document.getElementById('qr-img');
  const qrOverlay = document.getElementById('qr-expirado-overlay');
  const qrContador = document.getElementById('qr-contador');
  const qrErro = document.getElementById('qr-erro');
  const qrEstadoConexao = document.getElementById('qr-estado-conexao');
  const btnSimular = document.getElementById('btn-simular-leitura');
  const btnNovoQr = document.getElementById('btn-novo-qr');
  const checkboxManter = document.getElementById('manter-conectado');

  let segundosRestantes = TEMPO_INICIAL_QR;
  let intervaloId = null;
  let qrExpirado = false;

  /**
   * Restaura preferência do checkbox "Manter conectado".
   */
  function inicializarPreferenciaConexao() {
    if (!checkboxManter) return;
    checkboxManter.checked = localStorage.getItem(CHAVE_MANTER_CONECTADO) === 'true';
    checkboxManter.addEventListener('change', function () {
      localStorage.setItem(CHAVE_MANTER_CONECTADO, String(checkboxManter.checked));
    });
  }

  /**
   * Gera um SVG dinâmico para simular a renovação visual do QR code.
   */
  function gerarSvgQrSimulado(semente) {
    let blocos = '';
    for (let linha = 0; linha < 7; linha++) {
      for (let col = 0; col < 7; col++) {
        // Evita sobrepor os três quadrados de alinhamento dos cantos
        const noCantoSupEsq = linha < 2 && col < 2;
        const noCantoSupDir = linha < 2 && col > 4;
        const noCantoInfEsq = linha > 4 && col < 2;
        if (noCantoSupEsq || noCantoSupDir || noCantoInfEsq) continue;

        const hash = (linha * 13 + col * 7 + semente) % 3;
        if (hash !== 0) {
          const x = 36 + col * 20;
          const y = 36 + linha * 20;
          blocos += `<rect x="${x}" y="${y}" width="14" height="14" rx="2"/>`;
        }
      }
    }

    const svg = `
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 220" width="220" height="220">
        <rect width="220" height="220" fill="#FFFFFF"/>
        <g fill="#111B21">
          <rect x="16" y="16" width="52" height="52" rx="4"/>
          <rect x="24" y="24" width="36" height="36" fill="#FFFFFF"/>
          <rect x="32" y="32" width="20" height="20"/>
          <rect x="152" y="16" width="52" height="52" rx="4"/>
          <rect x="160" y="24" width="36" height="36" fill="#FFFFFF"/>
          <rect x="168" y="32" width="20" height="20"/>
          <rect x="16" y="152" width="52" height="52" rx="4"/>
          <rect x="24" y="160" width="36" height="36" fill="#FFFFFF"/>
          <rect x="32" y="168" width="20" height="20"/>
          ${blocos}
        </g>
        <circle cx="110" cy="110" r="18" fill="#FFFFFF"/>
        <circle cx="110" cy="110" r="14" fill="#00A884"/>
        <path d="M104 110l4 4 8-8" stroke="#FFFFFF" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>`;

    return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg.trim());
  }

  /**
   * Atualiza o texto e o estado visual do contador do QR code.
   */
  function atualizarExibicaoContador() {
    if (segundosRestantes > 0) {
      qrContador.innerHTML = `O QR code expira em <strong>${segundosRestantes} s</strong>`;
      qrContador.classList.remove('expirado');
    } else {
      qrExpirado = true;
      qrMoldura.classList.add('expirado');
      qrOverlay.hidden = false;
      qrContador.textContent = 'QR expirado (RN04)';
      qrContador.classList.add('expirado');
      qrEstadoConexao.textContent = 'Este QR code expirou. Gere um novo código para conectar.';
      clearInterval(intervaloId);
    }
  }

  /**
   * Inicia ou reinicia a contagem regressiva de 60 segundos.
   */
  function iniciarContagemQr(renovarImagem) {
    clearInterval(intervaloId);
    segundosRestantes = TEMPO_INICIAL_QR;
    qrExpirado = false;
    qrErro.textContent = '';
    qrMoldura.classList.remove('expirado');
    qrOverlay.hidden = true;
    qrEstadoConexao.textContent = 'Estado da espera: Aguardando o celular...';

    if (renovarImagem) {
      const semente = Math.floor(Date.now() % 1000);
      qrImg.src = gerarSvgQrSimulado(semente);
    }

    atualizarExibicaoContador();

    intervaloId = setInterval(function () {
      segundosRestantes -= 1;
      atualizarExibicaoContador();
    }, 1000);
  }

  // Simula leitura do QR code e redireciona para conversas.php
  if (btnSimular) {
    btnSimular.addEventListener('click', function () {
      if (qrExpirado) {
        qrErro.textContent = 'Este QR code expirou. Clique em "Gerar novo QR code" antes de simular.';
        btnNovoQr.focus();
        return;
      }

      qrErro.textContent = '';
      qrEstadoConexao.textContent = 'Conectando...';
      btnSimular.disabled = true;

      setTimeout(function () {
        window.location.href = 'conversas.php';
      }, 400);
    });
  }

  // Gera um novo QR code e reinicia os 60 segundos
  if (btnNovoQr) {
    btnNovoQr.addEventListener('click', function () {
      iniciarContagemQr(true);
    });
  }

  inicializarPreferenciaConexao();
  iniciarContagemQr(false);
})();
