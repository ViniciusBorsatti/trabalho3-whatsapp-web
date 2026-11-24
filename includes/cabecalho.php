<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$tituloPagina = $tituloPagina ?? 'WhatsApp Web';
$cssPagina    = $cssPagina ?? '';
$caminhoBase  = $caminhoBase ?? '';
$scriptsHead  = $scriptsHead ?? [];
$scriptsDefer = $scriptsDefer ?? [];
$versao = function (string $arquivo): string {
    $caminho = __DIR__ . '/../' . $arquivo;
    return $arquivo . (is_file($caminho) ? '?v=' . filemtime($caminho) : '');
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($tituloPagina, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= htmlspecialchars($caminhoBase . $versao('css/style.css'), ENT_QUOTES, 'UTF-8') ?>">
  <?php if ($cssPagina !== ''): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($caminhoBase . $versao($cssPagina), ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>
  <?php foreach ($scriptsHead as $src): ?>
    <script src="<?= htmlspecialchars($caminhoBase . $versao($src), ENT_QUOTES, 'UTF-8') ?>"></script>
  <?php endforeach; ?>
  <?php foreach ($scriptsDefer as $src): ?>
    <script src="<?= htmlspecialchars($caminhoBase . $versao($src), ENT_QUOTES, 'UTF-8') ?>" defer></script>
  <?php endforeach; ?>
</head>
<body>