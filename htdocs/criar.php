<?php
require_once 'includes_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        // Fallback if random_bytes not available
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

if ($auth->isLoggedIn()) {
    header("Location: home.php");
    exit();
}

$error = '';
$success = '';
$name = '';
$email = '';

// PROCESSAR FORMULÁRIO DE CADASTRO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    // Validar CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Token de segurança inválido.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validações
        if (empty($name) || empty($email) || empty($password)) {
            $error = 'Todos os campos são obrigatórios.';
        } elseif ($password !== $confirm_password) {
            $error = 'As senhas não coincidem.';
        } elseif (strlen($password) < 6) {
            $error = 'A senha deve ter pelo menos 6 caracteres.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email inválido.';
        } else {
            try {
                // Registrar novo usuário
                $result = $auth->register($name, $email, $password);
                
                if ($result === true) {
                    $success = 'Cadastro realizado com sucesso! Redirecionando...';
                    
                    // Limpar dados do formulário
                    $name = '';
                    $email = '';
                    
                    // Fazer login automático após cadastro
                    $auth->login($email, $password);
                } else {
                    $error = $result;
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
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
    <title>QuestionQueue - Cadastro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="criar.css">
</head>

<body>
    <div class="login-container criar-container auth-forms">
        <div class="logo forms-header">
            <h1>Bem-vindo(a) ao QuestionQueue</h1>
            <p>crie uma nova aqui</p>
        </div>

        <?php if ($success): ?>
            <div class="success-celebration">
                <i class="fas fa-party-horn"></i>
                <h3>🎉 Parabéns! 🎉</h3>
                <p>Conta criada com sucesso!</p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="tab-content" id="signup">
            <form method="POST" id="signupForm">
                <input type="hidden" name="signup" value="1">
                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-group">
                    <label for="signupName"><i class="fas fa-user"></i> Nome de usuário</label>
                    <input class="form-control" type="text" id="signupName" name="name"
                        placeholder="Seu nome de usuário"
                        value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="signupEmail"><i class="fas fa-envelope"></i> Email</label>
                    <input class="form-control" type="email" id="signupEmail" name="email" placeholder="seu@email.com"
                        value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="form-group password-toggle">
                    <label for="signupPassword"><i class="fas fa-lock"></i> Senha</label>
                    <input class="form-control" type="password" id="signupPassword" name="password"
                        placeholder="Crie uma senha forte" required>
                    <i class="fas fa-eye" id="toggleSignupPassword"></i>
                </div>

                <div class="form-group password-toggle">
                    <label for="signupConfirmPassword"><i class="fas fa-lock"></i> Confirmar senha</label>
                    <input class="form-control" type="password" id="signupConfirmPassword" name="confirm_password"
                        placeholder="Digite sua senha novamente" required>
                    <i class="fas fa-eye" id="toggleSignupConfirmPassword"></i>
                </div>

                <button type="submit" class="btn-login btn-criar" id="signupBtn">
                    <i class="fas fa-user-plus"></i> Criar Minha Conta
                </button>
            </form>

            <div class="auth-footer">
                Ao continuar, você concorda com os <a href="#">Termos de Uso</a> e a <a href="#">Política de
                    Privacidade</a> do QuestionQueue.
            </div>
        </div>
    </div>

    <script>
        // Toggle senha
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle password visibility
            const togglePassword = document.getElementById('toggleSignupPassword');
            const passwordField = document.getElementById('signupPassword');

            const toggleConfirmPassword = document.getElementById('toggleSignupConfirmPassword');
            const confirmPasswordField = document.getElementById('signupConfirmPassword');

            togglePassword.addEventListener('click', function () {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            toggleConfirmPassword.addEventListener('click', function () {
                const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordField.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            // Processar sucesso de cadastro automaticamente
            processSignupSuccess();
        });

        // Validação do formulário de cadastro
        document.getElementById('signupForm').addEventListener('submit', function (e) {
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('signupConfirmPassword').value;
            const signupBtn = document.getElementById('signupBtn');

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('❌ As senhas não coincidem!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('❌ A senha deve ter pelo menos 6 caracteres!');
                return false;
            }
        });

        // Processar sucesso de cadastro
        function processSignupSuccess() {
            if (document.querySelector('.alert-success')) {
                console.log('Cadastro realizado com sucesso! Redirecionando...');
                
                // Redirecionar para home.php após 2 segundos
                setTimeout(() => {
                    window.location.href = 'home.php';
                }, 2000);
            }
        }
    </script>
</body>
</html>