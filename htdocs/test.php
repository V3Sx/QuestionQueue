<?php
/**
 * Script de Teste e Diagnóstico do Sistema
 * Acesse: http://localhost/questionQueue-06/test.php
 */

session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste - QuestionQueue</title>
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
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .test-group {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .test-group-title {
            background: #f5f5f5;
            padding: 15px;
            font-weight: bold;
            border-bottom: 1px solid #e0e0e0;
        }
        .test-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .test-item:last-child {
            border-bottom: none;
        }
        .status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
        }
        .status.ok {
            background: #4caf50;
            color: white;
        }
        .status.error {
            background: #f44336;
            color: white;
        }
        .status.warning {
            background: #ff9800;
            color: white;
        }
        .details {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #764ba2;
        }
        .btn-success {
            background: #4caf50;
            color: white;
        }
        .btn-success:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Diagnóstico do Sistema QuestionQueue</h1>
            <p>Verificação de configuração e conectividade</p>
        </div>

        <div class="content">
            <!-- Teste de PHP -->
            <div class="test-group">
                <div class="test-group-title">✅ Ambiente PHP</div>
                <div class="test-item">
                    <span>Versão do PHP</span>
                    <span class="status ok"><?php echo phpversion(); ?></span>
                </div>
                <div class="test-item">
                    <span>Extensão MySQLi/PDO</span>
                    <span class="status <?php echo extension_loaded('pdo_mysql') ? 'ok' : 'error'; ?>">
                        <?php echo extension_loaded('pdo_mysql') ? 'INSTALADA' : 'NÃO INSTALADA'; ?>
                    </span>
                </div>
                <div class="test-item">
                    <span>Suporte a Sessions</span>
                    <span class="status ok">ATIVO</span>
                </div>
            </div>

            <!-- Teste de Banco de Dados -->
            <div class="test-group">
                <div class="test-group-title">💾 Banco de Dados</div>
                <?php
                require_once 'includes_auth.php';
                $db = getDBConnection();
                
                if ($db) {
                    echo '<div class="test-item">';
                    echo '<span>Conexão com MySQL</span>';
                    echo '<span class="status ok">CONECTADO</span>';
                    echo '</div>';
                    
                    // Testar tabelas
                    $tables = ['users', 'games', 'answers'];
                    foreach ($tables as $table) {
                        try {
                            $stmt = $db->query("SHOW TABLES LIKE '$table'");
                            $exists = $stmt->rowCount() > 0;
                            echo '<div class="test-item">';
                            echo "<span>Tabela: $table</span>";
                            echo '<span class="status ' . ($exists ? 'ok' : 'error') . '">';
                            echo $exists ? 'EXISTE' : 'NÃO EXISTE';
                            echo '</span>';
                            echo '</div>';
                        } catch (Exception $e) {
                            echo '<div class="test-item">';
                            echo "<span>Tabela: $table</span>";
                            echo '<span class="status error">ERRO</span>';
                            echo '</div>';
                        }
                    }
                    
                    // Testar usuário de teste
                    try {
                        $stmt = $db->prepare("SELECT id, name, email FROM users LIMIT 1");
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo '<div class="test-item">';
                        echo '<span>Usuários no banco</span>';
                        if ($user) {
                            echo '<span class="status ok">' . ($db->query("SELECT COUNT(*) FROM users")->fetchColumn()) . ' USUÁRIOS</span>';
                        } else {
                            echo '<span class="status warning">NENHUM</span>';
                        }
                        echo '</div>';
                    } catch (Exception $e) {
                        echo '<div class="test-item">';
                        echo '<span>Usuários no banco</span>';
                        echo '<span class="status error">ERRO NA QUERY</span>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="test-item">';
                    echo '<span>Conexão com MySQL</span>';
                    echo '<span class="status error">NÃO CONECTADO</span>';
                    echo '</div>';
                    echo '<div class="test-item">';
                    echo '<span>Detalhes</span>';
                    echo '<div class="details">Verifique: hostname, nome do BD, usuário e senha</div>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Teste de Arquivos -->
            <div class="test-group">
                <div class="test-group-title">📁 Arquivos Necessários</div>
                <?php
                $files = [
                    'index.php' => 'Página de Login',
                    'criar.php' => 'Página de Cadastro',
                    'home.php' => 'Página Principal',
                    'logout.php' => 'Logout',
                    'includes_auth.php' => 'Sistema de Autenticação',
                    'config.php' => 'Configurações',
                ];
                
                foreach ($files as $file => $desc) {
                    $exists = file_exists(__DIR__ . '/' . $file);
                    echo '<div class="test-item">';
                    echo "<span>$file - $desc</span>";
                    echo '<span class="status ' . ($exists ? 'ok' : 'error') . '">';
                    echo $exists ? 'OK' : 'FALTANDO';
                    echo '</span>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Links de Navegação -->
            <div class="buttons">
                <a href="index.php" class="btn btn-primary">🔐 Ir para Login</a>
                <a href="criar.php" class="btn btn-primary">📝 Ir para Cadastro</a>
                <?php if ($db): ?>
                    <a href="home.php" class="btn btn-success">🏠 Ir para Home</a>
                <?php endif; ?>
            </div>

            <!-- Informações de Debug -->
            <div class="test-group" style="margin-top: 30px;">
                <div class="test-group-title">🔧 Configuração</div>
                <div class="test-item">
                    <span>Arquivo de Config</span>
                    <span class="status <?php echo file_exists(__DIR__ . '/config.php') ? 'ok' : 'warning'; ?>">
                        <?php echo file_exists(__DIR__ . '/config.php') ? 'PRESENTE' : 'USANDO PADRÃO'; ?>
                    </span>
                </div>
                <div class="test-item">
                    <span>Diretório Raiz</span>
                    <div class="details"><?php echo __DIR__; ?></div>
                </div>
                <div class="test-item">
                    <span>URL Atual</span>
                    <div class="details"><?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
