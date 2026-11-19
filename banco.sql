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

-- Tabelas das conversas (conversas, mensagens, qr_codes)
