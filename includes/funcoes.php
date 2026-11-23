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


function telefoneValido(string $telefone): bool
{
    return (bool)preg_match('/^\(\d{2}\) \d{5}-\d{4}$/', $telefone);
}

function telefoneEmUso(PDO $pdo, string $telefone, int $ignorarId = 0): bool
{
    $stmt = $pdo->prepare('SELECT id FROM contatos WHERE telefone = ? AND id <> ? LIMIT 1');
    $stmt->execute([$telefone, $ignorarId]);
    return (bool)$stmt->fetch();
}

function validarContato(PDO $pdo, array $dados, int $id = 0): array
{
    $erros = [];
    if ($dados['nome'] === '' || comprimentoTexto($dados['nome']) > 100) {
        $erros['nome'] = 'Informe um nome com até 100 caracteres.';
    }
    if (!telefoneValido($dados['telefone'])) {
        $erros['telefone'] = 'Use o formato (49) 99999-0001.';
    } elseif (telefoneEmUso($pdo, $dados['telefone'], $id)) {
        $erros['telefone'] = 'Telefone já cadastrado.';
    }
    if (comprimentoTexto($dados['recado']) > 150) {
        $erros['recado'] = 'O recado deve ter até 150 caracteres.';
    }
    return $erros;
}

function listarContatosPainel(PDO $pdo, string $busca = ''): array
{
    $termo = '%' . $busca . '%';
    $stmt = $pdo->prepare(
        'SELECT c.id, c.nome, c.telefone, c.recado, c.bloqueado, COUNT(m.id) AS total_mensagens
         FROM contatos c
         LEFT JOIN conversas cv ON cv.contato_id = c.id
         LEFT JOIN mensagens m ON m.conversa_id = cv.id
         WHERE c.grupo = 0 AND (c.nome LIKE ? OR c.telefone LIKE ?)
         GROUP BY c.id
         ORDER BY c.nome'
    );
    $stmt->execute([$termo, $termo]);
    return $stmt->fetchAll();
}

function resumoPainel(PDO $pdo): array
{
    return [
        'contatos'   => (int)$pdo->query('SELECT COUNT(*) FROM contatos WHERE grupo = 0')->fetchColumn(),
        'mensagens'  => (int)$pdo->query('SELECT COUNT(*) FROM mensagens')->fetchColumn(),
        'bloqueados' => (int)$pdo->query('SELECT COUNT(*) FROM contatos WHERE grupo = 0 AND bloqueado = 1')->fetchColumn(),
        'denuncias'  => (int)$pdo->query('SELECT COUNT(*) FROM denuncias')->fetchColumn(),
    ];
}

function inserirContatoPainel(PDO $pdo, array $dados): void
{
    $stmt = $pdo->prepare('INSERT INTO contatos (nome, telefone, recado, bloqueado) VALUES (?, ?, ?, ?)');
    $stmt->execute([$dados['nome'], $dados['telefone'], $dados['recado'], $dados['bloqueado']]);
}

function atualizarContatoPainel(PDO $pdo, int $id, array $dados): void
{
    $stmt = $pdo->prepare('UPDATE contatos SET nome = ?, telefone = ?, recado = ?, bloqueado = ? WHERE id = ?');
    $stmt->execute([$dados['nome'], $dados['telefone'], $dados['recado'], $dados['bloqueado'], $id]);
    $stmt = $pdo->prepare('UPDATE conversas SET nome = ? WHERE contato_id = ?');
    $stmt->execute([$dados['nome'], $id]);
}

function excluirContatoPainel(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM contatos WHERE id = ?');
    $stmt->execute([$id]);
}

function listarDenuncias(PDO $pdo): array
{
    return $pdo->query(
        'SELECT d.id, d.motivo, d.descricao, d.bloqueou, d.criada_em, c.nome AS contato
         FROM denuncias d
         JOIN contatos c ON c.id = d.contato_id
         ORDER BY d.criada_em DESC'
    )->fetchAll();
}

const LIMITE_FIXADAS = 3;
const LIMITE_APAGAR_SEGUNDOS = 15 * 60;

function responderJson(array $dados, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

function exigirUsuarioApi(): void
{
    if (!usuarioLogado()) {
        responderJson(['erro' => 'Faça login para continuar.'], 401);
    }
}

function listarConversas(PDO $pdo): array
{
    return $pdo->query(
        'SELECT cv.id, cv.contato_id, cv.nome, cv.grupo, cv.fixada, cv.favorita,
                (SELECT COUNT(*) FROM mensagens n
                 WHERE n.conversa_id = cv.id AND n.de <> \'eu\' AND n.lida = 0) AS nao_lidas,
                c.telefone, c.bloqueado,
                m.texto AS ultima_texto, m.apagada AS ultima_apagada, m.enviada_em AS ultima_em
         FROM conversas cv
         JOIN contatos c ON c.id = cv.contato_id
         LEFT JOIN mensagens m ON m.id = (SELECT MAX(id) FROM mensagens WHERE conversa_id = cv.id)
         ORDER BY cv.fixada DESC, m.enviada_em DESC'
    )->fetchAll();
}

function buscarConversaPorId(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare(
        'SELECT cv.id, cv.fixada, cv.favorita, c.bloqueado
         FROM conversas cv JOIN contatos c ON c.id = cv.contato_id
         WHERE cv.id = ?'
    );
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function alternarFixada(PDO $pdo, array $conversa): void
{
    if (!$conversa['fixada']) {
        $fixadas = (int)$pdo->query('SELECT COUNT(*) FROM conversas WHERE fixada = 1')->fetchColumn();
        if ($fixadas >= LIMITE_FIXADAS) {
            throw new DomainException('Você só pode fixar até 3 conversas.');
        }
    }
    $stmt = $pdo->prepare('UPDATE conversas SET fixada = ? WHERE id = ?');
    $stmt->execute([(int)!$conversa['fixada'], $conversa['id']]);
}

function alternarFavorita(PDO $pdo, array $conversa): void
{
    $stmt = $pdo->prepare('UPDATE conversas SET favorita = ? WHERE id = ?');
    $stmt->execute([(int)!$conversa['favorita'], $conversa['id']]);
}

function marcarComoLida(PDO $pdo, int $conversaId): void
{
    $stmt = $pdo->prepare("UPDATE mensagens SET lida = 1 WHERE conversa_id = ? AND de <> 'eu'");
    $stmt->execute([$conversaId]);
}

function listarMensagens(PDO $pdo, int $conversaId): array
{
    $stmt = $pdo->prepare('SELECT id, de, texto, apagada, enviada_em FROM mensagens WHERE conversa_id = ? ORDER BY enviada_em, id');
    $stmt->execute([$conversaId]);
    return $stmt->fetchAll();
}

function enviarMensagem(PDO $pdo, array $conversa, string $texto): int
{
    if ($conversa['bloqueado']) {
        throw new DomainException('Você bloqueou este contato. Desbloqueie para enviar mensagens.');
    }
    $stmt = $pdo->prepare("INSERT INTO mensagens (conversa_id, de, texto) VALUES (?, 'eu', ?)");
    $stmt->execute([$conversa['id'], $texto]);
    return (int)$pdo->lastInsertId();
}

function apagarParaTodos(PDO $pdo, int $mensagemId): void
{
    $stmt = $pdo->prepare('SELECT de, apagada, TIMESTAMPDIFF(SECOND, enviada_em, NOW()) AS segundos FROM mensagens WHERE id = ?');
    $stmt->execute([$mensagemId]);
    $mensagem = $stmt->fetch();

    if (!$mensagem || $mensagem['de'] !== 'eu') {
        throw new DomainException('Só é possível apagar mensagens enviadas por você.');
    }
    if ($mensagem['apagada']) {
        throw new DomainException('Esta mensagem já foi apagada.');
    }
    if ((int)$mensagem['segundos'] > LIMITE_APAGAR_SEGUNDOS) {
        throw new DomainException('Só é possível apagar para todos mensagens com até 15 minutos.');
    }

    $stmt = $pdo->prepare('UPDATE mensagens SET apagada = 1 WHERE id = ?');
    $stmt->execute([$mensagemId]);
}

function formatarMensagem(array $m): array
{
    return [
        'id'      => (int)$m['id'],
        'de'      => $m['de'],
        'texto'   => $m['apagada'] ? '' : $m['texto'],
        'apagada' => (bool)$m['apagada'],
        'hora'    => date('H:i', strtotime($m['enviada_em'])),
    ];
}

const VALIDADE_QR_SEGUNDOS = 60;

function descreverNavegador(string $agente): string
{
    $navegador = 'Navegador';
    foreach (['Edg' => 'Microsoft Edge', 'OPR' => 'Opera', 'Firefox' => 'Firefox', 'Chrome' => 'Google Chrome', 'Safari' => 'Safari'] as $marca => $nome) {
        if (strpos($agente, $marca) !== false) {
            $navegador = $nome;
            break;
        }
    }
    $sistema = 'computador';
    foreach (['Windows' => 'Windows', 'Mac OS' => 'macOS', 'Linux' => 'Linux'] as $marca => $nome) {
        if (strpos($agente, $marca) !== false) {
            $sistema = $nome;
            break;
        }
    }
    return $navegador . ' no ' . $sistema;
}

function enderecoNaRede(): string
{
    $host = $_SERVER['HTTP_HOST'];
    $nome = preg_replace('/:\d+$/', '', $host);
    $porta = substr($host, strlen($nome));

    if (in_array($nome, ['localhost', '127.0.0.1', '[::1]'], true)) {
        $ip = gethostbyname(gethostname());
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && strpos($ip, '127.') !== 0) {
            return $ip . $porta;
        }
    }
    return $host;
}

function linkConectar(string $token): string
{
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $pasta = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
    return $protocolo . '://' . enderecoNaRede() . $pasta . '/conectar.php?token=' . urlencode($token);
}

function gerarQr(PDO $pdo): string
{
    $token = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare('INSERT INTO qr_codes (token, navegador) VALUES (?, ?)');
    $stmt->execute([$token, descreverNavegador($_SERVER['HTTP_USER_AGENT'] ?? '')]);
    return $token;
}

function buscarQr(PDO $pdo, string $token): ?array
{
    $stmt = $pdo->prepare(
        'SELECT id, token, usuario_id, navegador, status, criado_em,
                TIMESTAMPDIFF(SECOND, criado_em, NOW()) AS idade
         FROM qr_codes WHERE token = ?'
    );
    $stmt->execute([$token]);
    $qr = $stmt->fetch();
    if (!$qr) {
        return null;
    }
    $qr['restante'] = max(0, VALIDADE_QR_SEGUNDOS - (int)$qr['idade']);
    return $qr;
}

function confirmarQr(PDO $pdo, string $token, int $usuarioId): void
{
    $qr = buscarQr($pdo, $token);
    if (!$qr) {
        throw new DomainException('QR code inválido. Gere um novo no computador.');
    }
    if ($qr['status'] !== 'aguardando') {
        throw new DomainException('Este QR code já foi usado. Gere um novo no computador.');
    }
    if ($qr['restante'] <= 0) {
        throw new DomainException('QR code expirado. Gere um novo no computador.');
    }
    $stmt = $pdo->prepare("UPDATE qr_codes SET status = 'confirmado', usuario_id = ? WHERE id = ? AND status = 'aguardando'");
    $stmt->execute([$usuarioId, $qr['id']]);
}

function entrarPeloQr(PDO $pdo, array $qr): bool
{
    $stmt = $pdo->prepare("UPDATE qr_codes SET status = 'usado' WHERE id = ? AND status = 'confirmado'");
    $stmt->execute([$qr['id']]);
    if ($stmt->rowCount() !== 1) {
        return false;
    }

    $stmt = $pdo->prepare('SELECT id, nome, telefone FROM usuarios WHERE id = ?');
    $stmt->execute([$qr['usuario_id']]);
    $usuario = $stmt->fetch();
    if (!$usuario) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id'       => $usuario['id'],
        'nome'     => $usuario['nome'],
        'telefone' => $usuario['telefone'],
    ];
    return true;
}

function abrirConversaComContato(PDO $pdo, int $contatoId): ?int
{
    $stmt = $pdo->prepare('SELECT id FROM conversas WHERE contato_id = ? LIMIT 1');
    $stmt->execute([$contatoId]);
    $id = $stmt->fetchColumn();
    if ($id) {
        return (int)$id;
    }

    $contato = buscarContatoPorId($pdo, $contatoId);
    if (!$contato) {
        return null;
    }

    $stmt = $pdo->prepare('INSERT INTO conversas (contato_id, nome, grupo) VALUES (?, ?, ?)');
    $stmt->execute([$contatoId, $contato['nome'], (int)$contato['grupo']]);
    return (int)$pdo->lastInsertId();
}
