SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS whatsapp_clone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE whatsapp_clone;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  telefone VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  recado VARCHAR(150) DEFAULT 'Disponível',
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS contatos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  telefone VARCHAR(20) NOT NULL UNIQUE,
  recado VARCHAR(150),
  grupo TINYINT(1) DEFAULT 0,
  favorito TINYINT(1) DEFAULT 0,
  bloqueado TINYINT(1) DEFAULT 0,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS denuncias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  contato_id INT NOT NULL,
  motivo VARCHAR(50) NOT NULL,
  descricao VARCHAR(500),
  bloqueou TINYINT(1) DEFAULT 0,
  criada_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (contato_id) REFERENCES contatos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conversas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  contato_id INT NOT NULL,
  nome VARCHAR(100) NOT NULL,
  grupo TINYINT(1) NOT NULL DEFAULT 0,
  fixada TINYINT(1) NOT NULL DEFAULT 0,
  favorita TINYINT(1) NOT NULL DEFAULT 0,
  nao_lidas INT NOT NULL DEFAULT 0,
  FOREIGN KEY (contato_id) REFERENCES contatos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mensagens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  conversa_id INT NOT NULL,
  de ENUM('eu', 'contato') NOT NULL,
  texto TEXT NOT NULL,
  apagada TINYINT(1) NOT NULL DEFAULT 0,
  enviada_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conversa_id) REFERENCES conversas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS qr_codes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  token VARCHAR(64) NOT NULL UNIQUE,
  usuario_id INT NULL,
  navegador VARCHAR(100),
  status ENUM('aguardando', 'confirmado', 'usado') NOT NULL DEFAULT 'aguardando',
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admins (nome, email, senha) VALUES
('Administrador', 'admin@teste.com', '$2y$10$qEGSW0x1sp0vGqKcnf9J9O57dXvbHV0Om5lbDc/MPl.b91FtN8Ni6');

INSERT INTO usuarios (nome, telefone, email, senha) VALUES
('Usuário Teste', '(49) 99999-0000', 'usuario@teste.com', '$2y$10$Ybi6q6rSVYa558.IsEraU.t1u8Dy6/ZhzzV3DJHNnOPaxKTWCyAOy');

INSERT INTO contatos (id, nome, recado, telefone, grupo, favorito, bloqueado) VALUES
(1, 'Trabalho DWI – Grupo 4', 'Grupo da disciplina', '(49) 99999-0001', 1, 1, 0),
(2, 'Mãe', 'Disponível', '(49) 99999-0002', 0, 1, 0),
(3, 'Lucas Ferreira', 'Jogando, depois respondo', '(49) 99999-0003', 0, 0, 0),
(4, 'Ana Beatriz', 'Estudando para as provas', '(49) 99999-0004', 0, 1, 0),
(5, 'Família Silva', 'Grupo da família', '(49) 99999-0005', 1, 0, 0),
(6, 'Carlos Mendes', 'Ocupado', '(49) 99999-0006', 0, 0, 1),
(7, 'Juliana Costa', 'Disponível', '(49) 99999-0007', 0, 0, 0),
(8, 'Futebol de Quinta', 'Quinta, 20h', '(49) 99999-0008', 1, 0, 0),
(9, 'Prof. Ricardo', 'Atendimento só em horário de aula', '(49) 99999-0009', 0, 0, 0),
(10, 'Mariana Rocha', 'Bateria fraca', '(49) 99999-0010', 0, 0, 0),
(11, 'Turma ADS 2026', 'Avisos da turma', '(49) 99999-0011', 1, 0, 0),
(12, 'Pedro Almeida', 'Em reunião', '(49) 99999-0012', 0, 0, 0),
(14, 'Bruno Oliveira', 'Disponível', '(49) 99999-0014', 0, 0, 0),
(15, 'Daniela Souza', 'No trabalho', '(49) 99999-0015', 0, 0, 0),
(16, 'Eduardo Lima', 'Só mensagens, por favor', '(49) 99999-0016', 0, 0, 0),
(17, 'Fernanda Castro', 'Viajando', '(49) 99999-0017', 0, 0, 0),
(18, 'Gabriel Santos', 'Na academia', '(49) 99999-0018', 0, 0, 0),
(19, 'Tatiane Ribeiro', 'Disponível', '(49) 99999-0019', 0, 0, 0);


INSERT INTO conversas (id, contato_id, nome, grupo, fixada, favorita, nao_lidas) VALUES
(1, 1, 'Trabalho DWI – Grupo 4', 1, 1, 1, 3),
(2, 2, 'Mãe', 0, 1, 1, 0),
(3, 3, 'Lucas Ferreira', 0, 0, 0, 2),
(4, 4, 'Ana Beatriz', 0, 0, 1, 0),
(5, 5, 'Família Silva', 1, 0, 0, 7),
(6, 6, 'Carlos Mendes', 0, 0, 0, 0),
(7, 7, 'Juliana Costa', 0, 0, 0, 1),
(8, 8, 'Futebol de Quinta', 1, 0, 0, 0),
(9, 9, 'Prof. Ricardo', 0, 0, 0, 0),
(10, 10, 'Mariana Rocha', 0, 0, 0, 0),
(11, 11, 'Turma ADS 2026', 1, 0, 0, 12),
(12, 12, 'Pedro Almeida', 0, 0, 0, 0);

INSERT INTO mensagens (conversa_id, de, texto, enviada_em) VALUES
(1, 'contato', 'Pessoal, quem vai fazer a tela de login?', '2026-11-14 19:10:00'),
(1, 'eu', 'Eu faço o login e o cadastro', '2026-11-14 19:12:00'),
(1, 'contato', 'Fechado! Eu fico com o painel admin', '2026-11-14 19:15:00'),
(1, 'contato', 'Entrega é até sexta, não esqueçam', '2026-11-14 19:20:00'),
(1, 'eu', 'Já subi a estrutura no repositório', '2026-11-15 10:00:00'),
(2, 'contato', 'Filho, vem almoçar no domingo?', '2026-11-14 11:02:00'),
(2, 'eu', 'Vou sim, mãe!', '2026-11-14 11:30:00'),
(2, 'contato', 'Vou fazer lasanha ❤️', '2026-11-14 11:31:00'),
(3, 'eu', 'E aí, bora jogar hoje?', '2026-11-14 18:00:00'),
(3, 'contato', 'Bora, depois das 21h', '2026-11-14 18:40:00'),
(3, 'contato', 'Chama o Pedro também', '2026-11-14 18:41:00'),
(4, 'contato', 'Você tem o resumo de banco de dados?', '2026-11-14 20:15:00'),
(4, 'eu', 'Tenho, te mando amanhã', '2026-11-14 20:30:00'),
(5, 'contato', 'Bom dia, família!', '2026-11-15 07:00:00'),
(5, 'contato', 'Alguém sabe se vai chover amanhã?', '2026-11-15 09:45:00'),
(6, 'contato', 'Promoção imperdível, clique no link!', '2026-11-10 09:00:00'),
(6, 'contato', 'Última chance!!!', '2026-11-10 09:05:00'),
(7, 'eu', 'Obrigado pela ajuda ontem!', '2026-11-14 14:00:00'),
(7, 'contato', 'Imagina, precisando é só chamar', '2026-11-14 14:20:00'),
(8, 'contato', 'Quinta tem jogo às 20h', '2026-11-13 12:00:00'),
(8, 'eu', 'Tô dentro', '2026-11-13 12:10:00'),
(9, 'eu', 'Professor, posso entregar o trabalho na segunda?', '2026-11-12 10:00:00'),
(9, 'contato', 'Pode sim, sem problemas.', '2026-11-12 13:30:00'),
(10, 'contato', 'Viu o filme que te indiquei?', '2026-11-11 21:00:00'),
(10, 'eu', 'Vi! Muito bom', '2026-11-11 22:15:00'),
(11, 'contato', 'Amanhã não vai ter aula', '2026-11-14 17:30:00'),
(11, 'contato', 'Confirmado pela coordenação', '2026-11-14 17:32:00'),
(12, 'contato', 'Me devolve o carregador kkk', '2026-11-09 15:00:00'),
(12, 'eu', 'Amanhã eu levo', '2026-11-09 15:10:00');

