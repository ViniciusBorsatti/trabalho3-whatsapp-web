<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function limpar(?string $valor): string
{
    return trim((string)($valor ?? ''));
}

function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function mensagem(?string $tipo = null, ?string $texto = null): ?array
{
    if ($tipo !== null && $texto !== null) {
        $_SESSION['flash_mensagem'] = [
            'tipo'  => $tipo,
            'texto' => $texto,
        ];
        return null;
    }

    if (!empty($_SESSION['flash_mensagem']) && is_array($_SESSION['flash_mensagem'])) {
        $msg = $_SESSION['flash_mensagem'];
        unset($_SESSION['flash_mensagem']);
        return $msg;
    }

    return null;
}

function esc(?string $valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}


function usuarioLogado(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function adminLogado(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function buscarUsuarioPorTelefone(PDO $pdo, string $telefone): ?array
{
    $stmt = $pdo->prepare('SELECT id, nome, telefone, email, senha, recado, criado_em FROM usuarios WHERE telefone = ? LIMIT 1');
    $stmt->execute([$telefone]);
    $usuario = $stmt->fetch();
    return $usuario ?: null;
}

function buscarAdminPorEmail(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare('SELECT id, nome, email, senha FROM admins WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    return $admin ?: null;
}

