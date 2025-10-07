<?php
// Game data
$sampleGames = [
    [
        'id' => 1,
        'mode' => 'casal',
        'modeName' => 'Modo Casal',
        'players' => ['Você', 'Maria'],
        'date' => '15 Nov 2024',
        'score' => '8/10',
        'duration' => '25 min'
    ],
    [
        'id' => 2,
        'mode' => 'familia',
        'modeName' => 'Modo Família',
        'players' => ['Você', 'João', 'Ana', 'Pedro'],
        'date' => '14 Nov 2024',
        'score' => '12/15',
        'duration' => '40 min'
    ],
    [
        'id' => 3,
        'mode' => 'amigos',
        'modeName' => 'Modo Amigos',
        'players' => ['Você', 'Carlos', 'Julia'],
        'date' => '12 Nov 2024',
        'score' => '7/10',
        'duration' => '30 min'
    ]
];

// Get username from session or URL
session_start();
$username = $_SESSION['username'] ?? $_GET['user'] ?? 'Player';

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

function loadRecentGames($games) {
    if (empty($games)) {
        return '
            <div class="empty-state">
                <div class="empty-icon">🎮</div>
                <h3>Nenhum jogo recente</h3>
                <p>Comece um novo jogo para ver seu histórico aqui!</p>
            </div>
        ';
    }

    $html = '';
    foreach ($games as $game) {
        $html .= '
            <div class="game-item">
                <div class="game-info">
                    <h3>' . htmlspecialchars($game['modeName']) . '</h3>
                    <p class="game-meta">
                        Jogadores: ' . htmlspecialchars(implode(', ', $game['players'])) . ' | 
                        Data: ' . htmlspecialchars($game['date']) . ' | 
                        Pontuação: ' . htmlspecialchars($game['score']) . ' | 
                        Duração: ' . htmlspecialchars($game['duration']) . '
                    </p>
                </div>
                <div class="game-actions">
                    <button class="btn-action" onclick="continueGame(' . $game['id'] . ')">Continuar</button>
                    <button class="btn-action" onclick="viewResults(' . $game['id'] . ')">Resultados</button>
                </div>
            </div>
        ';
    }
    return $html;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Queue - Dashboard</title>
    <style>
        /* Seus estilos CSS anteriores permanecem iguais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2C2C54 0%, #474787 100%);
            min-height: 100vh;
            transition: all 0.5s ease;
        }

        body.light-theme {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .header {
            background: rgba(71, 71, 135, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.5s ease;
        }

        body.light-theme .header {
            background: rgba(255, 255, 255, 0.95);
        }

        .logo h1 {
            color: #FFFFFF;
            font-size: 24px;
            font-weight: bold;
            transition: all 0.5s ease;
        }

        body.light-theme .logo h1 {
            color: #2C2C54;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-welcome {
            color: #67C8E3;
            font-size: 14px;
            transition: all 0.5s ease;
        }

        body.light-theme .user-welcome {
            color: #667eea;
        }

        .btn-logout {
            background: #34ACE0;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: #67C8E3;
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .game-modes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .mode-card {
            background: #474787;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.5s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .mode-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #34ACE0 0%, #706FD3 100%);
        }

        .mode-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }

        body.light-theme .mode-card {
            background: #FFFFFF;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .mode-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .mode-card h2 {
            color: #FFFFFF;
            font-size: 20px;
            margin-bottom: 10px;
            transition: all 0.5s ease;
        }

        body.light-theme .mode-card h2 {
            color: #2C2C54;
        }

        .mode-description {
            color: #67C8E3;
            font-size: 14px;
            line-height: 1.5;
            transition: all 0.5s ease;
        }

        body.light-theme .mode-description {
            color: #666;
        }

        .recent-games {
            background: #474787;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.5s ease;
            margin-bottom: 30px;
        }

        body.light-theme .recent-games {
            background: #FFFFFF;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-title {
            color: #FFFFFF;
            font-size: 22px;
            font-weight: bold;
            transition: all 0.5s ease;
        }

        body.light-theme .section-title {
            color: #2C2C54;
        }

        .btn-primary {
            background: #34ACE0;
            color: #FFFFFF;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #67C8E3;
            transform: translateY(-2px);
        }

        .games-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .game-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        body.light-theme .game-item {
            background: rgba(44, 44, 84, 0.05);
        }

        .game-item:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.15);
        }

        body.light-theme .game-item:hover {
            background: rgba(44, 44, 84, 0.1);
        }

        .game-info h3 {
            color: #FFFFFF;
            font-size: 16px;
            margin-bottom: 5px;
            transition: all 0.5s ease;
        }

        body.light-theme .game-info h3 {
            color: #2C2C54;
        }

        .game-meta {
            color: #67C8E3;
            font-size: 12px;
            transition: all 0.5s ease;
        }

        body.light-theme .game-meta {
            color: #666;
        }

        .game-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            background: transparent;
            border: 1px solid #34ACE0;
            color: #34ACE0;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            background: #34ACE0;
            color: #FFFFFF;
        }

        .theme-toggle {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 20px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(180deg);
        }

        body.light-theme .theme-toggle {
            background: rgba(44, 44, 84, 0.1);
            color: #2C2C54;
        }

        body.light-theme .theme-toggle:hover {
            background: rgba(44, 44, 84, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #67C8E3;
            transition: all 0.5s ease;
        }

        body.light-theme .empty-state {
            color: #666;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            .container {
                padding: 20px;
            }

            .game-modes {
                grid-template-columns: 1fr;
            }

            .section-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .game-item {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .game-actions {
                align-self: flex-end;
            }
        }
    </style>
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

        <!-- Recent Games -->
        <div class="recent-games">
            <div class="section-header">
                <h2 class="section-title">Jogos Recentes</h2>
                <button class="btn-primary" onclick="showAllGames()">Ver Todos os Jogos</button>
            </div>

            <div class="games-list" id="games-list">
                <?php echo loadRecentGames($sampleGames); ?>
            </div>
        </div>
    </div>

    <script>
        // JavaScript functions that remain client-side
        function startGame(mode) {
            const modeNames = {
                'familia': 'Modo Família',
                'casal': 'Modo Casal',
                'amigos': 'Modo Amigos',
                'desafio': 'Modo Desafio'
            };
            
            alert(`Iniciando ${modeNames[mode]}! 🎮\n\nPrepare-se para perguntas incríveis!`);
        }

        function showAllGames() {
            alert('Abrindo histórico completo de jogos!');
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