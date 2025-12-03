<?php
/**
 * Arquivo de Configuração XAMPP - QuestionQueue
 * Este arquivo deve ser colocado na raiz do htdocs (C:\xampp\htdocs\)
 * 
 * IMPORTANTE: Coloque este arquivo ANTES de qualquer outro arquivo PHP
 * Adicione no início do seu php.ini ou use um arquivo de auto-load
 */

// ============================================
// CONFIGURAÇÕES DE SESSÃO
// DEVE SER FEITO ANTES DE session_start()
// ============================================

// Proteção contra XSS - cookies inacessíveis via JavaScript
ini_set('session.cookie_httponly', 1);

// Usar apenas cookies (não URL)
ini_set('session.use_only_cookies', 1);

// Modo estrito de sessão
ini_set('session.use_strict_mode', 1);

// Tempo de vida da sessão: 1 hora
ini_set('session.gc_maxlifetime', 3600);

// SameSite para cookies (previne CSRF)
ini_set('session.cookie_samesite', 'Lax');

// ============================================
// MODO DE DESENVOLVIMENTO
// ============================================
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
}

// ============================================
// TIMEZONE
// ============================================
date_default_timezone_set('America/Sao_Paulo');

// ============================================
// CONFIGURAÇÕES DO BANCO DE DADOS - XAMPP
// ============================================
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'questionqueue');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CONFIGURAÇÕES DA APLICAÇÃO
// ============================================
define('APP_NAME', 'QuestionQueue');
define('APP_URL', 'http://localhost');
define('APP_VERSION', '1.0.0');
define('SESSION_TIMEOUT', 3600);
define('PASSWORD_MIN_LENGTH', 6);
?>
