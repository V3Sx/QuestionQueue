<?php
/**
 * Script de Instalação - Cria o banco de dados automaticamente
 * Acesse: http://localhost/questionQueue-06/setup_database.php
 */

$host = 'localhost';
$user = 'root';
$password = '';

// Tentar conectar ao MySQL
try {
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connected = true;
    $message = '';
} catch (PDOException $e) {
    $connected = false;
    $message = 'Erro ao conectar com MySQL: ' . $e->getMessage();
}

$success = false;

if ($connected && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_db'])) {
    try {
        // Criar banco de dados
        $pdo->exec("CREATE DATABASE IF NOT EXISTS questionqueue CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Selecionar banco
        $pdo->exec("USE questionqueue");
        
        // Criar tabela de usuários
        $sql_users = "CREATE TABLE IF NOT EXISTS users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $pdo->exec($sql_users);
        
        // Criar tabela de jogos
        $sql_games = "CREATE TABLE IF NOT EXISTS games (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            mode VARCHAR(50) NOT NULL,
            score INT DEFAULT 0,
            duration INT DEFAULT 0,
            started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            finished_at TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $pdo->exec($sql_games);
        
        // Criar tabela de respostas
        $sql_answers = "CREATE TABLE IF NOT EXISTS answers (
            id INT PRIMARY KEY AUTO_INCREMENT,
            game_id INT NOT NULL,
            question_number INT NOT NULL,
            answer TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $pdo->exec($sql_answers);
        
        // Criar índices
        $pdo->exec("CREATE INDEX idx_users_email ON users(email)");
        $pdo->exec("CREATE INDEX idx_games_user_id ON games(user_id)");
        $pdo->exec("CREATE INDEX idx_games_created_at ON games(started_at)");
        $pdo->exec("CREATE INDEX idx_answers_game_id ON answers(game_id)");
        
        // Inserir usuário de teste
        // Senha: 123456 (hash bcrypt)
        $teste_password = password_hash('123456', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, password) VALUES ('Teste', 'teste@teste.com', '$teste_password')");
        
        $success = true;
        $message = '✅ Banco de dados criado com sucesso!';
        
    } catch (PDOException $e) {
        $success = false;
        $message = '❌ Erro ao criar banco: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração - QuestionQueue</title>
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
            max-width: 600px;
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
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
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
        .alert.info {
            background: #e3f2fd;
            color: #1565c0;
            border-left: 4px solid #1565c0;
        }
        .info-box {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
        }
        .info-box strong {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }
        .info-box code {
            background: #fff;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
            color: #d32f2f;
        }
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        .status-list {
            list-style: none;
            margin: 20px 0;
        }
        .status-list li {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            background: #f5f5f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-list li.ok {
            background: #e8f5e9;
        }
        .status-list li.error {
            background: #ffebee;
        }
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge.ok {
            background: #4caf50;
            color: white;
        }
        .badge.error {
            background: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Configuração QuestionQueue</h1>
            <p>Instalação do Banco de Dados</p>
        </div>

        <div class="content">
            <?php if (!$connected): ?>
                <div class="alert error">
                    🔴 Erro de Conexão
                </div>
                <div class="info-box">
                    <strong>Problema:</strong>
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <div class="info-box">
                    <strong>Como resolver:</strong>
                    <ol style="padding-left: 20px; margin-top: 10px;">
                        <li>Abra o XAMPP Control Panel</li>
                        <li>Clique em "Start" ao lado do Apache</li>
                        <li>Clique em "Start" ao lado do MySQL</li>
                        <li>Recarregue esta página</li>
                    </ol>
                </div>
            <?php else: ?>
                <?php if ($success): ?>
                    <div class="alert success">
                        ✅ Banco de dados criado com sucesso!
                    </div>
                    <div class="info-box">
                        <strong>Credenciais de Teste:</strong>
                        Email: <code>teste@teste.com</code><br>
                        Senha: <code>123456</code>
                    </div>
                    <div class="button-group">
                        <a href="index.php" class="btn btn-primary">🔐 Ir para Login</a>
                        <a href="test.php" class="btn btn-secondary">🔍 Ver Diagnóstico</a>
                    </div>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <div class="alert error">
                        ❌ Erro ao processar
                    </div>
                    <div class="info-box">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <div class="button-group">
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="create_db" value="1" class="btn btn-primary">
                                🔄 Tentar Novamente
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert info">
                        🔵 MySQL conectado com sucesso!
                    </div>
                    
                    <div class="info-box">
                        <strong>Status de Verificação:</strong>
                        <ul class="status-list">
                            <?php
                            try {
                                $result = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'questionqueue'");
                                $db_exists = $result->rowCount() > 0;
                            } catch (Exception $e) {
                                $db_exists = false;
                            }
                            ?>
                            <li <?php echo $db_exists ? 'class="ok"' : 'class="error"'; ?>>
                                <span>Banco "questionqueue"</span>
                                <span class="badge <?php echo $db_exists ? 'ok' : 'error'; ?>">
                                    <?php echo $db_exists ? '✓ Existe' : '✗ Não existe'; ?>
                                </span>
                            </li>
                            <li>
                                <span>MySQL Version</span>
                                <span><?php 
                                try {
                                    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
                                    echo htmlspecialchars($version);
                                } catch (Exception $e) {
                                    echo 'Desconhecida';
                                }
                                ?></span>
                            </li>
                        </ul>
                    </div>

                    <div class="info-box">
                        <strong>O que será criado:</strong>
                        <ul style="padding-left: 20px; margin-top: 10px;">
                            <li>✅ Banco de dados: <code>questionqueue</code></li>
                            <li>✅ Tabela: <code>users</code> (usuários)</li>
                            <li>✅ Tabela: <code>games</code> (histórico de jogos)</li>
                            <li>✅ Tabela: <code>answers</code> (respostas)</li>
                            <li>✅ Usuário de teste: <code>teste@teste.com</code> / <code>123456</code></li>
                        </ul>
                    </div>

                    <div class="button-group">
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="create_db" value="1" class="btn btn-primary">
                                ✨ Criar Banco de Dados
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; color: #666; font-size: 12px;">
                <p>QuestionQueue © 2025</p>
            </div>
        </div>
    </div>
</body>
</html>
