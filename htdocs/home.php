<?php
require_once 'includes_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Verifica se está logado, caso contrário redireciona para login
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Get username from session
$user = $auth->getUser();
$username = $user['name'] ?? $_SESSION['username'] ?? 'Player';

// Game functions
function startGame($mode) {
    $modeNames = [
        'familia' => 'Modo Família',
        'casal' => 'Modo Casal',
        'amigos' => 'Modo Amigos',
        'desafio' => 'Modo Desafio'
    ];
    
    return $modeNames[$mode] ?? 'Modo Desconhecido';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Queue - Dashboard</title>
    <link rel = 'stylesheet' href = 'homeStyle.css'>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">
            <h1>Question Queue</h1>
        </div>
        <div class="user-info">
            <span class="user-welcome">Olá, <strong id="username-display"><?php echo htmlspecialchars($username); ?></strong>! 👋</span>
            <button class="theme-toggle" onclick="toggleTheme()">
                <span id="theme-icon">🌙</span>
            </button>
            <button class="btn-logout" onclick="logout()">Sair</button>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Game Modes -->
        <div class="game-modes">
            <div class="mode-card" onclick="startGame('familia')">
                <div class="mode-icon">👨‍👩‍👧‍👦</div>
                <h2>Modo Família</h2>
                <p class="mode-description">Perguntas divertidas e apropriadas para todas as idades. Perfect para reunir a família!</p>
            </div>

            <div class="mode-card" onclick="startGame('casal')">
                <div class="mode-icon">💑</div>
                <h2>Modo Casal</h2>
                <p class="mode-description">Perguntas românticas e desafiadoras para fortalecer a relação.</p>
            </div>

            <div class="mode-card" onclick="startGame('amigos')">
                <div class="mode-icon">👥</div>
                <h2>Modo Amigos</h2>
                <p class="mode-description">Perguntas descontraídas e engraçadas para momentos com os amigos.</p>
            </div>

            <div class="mode-card" onclick="startGame('desafio')">
                <div class="mode-icon">⚡</div>
                <h2>Modo Desafio</h2>
                <p class="mode-description">Perguntas difíceis e situações desafiadoras para testar os limites.</p>
            </div>
        </div>


    <script>                                                                // python?
        function startGame(mode) {
            const modeNames = {
                'familia': 'Modo Família',
                'casal': 'Modo Casal',
                'amigos': 'Modo Amigos',
                'desafio': 'Modo Desafio'
            }; 
            window.location.href = 'questions01.php';
        }
        function continueGame(gameId) {
            alert(`Continuando jogo #${gameId}`);
        }

        function viewResults(gameId) {
            alert(`Visualizando resultados do jogo #${gameId}`);
        }

        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = 'logout.php';
            }
        }

        // Theme management
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
            document.getElementById('theme-icon').textContent = '☀️';
        }

        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            
            body.classList.toggle('light-theme');
            
            if (body.classList.contains('light-theme')) {
                themeIcon.textContent = '☀️';
                localStorage.setItem('theme', 'light');
            } else {
                themeIcon.textContent = '🌙';
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
</body>
</html>