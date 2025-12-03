<?php
require_once 'includes_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Se já estiver logado, redireciona para home
if ($auth->isLoggedIn()) {
    header("Location: home.php");
    exit();
}

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

$error = '';
$success = '';

// PROCESSAR FORMULÁRIO DE LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Validar CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Token de segurança inválido.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = 'Por favor, preencha todos os campos.';
        } else {
            $result = $auth->login($email, $password);
            
            if ($result === true) {
                $success = 'Login realizado com sucesso! Redirecionando...';
                header('Location: home.php');
                exit();
            } else {
                $error = $result;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Queue - Login</title>
    <link rel = 'stylesheet' href = 'indexStyle.css'>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <h1>Bem-Vindo(a) ao Question Queue</h1>
            <p>Perguntas para passar o tempo ;)</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="loginForm">
            <input type="hidden" name="login" value="1">
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="seu@email.com"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Digite sua senha"
                    required
                >
            </div>

            <button type="submit" class="btn-login">
                ENTRAR
            </button>
        </form>

        <div class="links">
            <a href="#" class="link" onclick="forgotPassword()">Esqueci minha senha</a>
            <a href="criar.php" class="link">Criar conta</a>
        </div>
    </div>

    <script>

        // Permitir login com Enter
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>