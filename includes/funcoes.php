<?php

date_default_timezone_set('America/Sao_Paulo');

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

function listarConversas(PDO $pdo, int $meuId): array
{
    $stmt = $pdo->prepare(
        'SELECT cv.id, cv.contato_id, cv.nome, cv.grupo, cv.fixada, c.favorito AS favorita,
                (SELECT COUNT(*) FROM mensagens n
                 WHERE n.conversa_id = cv.id AND n.de <> \'eu\' AND n.lida = 0) AS nao_lidas,
                c.telefone, c.bloqueado,
                m.texto AS ultima_texto, m.apagada AS ultima_apagada, m.arquivo_tipo AS ultima_tipo, m.enviada_em AS ultima_em
         FROM conversas cv
         JOIN contatos c ON c.id = cv.contato_id
         LEFT JOIN mensagens m ON m.id = (SELECT MAX(id) FROM mensagens WHERE conversa_id = cv.id)
         WHERE cv.usuario_id = ?
         ORDER BY cv.fixada DESC, m.enviada_em DESC'
    );
    $stmt->execute([$meuId]);
    return $stmt->fetchAll();
}

function buscarConversaPorId(PDO $pdo, int $id, int $meuId): ?array
{
    $stmt = $pdo->prepare(
        'SELECT cv.id, cv.usuario_id, cv.contato_id, cv.fixada, c.favorito AS favorita, c.bloqueado
         FROM conversas cv JOIN contatos c ON c.id = cv.contato_id
         WHERE cv.id = ? AND cv.usuario_id = ?'
    );
    $stmt->execute([$id, $meuId]);
    return $stmt->fetch() ?: null;
}

function alternarFixada(PDO $pdo, array $conversa): void
{
    if (!$conversa['fixada']) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM conversas WHERE fixada = 1 AND usuario_id = ?');
        $stmt->execute([$conversa['usuario_id']]);
        $fixadas = (int)$stmt->fetchColumn();
        if ($fixadas >= LIMITE_FIXADAS) {
            throw new DomainException('Você só pode fixar até 3 conversas.');
        }
    }
    $stmt = $pdo->prepare('UPDATE conversas SET fixada = ? WHERE id = ?');
    $stmt->execute([(int)!$conversa['fixada'], $conversa['id']]);
}

function alternarFavorita(PDO $pdo, array $conversa): void
{
    $stmt = $pdo->prepare('UPDATE contatos SET favorito = ? WHERE id = ?');
    $stmt->execute([(int)!$conversa['favorita'], $conversa['contato_id']]);
}

function marcarComoLida(PDO $pdo, int $conversaId): void
{
    $stmt = $pdo->prepare("UPDATE mensagens SET lida = 1 WHERE conversa_id = ? AND de <> 'eu'");
    $stmt->execute([$conversaId]);
}

function listarMensagens(PDO $pdo, int $conversaId): array
{
    $stmt = $pdo->prepare('SELECT id, de, texto, apagada, arquivo, arquivo_tipo, enviada_em FROM mensagens WHERE conversa_id = ? ORDER BY enviada_em, id');
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

function conferirPodeApagar($mensagem, bool $minha): void
{
    if (!$mensagem || !$minha) {
        throw new DomainException('Só é possível apagar mensagens enviadas por você.');
    }
    if ($mensagem['apagada']) {
        throw new DomainException('Esta mensagem já foi apagada.');
    }
    if ((int)$mensagem['segundos'] > LIMITE_APAGAR_SEGUNDOS) {
        throw new DomainException('Só é possível apagar para todos mensagens com até 15 minutos.');
    }
}

function removerArquivo(?string $caminho): void
{
    if ($caminho && is_file(__DIR__ . '/../' . $caminho)) {
        unlink(__DIR__ . '/../' . $caminho);
    }
}

function apagarParaTodos(PDO $pdo, int $mensagemId, int $meuId): void
{
    $stmt = $pdo->prepare(
        'SELECT m.de, m.apagada, m.arquivo, TIMESTAMPDIFF(SECOND, m.enviada_em, NOW()) AS segundos
         FROM mensagens m JOIN conversas cv ON cv.id = m.conversa_id
         WHERE m.id = ? AND cv.usuario_id = ?'
    );
    $stmt->execute([$mensagemId, $meuId]);
    $mensagem = $stmt->fetch();
    conferirPodeApagar($mensagem, $mensagem && $mensagem['de'] === 'eu');

    $stmt = $pdo->prepare('UPDATE mensagens SET apagada = 1, arquivo = NULL WHERE id = ?');
    $stmt->execute([$mensagemId]);
    removerArquivo($mensagem['arquivo']);
}

function formatarMensagem(array $m): array
{
    return [
        'id'      => (int)$m['id'],
        'de'      => $m['de'],
        'texto'   => $m['apagada'] ? '' : $m['texto'],
        'apagada' => (bool)$m['apagada'],
        'arquivo' => $m['apagada'] ? null : $m['arquivo'],
        'tipo'    => $m['apagada'] ? null : $m['arquivo_tipo'],
        'hora'    => date('H:i', strtotime($m['enviada_em'])),
    ];
}

const TAMANHO_MAXIMO_ARQUIVO = 5 * 1024 * 1024;
const TIPOS_ARQUIVO = [
    'image/jpeg'      => ['jpg', 'imagem'],
    'image/png'       => ['png', 'imagem'],
    'image/gif'       => ['gif', 'imagem'],
    'image/webp'      => ['webp', 'imagem'],
    'application/pdf' => ['pdf', 'documento'],
];

function tipoDoArquivo(string $caminho): string
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipo = finfo_file($finfo, $caminho);
        finfo_close($finfo);
        return (string)$tipo;
    }
    $imagem = @getimagesize($caminho);
    if ($imagem) {
        return $imagem['mime'];
    }
    return file_get_contents($caminho, false, null, 0, 5) === '%PDF-' ? 'application/pdf' : '';
}

function salvarArquivoEnviado(array $arquivo): array
{
    if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($arquivo['tmp_name'])) {
        throw new DomainException('Não foi possível receber o arquivo. Tente de novo.');
    }
    if ($arquivo['size'] > TAMANHO_MAXIMO_ARQUIVO) {
        throw new DomainException('O arquivo pode ter no máximo 5 MB.');
    }

    $tipo = tipoDoArquivo($arquivo['tmp_name']);
    if (!isset(TIPOS_ARQUIVO[$tipo])) {
        throw new DomainException('Envie uma foto (JPG, PNG, GIF ou WEBP) ou um PDF.');
    }
    [$extensao, $categoria] = TIPOS_ARQUIVO[$tipo];

    $pasta = __DIR__ . '/../uploads';
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }
    $nomeSalvo = bin2hex(random_bytes(16)) . '.' . $extensao;
    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . '/' . $nomeSalvo)) {
        throw new DomainException('Não foi possível salvar o arquivo.');
    }

    $nomeOriginal = basename((string)$arquivo['name']);
    $nomeOriginal = function_exists('mb_substr') ? mb_substr($nomeOriginal, 0, 150) : substr($nomeOriginal, 0, 150);
    return ['uploads/' . $nomeSalvo, $categoria, $nomeOriginal];
}

function enviarArquivo(PDO $pdo, array $conversa, array $arquivo): int
{
    if ($conversa['bloqueado']) {
        throw new DomainException('Você bloqueou este contato. Desbloqueie para enviar mensagens.');
    }
    [$caminho, $categoria, $nomeOriginal] = salvarArquivoEnviado($arquivo);
    $stmt = $pdo->prepare("INSERT INTO mensagens (conversa_id, de, texto, arquivo, arquivo_tipo) VALUES (?, 'eu', ?, ?, ?)");
    $stmt->execute([$conversa['id'], $nomeOriginal, $caminho, $categoria]);
    return (int)$pdo->lastInsertId();
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
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    $protocolo = $https ? 'https' : 'http';
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

function abrirConversaComContato(PDO $pdo, int $contatoId, int $meuId): ?int
{
    $stmt = $pdo->prepare('SELECT id FROM conversas WHERE contato_id = ? AND usuario_id = ? LIMIT 1');
    $stmt->execute([$contatoId, $meuId]);
    $id = $stmt->fetchColumn();
    if ($id) {
        return (int)$id;
    }

    $contato = buscarContatoPorId($pdo, $contatoId);
    if (!$contato) {
        return null;
    }

    $stmt = $pdo->prepare('INSERT INTO conversas (usuario_id, contato_id, nome, grupo) VALUES (?, ?, ?, ?)');
    $stmt->execute([$meuId, $contatoId, $contato['nome'], (int)$contato['grupo']]);
    return (int)$pdo->lastInsertId();
}

function fotoContato(int $id): string
{
    return is_file(__DIR__ . '/../img/avatares/' . $id . '.svg') ? 'img/avatares/' . $id . '.svg' : 'img/avatar-padrao.svg';
}

function idDaConversa(string $valor): array
{
    if (preg_match('/^u(\d+)$/', $valor, $partes)) {
        return ['usuario', (int)$partes[1]];
    }
    return ['contato', (int)$valor];
}

function abrirChatPorTelefone(PDO $pdo, int $meuId, string $telefone): int
{
    if (!telefoneValido($telefone)) {
        throw new DomainException('Use o formato (49) 99999-0001.');
    }
    $outro = buscarUsuarioPorTelefone($pdo, $telefone);
    if (!$outro) {
        throw new DomainException('Nenhum usuário do site tem esse telefone.');
    }
    if ((int)$outro['id'] === $meuId) {
        throw new DomainException('Esse é o seu próprio número.');
    }

    $par = [min($meuId, (int)$outro['id']), max($meuId, (int)$outro['id'])];
    $stmt = $pdo->prepare('SELECT id FROM chats WHERE usuario1_id = ? AND usuario2_id = ?');
    $stmt->execute($par);
    $id = $stmt->fetchColumn();
    if ($id) {
        return (int)$id;
    }

    $stmt = $pdo->prepare('INSERT INTO chats (usuario1_id, usuario2_id) VALUES (?, ?)');
    $stmt->execute($par);
    return (int)$pdo->lastInsertId();
}

function listarChats(PDO $pdo, int $meuId): array
{
    $stmt = $pdo->prepare(
        'SELECT ch.id, u.nome, u.telefone, u.recado,
                (SELECT COUNT(*) FROM mensagens_chat n
                 WHERE n.chat_id = ch.id AND n.remetente_id <> ? AND n.lida = 0) AS nao_lidas,
                m.texto AS ultima_texto, m.apagada AS ultima_apagada, m.arquivo_tipo AS ultima_tipo, m.enviada_em AS ultima_em
         FROM chats ch
         JOIN usuarios u ON u.id = IF(ch.usuario1_id = ?, ch.usuario2_id, ch.usuario1_id)
         LEFT JOIN mensagens_chat m ON m.id = (SELECT MAX(id) FROM mensagens_chat WHERE chat_id = ch.id)
         WHERE ch.usuario1_id = ? OR ch.usuario2_id = ?'
    );
    $stmt->execute([$meuId, $meuId, $meuId, $meuId]);
    return $stmt->fetchAll();
}

function buscarChat(PDO $pdo, int $chatId, int $meuId): ?array
{
    $stmt = $pdo->prepare('SELECT id FROM chats WHERE id = ? AND (usuario1_id = ? OR usuario2_id = ?)');
    $stmt->execute([$chatId, $meuId, $meuId]);
    return $stmt->fetch() ?: null;
}

function listarMensagensChat(PDO $pdo, int $chatId, int $meuId): array
{
    $stmt = $pdo->prepare(
        "SELECT id, IF(remetente_id = ?, 'eu', 'contato') AS de, texto, apagada, arquivo, arquivo_tipo, enviada_em
         FROM mensagens_chat WHERE chat_id = ? ORDER BY enviada_em, id"
    );
    $stmt->execute([$meuId, $chatId]);
    return $stmt->fetchAll();
}

function enviarMensagemChat(PDO $pdo, int $chatId, int $meuId, string $texto, ?string $arquivo = null, ?string $tipo = null): int
{
    $stmt = $pdo->prepare('INSERT INTO mensagens_chat (chat_id, remetente_id, texto, arquivo, arquivo_tipo) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$chatId, $meuId, $texto, $arquivo, $tipo]);
    return (int)$pdo->lastInsertId();
}

function enviarArquivoChat(PDO $pdo, int $chatId, int $meuId, array $arquivo): int
{
    [$caminho, $categoria, $nomeOriginal] = salvarArquivoEnviado($arquivo);
    return enviarMensagemChat($pdo, $chatId, $meuId, $nomeOriginal, $caminho, $categoria);
}

function apagarMensagemChat(PDO $pdo, int $mensagemId, int $meuId): void
{
    $stmt = $pdo->prepare('SELECT remetente_id, apagada, arquivo, TIMESTAMPDIFF(SECOND, enviada_em, NOW()) AS segundos FROM mensagens_chat WHERE id = ?');
    $stmt->execute([$mensagemId]);
    $mensagem = $stmt->fetch();
    conferirPodeApagar($mensagem, $mensagem && (int)$mensagem['remetente_id'] === $meuId);

    $stmt = $pdo->prepare('UPDATE mensagens_chat SET apagada = 1, arquivo = NULL WHERE id = ?');
    $stmt->execute([$mensagemId]);
    removerArquivo($mensagem['arquivo']);
}

function marcarChatComoLido(PDO $pdo, int $chatId, int $meuId): void
{
    $stmt = $pdo->prepare('UPDATE mensagens_chat SET lida = 1 WHERE chat_id = ? AND remetente_id <> ?');
    $stmt->execute([$chatId, $meuId]);
}
