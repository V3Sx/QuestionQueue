<?php
/**
 * Arquivo de Configuração - QuestionQueue
 * Configurado para XAMPP (MySQL em localhost com usuário root sem senha)
 * 
 * IMPORTANTE: 
 * - As configurações de sessão agora estão em includes_auth.php
 * - Este arquivo é opcional e serve apenas como referência
 * - Não inclua ini_set() para sessões aqui!
 */

// ============================================
// CONFIGURAÇÕES DO BANCO DE DADOS - XAMPP
// ============================================
define('DB_HOST', 'localhost');      // XAMPP padrão
define('DB_PORT', 3306);              // Porta padrão MySQL
define('DB_NAME', 'questionqueue');   // Nome do banco
define('DB_USER', 'root');            // Usuário XAMPP padrão
define('DB_PASS', '');                // Sem senha no XAMPP
define('DB_CHARSET', 'utf8mb4');      // Charset UTF-8

// ============================================
// CONFIGURAÇÕES DA APLICAÇÃO
// ============================================
define('APP_NAME', 'QuestionQueue');
define('APP_URL', 'http://localhost/questionQueue-06');
define('APP_VERSION', '1.0.0');

// ============================================
// CONFIGURAÇÕES DE SEGURANÇA
// ============================================
define('SESSION_TIMEOUT', 3600);           // 1 hora em segundos
define('PASSWORD_MIN_LENGTH', 6);          // Mínimo de caracteres na senha
define('MAX_LOGIN_ATTEMPTS', 5);           // Tentativas máximas de login
define('LOCKOUT_TIME', 900);               // Tempo de bloqueio em segundos (15 min)

// ============================================
// MODO DE DESENVOLVIMENTO
// ============================================
// true = Mostrar erros (DESENVOLVIMENTO)
// false = Ocultar erros (PRODUÇÃO)
define('DEBUG_MODE', true);

// IMPORTANTE: Não use ini_set aqui se a sessão já foi iniciada!
// As configurações de sessão estão em includes_auth.php
?>
