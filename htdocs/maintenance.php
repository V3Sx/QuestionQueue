<?php
/**
 * Script de Manutenção e Reset do Banco de Dados
 * Acesse: http://localhost/questionQueue-06/maintenance.php
 * 
 * CUIDADO: Este script permite deletar dados! Use com cautela em produção.
 */

// Segurança: verificar se estamos em localhost
$localhost_only = true;
if ($localhost_only && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== 'localhost') {
    die('❌ Acesso negado. Este script só pode ser executado localmente.');
}

$message = '';
$alert_type = '';
$pdo = null;

// Tentar conectar ao MySQL
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=questionqueue",
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $connected = true;
} catch (PDOException $e) {
    $connected = false;
    $message = '❌ Erro ao conectar: ' . $e->getMessage();
    $alert_type = 'error';
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$connected) {
        $message = '❌ Não foi possível conectar ao banco de dados.';
        $alert_type = 'error';
    } else {
        $action = $_POST['action'] ?? '';
        
        try {
            if ($action === 'clear_users') {
                // Deletar todos os usuários exceto o de teste
                $stmt = $pdo->prepare("DELETE FROM users WHERE email != ?");
                $stmt->execute(['teste@teste.com']);
                $message = '✅ Usuários deletados (exceto teste@teste.com)';
                $alert_type = 'success';
                
            } elseif ($action === 'clear_games') {
                // Deletar todos os jogos
                $pdo->exec("DELETE FROM answers");
                $pdo->exec("DELETE FROM games");
                $message = '✅ Histórico de jogos deletado';
                $alert_type = 'success';
                
            } elseif ($action === 'reset_database') {
                // Reset completo
                $pdo->exec("DROP TABLE IF EXISTS answers");
                $pdo->exec("DROP TABLE IF EXISTS games");
                $pdo->exec("DROP TABLE IF EXISTS users");
                
                // Recriar tabelas
                require_once 'database.sql';
                
                $message = '✅ Banco de dados resetado com sucesso';
                $alert_type = 'success';
                
            } elseif ($action === 'recreate_database') {
                // Recriar banco completamente
                $pdo->exec("DROP DATABASE IF EXISTS questionqueue");
                $pdo->exec("CREATE DATABASE questionqueue CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                $message = '✅ Banco de dados recriado';
                $alert_type = 'success';
                
            } elseif ($action === 'insert_test_user') {
                // Inserir usuário de teste
                $email = 'teste@teste.com';
                $password = password_hash('123456', PASSWORD_DEFAULT);
                
                // Checar se já existe
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() > 0) {
                    $message = '⚠️ Usuário de teste já existe';
                    $alert_type = 'warning';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                    $stmt->execute(['Usuário Teste', $email, $password]);
                    $message = '✅ Usuário de teste criado';
                    $alert_type = 'success';
                }
            }
        } catch (Exception $e) {
            $message = '❌ Erro: ' . $e->getMessage();
            $alert_type = 'error';
        }
    }
}

// Obter estatísticas
$stats = [
    'users' => 0,
    'games' => 0,
    'answers' => 0
];

if ($connected && $pdo) {
    try {
        $stats['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['games'] = $pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();
        $stats['answers'] = $pdo->query("SELECT COUNT(*) FROM answers")->fetchColumn();
    } catch (Exception $e) {
        // Ignorar erros
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção - QuestionQueue</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alert.error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }
        .alert.success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }
        .alert.warning {
            background: #fff3e0;
            color: #e65100;
            border-left: 4px solid #e65100;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-card .label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .btn {
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
        .btn-danger {
            background: #f44336;
            color: white;
        }
        .btn-danger:hover {
            background: #da190b;
            transform: translateY(-2px);
        }
        .btn-warning {
            background: #ff9800;
            color: white;
        }
        .btn-warning:hover {
            background: #e68900;
            transform: translateY(-2px);
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #1976d2;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .info-box strong {
            color: #1976d2;
        }
        .link-group {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }
        .link-group a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .link-group a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Manutenção - QuestionQueue</h1>
            <p>Ferramentas para gerenciar o banco de dados</p>
        </div>

        <div class="content">
            <?php if ($message): ?>
                <div class="alert <?php echo $alert_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($connected): ?>
                <!-- Estatísticas -->
                <div class="section">
                    <div class="section-title">📊 Estatísticas Atuais</div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="number"><?php echo $stats['users']; ?></div>
                            <div class="label">Usuários Cadastrados</div>
                        </div>
                        <div class="stat-card">
                            <div class="number"><?php echo $stats['games']; ?></div>
                            <div class="label">Jogos Realizados</div>
                        </div>
                        <div class="stat-card">
                            <div class="number"><?php echo $stats['answers']; ?></div>
                            <div class="label">Respostas Registradas</div>
                        </div>
                    </div>
                </div>

                <!-- Ações de Manutenção -->
                <div class="section">
                    <div class="section-title">🛠️ Operações de Manutenção</div>
                    
                    <div class="info-box">
                        <strong>ℹ️ Informação:</strong> Estas operações permitem limpar dados do banco de dados.
                    </div>

                    <div class="action-buttons">
                        <form method="POST" style="grid-column: 1;">
                            <input type="hidden" name="action" value="insert_test_user">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Adicionar usuário de teste?')">
                                ➕ Adicionar Teste
                            </button>
                        </form>

                        <form method="POST" style="grid-column: 2;">
                            <input type="hidden" name="action" value="clear_users">
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Deletar usuários (exceto teste)? ⚠️')">
                                🗑️ Limpar Usuários
                            </button>
                        </form>

                        <form method="POST" style="grid-column: 1;">
                            <input type="hidden" name="action" value="clear_games">
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Deletar histórico de jogos? ⚠️')">
                                🗑️ Limpar Jogos
                            </button>
                        </form>

                        <form method="POST" style="grid-column: 2;">
                            <input type="hidden" name="action" value="reset_database">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('RESETAR BANCO? Todos os dados serão perdidos! ❌')">
                                ⚠️ Reset Total
                            </button>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert error">
                    ❌ Erro de Conexão
                </div>
                <div class="info-box">
                    <strong>Problema:</strong> Não foi possível conectar ao banco de dados.
                    <br><br>
                    <strong>Solução:</strong> 
                    <ol style="margin-left: 20px; margin-top: 10px;">
                        <li>Inicie o MySQL no XAMPP</li>
                        <li>Acesse http://localhost/questionQueue-06/setup_database.php</li>
                        <li>Crie o banco de dados</li>
                    </ol>
                </div>
            <?php endif; ?>

            <!-- Links Rápidos -->
            <div class="link-group">
                <a href="index.php">🔐 Login</a>
                <a href="test.php">🔍 Diagnóstico</a>
                <a href="setup_database.php">⚙️ Setup</a>
                <a href="http://localhost/phpmyadmin/">💾 phpMyAdmin</a>
            </div>
        </div>
    </div>
</body>
</html>
