<?php
// Protege páginas da área do usuário: quem não estiver logado vai para o login
require_once __DIR__ . '/funcoes.php';

if (!usuarioLogado()) {
    mensagem('erro', 'Faça login para acessar essa página.');
    redirecionar('login.php');
}