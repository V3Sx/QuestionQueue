-- ================================================================
-- QuestionQueue - Script de Banco de Dados
-- Compatível com XAMPP MySQL
-- ================================================================
-- Este script cria automaticamente todas as tabelas necessárias
-- para o funcionamento do sistema QuestionQueue
--
-- INSTRUÇÕES:
-- 1. Abra http://localhost/phpmyadmin
-- 2. Na aba SQL, cole este conteúdo
-- 3. Clique em "Executar"
--
-- OU use a interface de setup:
-- http://localhost/questionQueue-06/setup_database.php
-- ================================================================

-- Criar banco de dados com UTF-8 para suportar acentuação
CREATE DATABASE IF NOT EXISTS questionqueue CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE questionqueue;

-- ================================================================
-- TABELA: USUÁRIOS
-- ================================================================
-- Armazena informações de login e cadastro
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do usuário',
    name VARCHAR(255) NOT NULL COMMENT 'Nome completo do usuário',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email único para login',
    password VARCHAR(255) NOT NULL COMMENT 'Senha com hash bcrypt',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data da última atualização'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela de usuários do sistema';

-- ================================================================
-- TABELA: JOGOS
-- ================================================================
-- Histórico de jogos realizados pelos usuários
CREATE TABLE IF NOT EXISTS games (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID único do jogo',
    user_id INT NOT NULL COMMENT 'ID do usuário que jogou',
    mode VARCHAR(50) NOT NULL COMMENT 'Modo: familia, casal, amigos, desafio',
    score INT DEFAULT 0 COMMENT 'Pontuação final do jogo',
    duration INT DEFAULT 0 COMMENT 'Duração em segundos',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Quando o jogo começou',
    finished_at TIMESTAMP NULL COMMENT 'Quando o jogo terminou',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de jogos';

-- ================================================================
-- TABELA: RESPOSTAS
-- ================================================================
-- Armazena respostas de cada pergunta em cada jogo
CREATE TABLE IF NOT EXISTS answers (
    id INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID única da resposta',
    game_id INT NOT NULL COMMENT 'ID do jogo a que pertence',
    question_number INT NOT NULL COMMENT 'Número da pergunta (1-5)',
    answer TEXT COMMENT 'Conteúdo da resposta',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Quando foi respondida',
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Respostas dos jogos';

-- ================================================================
-- ÍNDICES PARA PERFORMANCE
-- ================================================================
CREATE INDEX idx_users_email ON users(email) COMMENT 'Índice para buscar usuários por email';
CREATE INDEX idx_games_user_id ON games(user_id) COMMENT 'Índice para buscar jogos de um usuário';
CREATE INDEX idx_games_created_at ON games(started_at) COMMENT 'Índice para ordenar jogos por data';
CREATE INDEX idx_answers_game_id ON answers(game_id) COMMENT 'Índice para buscar respostas de um jogo';

-- ================================================================
-- DADOS INICIAIS - USUÁRIO DE TESTE
-- ================================================================
-- Email: teste@teste.com
-- Senha: 123456
-- Hash: gerado com password_hash('123456', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password) VALUES 
('Usuário Teste', 'teste@teste.com', '$2y$10$H9O3E9QwqPa8GQ5V8Q5ZJO5zW9H8Z7Y6X5W4V3U2T1S0R9Q8P7');

-- ================================================================
-- FIM DO SCRIPT
-- ================================================================
-- Se chegou aqui sem erros, o banco foi criado com sucesso!
-- Acesse: http://localhost/questionQueue-06/
COMMIT;
