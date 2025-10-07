<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Queue - Login</title>
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

        .login-container {
            background: #474787;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
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
            font-size: 12px;
            opacity: 0.9;
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
        }

        .form-control:focus {
            outline: none;
            background: #FFFFFF;
            box-shadow: 0 0 0 3px rgba(52, 172, 224, 0.2);
        }

        .btn-login {
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

        .btn-login:hover {
            background: #67C8E3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 172, 224, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .link {
            color: #67C8E3;
            font-size: 12px;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .link:hover {
            color: #FFFFFF;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            text-align: center;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .alert-warning {
            background: rgba(241, 196, 15, 0.1);
            color: #f1c40f;
            border: 1px solid rgba(241, 196, 15, 0.3);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
            }
            
            .logo h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Question Queue</h1>
            <p>Sistema de Gerenciamento de Perguntas</p>
        </div>

        <?php
        // Simulação de processamento do login
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validação básica
            if (empty($username) || empty($password)) {
                $error = 'Por favor, preencha todos os campos.';
            } else {
                // Simulação de autenticação
                if ($username === 'admin' && $password === '1234') {
                    $success = 'Login realizado com sucesso!';
                    // Aqui você redirecionaria para a página principal
                    // header('Location: dashboard.php');
                    // exit;
                } else {
                    $error = 'Usuário ou senha incorretos.';
                }
            }
        }
        ?>

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