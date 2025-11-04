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

// Banco de perguntas por modo - Foco em "Quem seria o mais provável"
$banco_perguntas = [
    'familia' => [
        [
            'pergunta' => 'Quem seria o mais provável de esquecer um aniversário importante?',
            'tipo' => 'votacao',
            'categoria' => 'esquecimento'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de fazer todos rirem durante uma reunião familiar?',
            'tipo' => 'votacao',
            'categoria' => 'humor'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de cozinhar um jantar surpresa para a família?',
            'tipo' => 'votacao',
            'categoria' => 'culinária'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de se perder em uma viagem?',
            'tipo' => 'votacao',
            'categoria' => 'orientação'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de herdar o senso de humor do avô?',
            'tipo' => 'votacao',
            'categoria' => 'herança'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de organizar todas as fotos de família?',
            'tipo' => 'votacao',
            'categoria' => 'organização'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de contar histórias embaraçosas nas reuniões?',
            'tipo' => 'votacao',
            'categoria' => 'conflito'
        ]
    ],
    'casal' => [
        [
            'pergunta' => 'Quem seria o mais provável de esquecer a data do aniversário de casamento?',
            'tipo' => 'votacao',
            'categoria' => 'memória'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de planejar uma surpresa romântica?',
            'tipo' => 'votacao',
            'categoria' => 'romance'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de roncar alto durante a noite?',
            'tipo' => 'votacao',
            'categoria' => 'hábitos'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de escolher o filme para assistir juntos?',
            'tipo' => 'votacao',
            'categoria' => 'decisões'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de acordar primeiro nos finais de semana?',
            'tipo' => 'votacao',
            'categoria' => 'rotina'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de lembrar todos os detalhes do primeiro encontro?',
            'tipo' => 'votacao',
            'categoria' => 'sentimental'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de iniciar uma guerra de travesseiros?',
            'tipo' => 'votacao',
            'categoria' => 'brincadeira'
        ]
    ],
    'amigos' => [
        [
            'pergunta' => 'Quem seria o mais provável de chegar atrasado em todos os compromissos?',
            'tipo' => 'votacao',
            'categoria' => 'pontualidade'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de esquecer a carteira quando saímos?',
            'tipo' => 'votacao',
            'categoria' => 'esquecimento'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de fazer amizade com estranhos em uma festa?',
            'tipo' => 'votacao',
            'categoria' => 'social'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de ter a ideia mais maluca para uma aventura?',
            'tipo' => 'votacao',
            'categoria' => 'criatividade'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de lembrar de todos os apelidos engraçados?',
            'tipo' => 'votacao',
            'categoria' => 'memória'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de ser o motorista da rodada?',
            'tipo' => 'votacao',
            'categoria' => 'responsabilidade'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de contar o segredo que juramos guardar?',
            'tipo' => 'votacao',
            'categoria' => 'confiança'
        ]
    ],
    'desafio' => [
        [
            'pergunta' => 'Quem seria o mais provável de sobreviver em uma ilha deserta?',
            'tipo' => 'votacao',
            'categoria' => 'sobrevivência'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de ganhar um reality show?',
            'tipo' => 'votacao',
            'categoria' => 'competição'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de inventar uma máquina do tempo?',
            'tipo' => 'votacao',
            'categoria' => 'invenção'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de se tornar famoso da noite para o dia?',
            'tipo' => 'votacao',
            'categoria' => 'fama'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de resolver um mistério policial?',
            'tipo' => 'votacao',
            'categoria' => 'detetive'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de vencer uma maratona de dança?',
            'tipo' => 'votacao',
            'categoria' => 'resistência'
        ],
        [
            'pergunta' => 'Quem seria o mais provável de conseguir um empréstimo com um alienígena?',
            'tipo' => 'votacao',
            'categoria' => 'persuasão'
        ]
    ]
];

// Funções do jogo
function startGame($mode)
{
    $modeNames = [
        'familia' => 'Modo Família',
        'casal' => 'Modo Casal',
        'amigos' => 'Modo Amigos',
        'desafio' => 'Modo Desafio'
    ];

    return $modeNames[$mode] ?? 'Modo Desconhecido';
}

function getRandomQuestion($modo, &$perguntas_respondidas)
{
    global $banco_perguntas;

    if (!isset($banco_perguntas[$modo])) {
        return null;
    }

    $perguntas_disponiveis = array_filter($banco_perguntas[$modo], function ($index) use ($perguntas_respondidas) {
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

function calculateScore($respostas_corretas, $tempo_resposta)
{
    $pontuacao_base = $respostas_corretas * 100;
    $bonus_tempo = max(0, 30 - $tempo_resposta) * 5;
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
                    'pontuacao' => 0,
                    'votacoes' => []
                ];
            }
            break;

        case 'submit_vote':
            if ($_SESSION['jogo_atual']) {
                $jogador_votado = $_POST['player_vote'] ?? '';
                $indice_pergunta = $_POST['question_index'] ?? '';
                $tempo_resposta = $_POST['response_time'] ?? 0;

                // Registra a votação
                $_SESSION['jogo_atual']['votacoes'][] = [
                    'pergunta_indice' => $indice_pergunta,
                    'jogador_votado' => $jogador_votado,
                    'tempo' => $tempo_resposta
                ];

                $_SESSION['jogo_atual']['perguntas_respondidas'][] = $indice_pergunta;

                // Se 5 perguntas foram respondidas, finaliza o jogo
                if (count($_SESSION['jogo_atual']['votacoes']) >= 5) {
                    $jogo_finalizado = $_SESSION['jogo_atual'];
                    $jogo_finalizado['fim'] = date('Y-m-d H:i:s');
                    $jogo_finalizado['duracao'] = strtotime($jogo_finalizado['fim']) - strtotime($jogo_finalizado['inicio']);
                    $jogo_finalizado['pontuacao_final'] = calculateScore(count($jogo_finalizado['votacoes']), $jogo_finalizado['duracao']);

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
                $jogo_finalizado['pontuacao_final'] = calculateScore(count($jogo_finalizado['votacoes']), $jogo_finalizado['duracao']);

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
    <link rel='stylesheet' href='styleQuestion.css'>
</head>

<body>
    <div class="game-container">
        <div class="logo">
            <h1>Question Queue</h1>
            <p>Quem Seria o Mais Provável?</p>
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
                    Pergunta <?php
                    if (isset($jogo_atual['votacoes']) && is_array($jogo_atual['votacoes'])) {
                        echo count($jogo_atual['votacoes']);
                    } else {
                        echo 0;
                    }
                    ?> de 5
                </div>
                <div class="progress-bar">
                    <div class="progress-fill"
                        style="width: <?php echo ((count($jogo_atual['votacoes']) + 1) / 5) * 100; ?>%"></div>
                </div>
                <div class="timer" id="timer">00:00</div>
            </div>

            <?php if ($pergunta_atual): ?>
                <div class="question-container">
                    <div class="question-category"><?php echo htmlspecialchars($pergunta_atual['dados']['categoria']); ?></div>
                    <div class="question-text">"<?php echo htmlspecialchars($pergunta_atual['dados']['pergunta']); ?>"</div>

                    <form method="POST">
                        <input type="hidden" name="action" value="submit_vote">
                        <input type="hidden" name="question_index" value="<?php echo $pergunta_atual['indice']; ?>">
                        <input type="hidden" name="response_time" id="response_time" value="0">

                        <div class="voting-options">
                            <?php foreach ($jogo_atual['jogadores'] as $jogador): ?>
                                <label class="vote-option">
                                    <input type="radio" name="player_vote" value="<?php echo htmlspecialchars($jogador); ?>"
                                        required>
                                    <?php echo htmlspecialchars($jogador); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn-primary">🗳️ Votar</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                    🎉 Todas as 5 perguntas foram respondidas!
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="end_game">
                    <button type="submit" class="btn-primary">📊 Ver Resultados Finais</button>
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
            </script>

        <?php else: ?>
            <!-- Tela de Início do Jogo -->
            <div class="game-mode">Novo Jogo - Quem Seria o Mais Provável?</div>

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
                    <label>Jogadores (mínimo 2)</label>
                    <div id="players-container">
                        <input type="text" name="players[]" placeholder="Nome do Jogador 1" class="form-control" required>
                        <input type="text" name="players[]" placeholder="Nome do Jogador 2" class="form-control" required>
                    </div>
                    <button type="button" onclick="addPlayer()" class="btn-secondary">
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

                document.getElementById('mode').focus();
            </script>
        <?php endif; ?>
    </div>
</body>

</html>