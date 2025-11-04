<?php
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        // Simulação de autenticação
        if ($username === 'admin' && $password === '1234') {
            $success = 'Login realizado com sucesso!';
            header('Location: home.php');
            exit;
        } else {
            $error = 'Usuário ou senha incorretos.';
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
            <h1>Question Queue</h1>
            <p>Sistema de Gerenciamento de Perguntas</p>
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

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Usuário</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control" 
                    placeholder="Digite seu usuário"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
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
            <a href="#" class="link" onclick="createAccount()">Criar conta</a>
        </div>
    </div>

    <script>
        function forgotPassword() {
            alert('Funcionalidade em desenvolvimento!');
        }

        function createAccount() {
            alert('Funcionalidade em desenvolvimento!');
        }

        // Foco no campo de usuário ao carregar a página
        document.getElementById('username').focus();

        // Adicionar efeitos de interação nos campos
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Permitir login com Enter
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>