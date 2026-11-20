<?php
// Encerra a sessão do usuário e volta para a entrada
require_once __DIR__ . '/includes/funcoes.php';

unset($_SESSION['usuario']);
session_destroy();
redirecionar('index.php');