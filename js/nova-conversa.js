(function () {
  const modal = document.getElementById('modal-novo-contato');
  const abrir = document.getElementById('btn-abrir-novo-contato');
  const fechar = document.getElementById('btn-fechar-modal-contato');
  const cancelar = document.getElementById('btn-cancelar-contato');
  const inputNome = document.getElementById('novo-nome');
  const toast = document.getElementById('toast-contatos');

  if (!modal || !abrir) return;
  function fecharModal() {
    modal.classList.remove('aberto');
    abrir.focus();
  }
  abrir.addEventListener('click', function () {
    modal.classList.add('aberto');
    inputNome.focus();
  });
  fechar.addEventListener('click', fecharModal);
  cancelar.addEventListener('click', fecharModal);
  modal.addEventListener('click', function (event) {
    if (event.target === modal) fecharModal();
  });
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && modal.classList.contains('aberto')) fecharModal();
  });
  document.getElementById('btn-novo-grupo').addEventListener('click', function () {
    toast.textContent = 'Novo grupo é apenas visual neste protótipo.';
    toast.classList.add('visivel');
    setTimeout(function () { toast.classList.remove('visivel'); }, 3500);
  });
})();
