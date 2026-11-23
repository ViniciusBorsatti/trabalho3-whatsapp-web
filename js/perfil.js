// Abre e fecha o modal de denúncia sem usar onclick no HTML
const modalDenuncia = document.getElementById('modal-denuncia');
const btnAbrirDenuncia = document.getElementById('btn-abrir-denuncia');

if (modalDenuncia && btnAbrirDenuncia) {
  btnAbrirDenuncia.addEventListener('click', function () {
    modalDenuncia.classList.add('aberto');
    document.getElementById('motivo-denuncia').focus();
  });

  document.querySelectorAll('[data-fechar-denuncia]').forEach(function (botao) {
    botao.addEventListener('click', function () {
      modalDenuncia.classList.remove('aberto');
    });
  });

  document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape') modalDenuncia.classList.remove('aberto');
  });
}
