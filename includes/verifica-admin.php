<?php
// Protege páginas da área do administrador (use em arquivos dentro de admin/)
require_once __DIR__ . '/funcoes.php';

if (!adminLogado()) {
    mensagem('erro', 'Faça login como administrador para acessar essa página.');
    redirecionar('login.php'); // dentro de admin/, aponta para admin/login.php
}