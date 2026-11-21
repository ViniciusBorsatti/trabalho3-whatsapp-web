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

function comprimentoTexto(string $valor): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($valor, 'UTF-8');
    }
    return preg_match_all('/./us', $valor, $correspondencias) ?: 0;
}

function inicialTexto(string $valor): string
{
    if (function_exists('mb_substr')) {
        $inicial = mb_substr($valor, 0, 1, 'UTF-8');
        return function_exists('mb_strtoupper') ? mb_strtoupper($inicial, 'UTF-8') : strtoupper($inicial);
    }
    preg_match('/^./us', $valor, $correspondencias);
    return strtoupper($correspondencias[0] ?? '');
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

/* ============================== Jadson ================================ */
function listarContatos(PDO $pdo, string $busca = ''): array
{
    $termo = '%' . $busca . '%';
    $stmt = $pdo->prepare('SELECT id, nome, telefone, recado, favorito, bloqueado, grupo FROM contatos WHERE nome LIKE ? OR telefone LIKE ? ORDER BY nome COLLATE utf8mb4_unicode_ci ASC');
    $stmt->execute([$termo, $termo]);
    return $stmt->fetchAll();
}

function buscarContatoPorId(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, nome, telefone, recado, favorito, bloqueado, grupo FROM contatos WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function telefoneJaCadastrado(PDO $pdo, string $telefoneNormalizado): bool
{
    $stmt = $pdo->prepare("SELECT id FROM contatos WHERE REPLACE(REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), ' ', ''), '-', '') = ? LIMIT 1");
    $stmt->execute([$telefoneNormalizado]);
    return (bool)$stmt->fetch();
}

function criarContato(PDO $pdo, string $nome, string $telefone): void
{
    $stmt = $pdo->prepare('INSERT INTO contatos (nome, telefone, recado) VALUES (?, ?, ?)');
    $stmt->execute([$nome, $telefone, 'Disponível']);
}

function alternarEstadoContato(PDO $pdo, int $id, string $campo, bool $estadoAtual): void
{
    if (!in_array($campo, ['favorito', 'bloqueado'], true)) {
        throw new InvalidArgumentException('Campo de contato inválido.');
    }
    $stmt = $pdo->prepare("UPDATE contatos SET $campo = ? WHERE id = ?");
    $stmt->execute([(int)!$estadoAtual, $id]);
}

function registrarDenuncia(PDO $pdo, int $contatoId, string $motivo, string $descricao, bool $bloquear): void
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO denuncias (contato_id, motivo, descricao, bloqueou) VALUES (?, ?, ?, ?)');
        $stmt->execute([$contatoId, $motivo, $descricao, (int)$bloquear]);
        if ($bloquear) {
            $stmt = $pdo->prepare('UPDATE contatos SET bloqueado = 1 WHERE id = ?');
            $stmt->execute([$contatoId]);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function atualizarPerfilUsuario(PDO $pdo, int $usuarioId, string $nome, string $recado): void
{
    $stmt = $pdo->prepare('UPDATE usuarios SET nome = ?, recado = ? WHERE id = ?');
    $stmt->execute([$nome, $recado, $usuarioId]);
}

