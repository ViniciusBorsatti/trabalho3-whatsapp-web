<?php
// Encerra a sessão do administrador e volta para o login do admin
require_once __DIR__ . '/../includes/funcoes.php';

unset($_SESSION['admin']);
session_destroy();
redirecionar('login.php');