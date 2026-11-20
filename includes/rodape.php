<?php
$tipoRodape       = $tipoRodape ?? 'aviso';
$caminhoBase      = $caminhoBase ?? '';
$incluirLinkAdmin = $incluirLinkAdmin ?? false;
?>
<?php if ($tipoRodape === 'educacional'): ?>
  <footer class="rodape-educacional">
    <p>
      Clone do WhatsApp Web para fins educacionais. Sem vínculo com o WhatsApp/Meta. Dados fictícios.
      <?php if ($incluirLinkAdmin): ?>
        · <a href="<?= htmlspecialchars($caminhoBase, ENT_QUOTES, 'UTF-8') ?>admin/login.php">Área do administrador</a>
      <?php endif; ?>
    </p>
  </footer>
<?php else: ?>
  <footer class="rodape-aviso">Clone do WhatsApp Web para fins educacionais, sem vínculo com o WhatsApp ou a Meta. Todos os dados são fictícios.</footer>
<?php endif; ?>
</body>
</html>