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

// Use explicit request method check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Requisição inválida (verificação de segurança falhou).';
    } else {
        if (isset($_POST['login'])) {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            $result = $auth->login($email, $password);
            if ($result === true) {
                // Prevent session fixation
                session_regenerate_id(true);
                header("Location: home.php");
                exit();
            } else {
                $error = $result;
            }
        } elseif (isset($_POST['signup'])) {
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            // Server-side validation
            if (empty($name)) {
                $error = 'Nome de usuário é obrigatório.';
            } elseif (empty($email)) {
                $error = 'Email é obrigatório.';
            } elseif ($password !== $confirm) {
                $error = 'As senhas não coincidem.';
            } elseif (strlen($password) < 6) {
                $error = 'A senha deve ter pelo menos 6 caracteres.';
            } else {
                // Tentar fazer o cadastro
                // Usa o método register() implementado em includes_auth.php
                $result = $auth->register($name, $email, $password);
                
                
                if ($result === true) {
                    $success = "🎉 Cadastro realizado com sucesso! Faça login para continuar.";
                    $name = ''; // Limpar apenas o nome
                    // Manter o email para facilitar o login
                } else {
                    $error = $result;
                }
            }
        }
    }
}

// Debug: Verificar se há mensagens
error_log("Success message: " . $success);
error_log("Error message: " . $error);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuestionQueue - Login e Cadastro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="criar.css">
</head>
<body>
        <div class="login-container criar-container auth-forms">
            <div class="logo forms-header">
                <h1>Bem-vindo(a) ao QUestionQueue</h1>
                <p>crie uma nova aqui</p>
            </div>
            
            <?php if($success): ?>
                <div class="success-celebration">
                    <i class="fas fa-party-horn"></i>
                    <h3>🎉 Parabéns! 🎉</h3>
                    <p>Conta criada com sucesso!</p>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            
            
            <div class="tab-content active" id="login">
                <form method="POST" id="loginForm">
                    <input type="hidden" name="login" value="1">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div class="form-group">
                        <label for="loginEmail"><i class="fas fa-envelope"></i> Email</label>
                        <input class="form-control" type="email" id="loginEmail" name="email" placeholder="seu@email.com" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    
                    <div class="form-group password-toggle">
                        <label for="loginPassword"><i class="fas fa-lock"></i> Senha</label>
                        <input class="form-control" type="password" id="loginPassword" name="password" placeholder="Sua senha" required>
                        <i class="fas fa-eye" id="toggleLoginPassword"></i>
                    </div>
                    
                    <button type="submit" class="btn-login" id="loginBtn">
                        <i class="fas fa-sign-in-alt"></i> Entrar na Comunidade
                    </button>
                </form>
                
                
                <div class="auth-footer">
                    Ao continuar, você concorda com os <a href="#">Termos de Uso</a> e a <a href="#">Política de Privacidade</a> do HQ Verso.
                </div>
            </div>
            
            <div class="tab-content" id="signup">
                <form method="POST" id="signupForm">
                    <input type="hidden" name="signup" value="1">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div class="form-group">
                        <label for="signupName"><i class="fas fa-user"></i> Nome de usuário</label>
                        <input class="form-control" type="text" id="signupName" name="name" placeholder="Seu nome de usuário" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="signupEmail"><i class="fas fa-envelope"></i> Email</label>
                        <input class="form-control" type="email" id="signupEmail" name="email" placeholder="seu@email.com" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    
                    <div class="form-group password-toggle">
                        <label for="signupPassword"><i class="fas fa-lock"></i> Senha</label>
                        <input class="form-control" type="password" id="signupPassword" name="password" placeholder="Crie uma senha forte" required>
                        <i class="fas fa-eye" id="toggleSignupPassword"></i>
                    </div>
                    
                    <div class="form-group password-toggle">
                        <label for="signupConfirmPassword"><i class="fas fa-lock"></i> Confirmar senha</label>
                        <input class="form-control" type="password" id="signupConfirmPassword" name="confirm_password" placeholder="Digite sua senha novamente" required>
                        <i class="fas fa-eye" id="toggleSignupConfirmPassword"></i>
                    </div>
                    
                    <button type="submit" class="btn-login btn-criar" id="signupBtn">
                        <i class="fas fa-user-plus"></i> Criar Minha Conta
                    </button>
                </form>
                
                <div class="separator">ou cadastre-se com</div>
                
                <div class="social-login">
                    <div class="social-btn facebook" title="Cadastrar com Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div class="social-btn google" title="Cadastrar com Google">
                        <i class="fab fa-google"></i>
                    </div>
                    <div class="social-btn twitter" title="Cadastrar com Twitter">
                        <i class="fab fa-twitter"></i>
                    </div>
                </div>
                
                <div class="auth-footer">
                    Ao continuar, você concorda com os <a href="#">Termos de Uso</a> e a <a href="#">Política de Privacidade</a> do HQ Verso.
                </div>
            </div>
        </div>
    </div>

    <script>
        // Criar partículas de fundo
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 15;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const size = Math.random() * 6 + 2;
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                const delay = Math.random() * 5;
                const duration = Math.random() * 10 + 10;
                
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                particle.style.animationDelay = `${delay}s`;
                particle.style.animationDuration = `${duration}s`;
                
                particlesContainer.appendChild(particle);
            }
        }

        // Sistema de abas
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                const tabName = tab.getAttribute('data-tab');
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(tabName).classList.add('active');
            });
        });
        
        // Toggle de senha
        document.getElementById('toggleLoginPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('loginPassword');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        document.getElementById('toggleSignupPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('signupPassword');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        document.getElementById('toggleSignupConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('signupConfirmPassword');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
        
        // Validação do formulário de cadastro
        document.getElementById('signupForm').addEventListener('submit', function(e) {
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
            
            signupBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Criando conta...';
            signupBtn.classList.add('loading');
        });
        
        // Loading no formulário de login
        document.getElementById('loginForm').addEventListener('submit', function() {
            const loginBtn = document.getElementById('loginBtn');
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Entrando...';
            loginBtn.classList.add('loading');
        });
        
        // Processar sucesso de cadastro
        function processSignupSuccess() {
            if(document.querySelector('.alert-success')) {
                // Preencher o email no formulário de login
                const signupEmail = document.getElementById('signupEmail').value;
                if(signupEmail) {
                    document.getElementById('loginEmail').value = signupEmail;
                }
                
                // Mudar para a aba de login automaticamente após 2 segundos
                setTimeout(() => {
                    document.querySelector('[data-tab="login"]').click();
                    
                    // Adicionar foco no campo de email do login
                    setTimeout(() => {
                        document.getElementById('loginEmail').focus();
                    }, 500);
                }, 2000);
            }
        }
        
        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            processSignupSuccess();
        });
    </script>
</body>
</html>