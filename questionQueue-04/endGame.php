<?php
session_start();

// Verifica se há um jogo finalizado
if (isset($_SESSION['jogos_recentes']) && !empty($_SESSION['jogos_recentes'])) {
    $ultimo_jogo = end($_SESSION['jogos_recentes']);
} else {
    // Redireciona para home se não houver jogo finalizado
    header('Location: home.php');
    exit;
}

function formatarTempo($segundos) {
    $minutos = floor($segundos / 60);
    $segundos = $segundos % 60;
    return sprintf("%02d:%02d", $minutos, $segundos);
}

function calcularPorcentagem($acertos, $total) {
    return $total > 0 ? round(($acertos / $total) * 100, 1) : 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fim de Jogo</title>
    <link rel = "stylesheet" href="endGame.css">
</head>
<body>
    <div class="container">
        <div class="trophy">🏆</div>
        <h1>Jogo Finalizado!</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo count($ultimo_jogo['votacoes']); ?>/5</div>
                <div class="stat-label">Perguntas Respondidas</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo formatarTempo($ultimo_jogo['duracao']); ?></div>
                <div class="stat-label">Tempo Total</div>
            </div>
            
            <div class="stat-card highlight">
                <div class="stat-value"><?php echo $ultimo_jogo['pontuacao_final']; ?> pts</div>
                <div class="stat-label">Pontuação Final</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo date('H:i', strtotime($ultimo_jogo['fim'])); ?></div>
                <div class="stat-label">Horário de Término</div>
            </div>
        </div>

        <div class="performance-message">
            <h3>Desempenho</h3>
            <p>
                <?php
                $pontuacao = $ultimo_jogo['pontuacao_final'];
                if ($pontuacao >= 800) {
                    echo "🎉 Excelente! Você foi incrível!";
                } elseif ($pontuacao >= 600) {
                    echo "👍 Muito bom! Continue assim!";
                } elseif ($pontuacao >= 400) {
                    echo "😊 Bom trabalho! Pode melhorar ainda mais!";
                } else {
                    echo "💪 Continue praticando! Você vai melhorar!";
                }
                ?>
            </p>
        </div>

        <div class="buttons">
            <a href="home.php" class="btn btn-secondary">voltar para o início</a>
        </div>
    </div>

    <script>
        // Animação simples para as barras de progresso
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 500);
            });
        });
    </script>
</body>
</html>