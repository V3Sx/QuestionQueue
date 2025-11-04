<?php
session_start();

// Inicializar sessões se não existirem
if (!isset($_SESSION['jogos_recentes'])) {
    $_SESSION['jogos_recentes'] = [];
}

if (!isset($_SESSION['perguntas_respondidas'])) {
    $_SESSION['perguntas_respondidas'] = [];
}

if (!isset($_SESSION['jogo_atual'])) {
    $_SESSION['jogo_atual'] = null;
}

// Banco de perguntas por modo
$banco_perguntas = [
    'familia' => [
        [
            'pergunta' => 'Qual foi a viagem em família mais memorável?',
            'tipo' => 'aberta',
            'categoria' => 'memórias'
        ],
        [
            'pergunta' => 'Qual tradição familiar você mais valoriza?',
            'tipo' => 'aberta', 
            'categoria' => 'valores'
        ],
        [
            'pergunta' => 'Quem é o melhor cozinheiro da família?',
            'tipo' => 'multipla_escolha',
            'opcoes' => ['Mãe', 'Pai', 'Avó/Avô', 'Irmão/Irmã', 'Todos'],
            'categoria' => 'habilidades'
        ]
    ],
    'casal' => [
        [
            'pergunta' => 'Qual foi nosso primeiro encontro mais especial?',
            'tipo' => 'aberta',
            'categoria' => 'memórias'
        ],
        [
            'pergunta' => 'O que mais te faz sorrir no nosso relacionamento?',
            'tipo' => 'aberta',
            'categoria' => 'sentimentos'
        ],
        [
            'pergunta' => 'Qual qualidade você mais admira no parceiro?',
            'tipo' => 'multipla_escolha',
            'opcoes' => ['Paciência', 'Sentido de humor', 'Inteligência', 'Carinho', 'Responsabilidade'],
            'categoria' => 'qualidades'
        ]
    ],
    'amigos' => [
        [
            'pergunta' => 'Qual foi a aventura mais divertida com os amigos?',
            'tipo' => 'aberta',
            'categoria' => 'aventuras'
        ],
        [
            'pergunta' => 'Qual amigo seria seu companheiro ideal em uma ilha deserta?',
            'tipo' => 'aberta',
            'categoria' => 'confiança'
        ],
        [
            'pergunta' => 'O que torna nossa amizade especial?',
            'tipo' => 'multipla_escolha',
            'opcoes' => ['Confiança', 'Diversão', 'Apoio mútuo', 'Experiências compartilhadas', 'Todos acima'],
            'categoria' => 'amizade'
        ]
    ],
    'desafio' => [
        [
            'pergunta' => 'Se você pudesse ter um superpoder, qual seria e por quê?',
            'tipo' => 'aberta',
            'categoria' => 'criatividade'
        ],
        [
            'pergunta' => 'Qual é o maior desafio que você já superou?',
            'tipo' => 'aberta',
            'categoria' => 'superação'
        ],
        [
            'pergunta' => 'Em uma situação de emergência, qual sua primeira reação?',
            'tipo' => 'multipla_escolha',
            'opcoes' => ['Mantém a calma', 'Age rapidamente', 'Pede ajuda', 'Analisa a situação', 'Entra em pânico'],
            'categoria' => 'reação'
        ]
    ]
];

// Funções do jogo
function startGame($mode) {
    $modeNames = [
        'familia' => 'Modo Família',
        'casal' => 'Modo Casal',
        'amigos' => 'Modo Amigos',
        'desafio' => 'Modo Desafio'
    ];
    
    return $modeNames[$mode] ?? 'Modo Desconhecido';
}

function getRandomQuestion($modo, &$perguntas_respondidas) {
    global $banco_perguntas;
    
    if (!isset($banco_perguntas[$modo])) {
        return null;
    }
    
    $perguntas_disponiveis = array_filter($banco_perguntas[$modo], function($index) use ($perguntas_respondidas) {
        return !in_array($index, $perguntas_respondidas);
    }, ARRAY_FILTER_USE_KEY);
    
    if (empty($perguntas_disponiveis)) {
        return null; // Todas as perguntas foram respondidas
    }
    
    $indice_aleatorio = array_rand($perguntas_disponiveis);
    $perguntas_respondidas[] = $indice_aleatorio;
    
    return [
        'indice' => $indice_aleatorio,
        'dados' => $perguntas_disponiveis[$indice_aleatorio]
    ];
}

function calculateScore($respostas_corretas, $tempo_resposta) {
    $pontuacao_base = $respostas_corretas * 100;
    $bonus_tempo = max(0, 30 - $tempo_resposta) * 5; // Bônus por resposta rápida
    return $pontuacao_base + $bonus_tempo;
}

// Processamento das ações do jogo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'start_game':
            $modo = $_POST['mode'] ?? '';
            $jogadores = $_POST['players'] ?? [];
            
            if ($modo && !empty($jogadores)) {
                $_SESSION['jogo_atual'] = [
                    'id' => uniqid(),
                    'modo' => $modo,
                    'jogadores' => $jogadores,
                    'inicio' => date('Y-m-d H:i:s'),
                    'perguntas_respondidas' => [],
                    'respostas' => [],
                    'pontuacao' => 0
                ];
            }
            break;
            
        case 'submit_answer':
            if ($_SESSION['jogo_atual']) {
                $resposta = $_POST['answer'] ?? '';
                $indice_pergunta = $_POST['question_index'] ?? '';
                $tempo_resposta = $_POST['response_time'] ?? 0;
                
                $_SESSION['jogo_atual']['respostas'][] = [
                    'pergunta_indice' => $indice_pergunta,
                    'resposta' => $resposta,
                    'tempo' => $tempo_resposta
                ];
                
                $_SESSION['jogo_atual']['perguntas_respondidas'][] = $indice_pergunta;
                
                // Se todas as perguntas foram respondidas, finaliza o jogo
                if (count($_SESSION['jogo_atual']['respostas']) >= 3) {
                    $jogo_finalizado = $_SESSION['jogo_atual'];
                    $jogo_finalizado['fim'] = date('Y-m-d H:i:s');
                    $jogo_finalizado['duracao'] = strtotime($jogo_finalizado['fim']) - strtotime($jogo_finalizado['inicio']);
                    $jogo_finalizado['pontuacao_final'] = calculateScore(count($jogo_finalizado['respostas']), $jogo_finalizado['duracao']);
                    
                    $_SESSION['jogos_recentes'][] = $jogo_finalizado;
                    $_SESSION['jogo_atual'] = null;
                }
            }
            break;
            
        case 'end_game':
            if ($_SESSION['jogo_atual']) {
                $jogo_finalizado = $_SESSION['jogo_atual'];
                $jogo_finalizado['fim'] = date('Y-m-d H:i:s');
                $jogo_finalizado['duracao'] = strtotime($jogo_finalizado['fim']) - strtotime($jogo_finalizado['inicio']);
                $jogo_finalizado['pontuacao_final'] = calculateScore(count($jogo_finalizado['respostas']), $jogo_finalizado['duracao']);
                
                $_SESSION['jogos_recentes'][] = $jogo_finalizado;
                $_SESSION['jogo_atual'] = null;
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Queue - Jogo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2C2C54 0%, #474787 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .game-container {
            background: #474787;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 600px;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .game-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #34ACE0 0%, #706FD3 100%);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #FFFFFF;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .logo p {
            color: #67C8E3;
            font-size: 14px;
            opacity: 0.9;
        }

        .game-mode {
            text-align: center;
            color: #34ACE0;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 14px;
            background: #F7F7F7;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            color: #333333;
            transition: all 0.3s ease;
            margin-bottom: 10px;
        }

        .form-control:focus {
            outline: none;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(52, 172, 224, 0.2);
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: #34ACE0;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background: #67C8E3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 172, 224, 0.4);
        }

        .btn-secondary {
            padding: 12px 20px;
            background: transparent;
            color: #67C8E3;
            border: 2px solid #67C8E3;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .btn-secondary:hover {
            background: #67C8E3;
            color: #FFFFFF;
        }

        .players-list {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .player-tag {
            background: #34ACE0;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .question-container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .question-category {
            display: inline-block;
            background: #34ACE0;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .question-text {
            font-size: 16px;
            color: #FFFFFF;
            margin-bottom: 20px;
            line-height: 1.5;
            font-weight: 500;
        }

        .answer-input {
            width: 100%;
            padding: 15px;
            background: #F7F7F7;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 14px;
            color: #333333;
            resize: vertical;
            min-height: 100px;
            transition: all 0.3s ease;
        }

        .answer-input:focus {
            outline: none;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(52, 172, 224, 0.2);
            border-color: #34ACE0;
        }

        .options-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .option-label {
            display: flex;
            align-items: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #FFFFFF;
        }

        .option-label:hover {
            border-color: #34ACE0;
            background: rgba(52, 172, 224, 0.2);
            transform: translateX(5px);
        }

        .option-label input {
            margin-right: 12px;
            transform: scale(1.2);
        }

        .game-progress {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .progress-info {
            color: #FFFFFF;
            font-size: 12px;
            font-weight: bold;
        }

        .progress-bar {
            flex-grow: 1;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
            margin: 0 15px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #34ACE0 0%, #67C8E3 100%);
            transition: width 0.3s ease;
            border-radius: 3px;
        }

        .timer {
            color: #67C8E3;
            font-size: 12px;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 10px;
            border-radius: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #FFFFFF;
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: #67C8E3;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: center;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        @media (max-width: 480px) {
            .game-container {
                padding: 30px 25px;
            }
            
            .logo h1 {
                font-size: 24px;
            }
            
            .game-progress {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .progress-bar {
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="game-container">
        <div class="logo">
            <h1>Question Queue</h1>
            <p>Sistema de Gerenciamento de Perguntas</p>
        </div>

        <?php if ($_SESSION['jogo_atual']): ?>
            <!-- Tela do Jogo em Andamento -->
            <?php
            $jogo_atual = $_SESSION['jogo_atual'];
            $pergunta_atual = getRandomQuestion($jogo_atual['modo'], $jogo_atual['perguntas_respondidas']);
            ?>
            
            <div class="game-mode"><?php echo startGame($jogo_atual['modo']); ?></div>

            <div class="players-list">
                <?php foreach ($jogo_atual['jogadores'] as $jogador): ?>
                    <div class="player-tag"><?php echo htmlspecialchars($jogador); ?></div>
                <?php endforeach; ?>
            </div>

            <div class="game-progress">
                <div class="progress-info">
                    Pergunta <?php echo count($jogo_atual['respostas']) + 1; ?> de 3
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo ((count($jogo_atual['respostas']) + 1) / 3) * 100; ?>%"></div>
                </div>
                <div class="timer" id="timer">00:00</div>
            </div>

            <?php if ($pergunta_atual): ?>
                <div class="question-container">
                    <div class="question-category"><?php echo htmlspecialchars($pergunta_atual['dados']['categoria']); ?></div>
                    <div class="question-text"><?php echo htmlspecialchars($pergunta_atual['dados']['pergunta']); ?></div>
                    
                    <form method="POST" class="answer-form">
                        <input type="hidden" name="action" value="submit_answer">
                        <input type="hidden" name="question_index" value="<?php echo $pergunta_atual['indice']; ?>">
                        <input type="hidden" name="response_time" id="response_time" value="0">
                        
                        <?php if ($pergunta_atual['dados']['tipo'] === 'aberta'): ?>
                            <textarea 
                                name="answer" 
                                class="answer-input" 
                                placeholder="Digite sua resposta aqui..."
                                required
                            ></textarea>
                        <?php else: ?>
                            <div class="options-container">
                                <?php foreach ($pergunta_atual['dados']['opcoes'] as $opcao): ?>
                                    <label class="option-label">
                                        <input type="radio" name="answer" value="<?php echo htmlspecialchars($opcao); ?>" required>
                                        <?php echo htmlspecialchars($opcao); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn-primary">Enviar Resposta</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                    🎉 Todas as perguntas foram respondidas!
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="end_game">
                    <button type="submit" class="btn-primary">Ver Resultados Finais</button>
                </form>
            <?php endif; ?>

            <script>
                // Timer
                let startTime = Date.now();
                let timerElement = document.getElementById('timer');
                let responseTimeInput = document.getElementById('response_time');
                
                setInterval(() => {
                    let elapsed = Math.floor((Date.now() - startTime) / 1000);
                    let minutes = Math.floor(elapsed / 60);
                    let seconds = elapsed % 60;
                    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    responseTimeInput.value = elapsed;
                }, 1000);

                // Efeitos de interação
                const inputs = document.querySelectorAll('.answer-input, .option-label');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.style.transform = 'scale(1.02)';
                    });
                    
                    input.addEventListener('blur', function() {
                        this.style.transform = 'scale(1)';
                    });
                });
            </script>

        <?php else: ?>
            <!-- Tela de Início do Jogo -->
            <div class="game-mode">Novo Jogo</div>

            <form method="POST">
                <input type="hidden" name="action" value="start_game">
                
                <div class="form-group">
                    <label for="mode">Modo de Jogo</label>
                    <select name="mode" id="mode" required class="form-control">
                        <option value="">Selecione um modo</option>
                        <option value="familia">👨‍👩‍👧‍👦 Modo Família</option>
                        <option value="casal">💑 Modo Casal</option>
                        <option value="amigos">👥 Modo Amigos</option>
                        <option value="desafio">🎯 Modo Desafio</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jogadores</label>
                    <div id="players-container">
                        <input type="text" name="players[]" placeholder="Nome do Jogador 1" class="form-control" required>
                        <input type="text" name="players[]" placeholder="Nome do Jogador 2" class="form-control" required>
                    </div>
                    <button type="button" onclick="addPlayer()" class="btn-secondary" style="margin-top: 10px;">
                        + Adicionar Jogador
                    </button>
                </div>

                <button type="submit" class="btn-primary">🎮 Iniciar Jogo</button>
            </form>

            <script>
                function addPlayer() {
                    const container = document.getElementById('players-container');
                    const playerCount = container.children.length + 1;
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'players[]';
                    input.placeholder = 'Nome do Jogador ' + playerCount;
                    input.className = 'form-control';
                    input.required = true;
                    container.appendChild(input);
                }

                // Foco no primeiro campo
                document.getElementById('mode').focus();
            </script>
        <?php endif; ?>
    </div>
</body>
</html>