<?php
include 'bancoDados.php';
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nome_completo = $_POST['nome_completo'] ?? '';

    // Validações
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($nome_completo)) {
        $error = 'Por favor, preencha todos os campos.';
    } elseif (strlen($username) < 3) {
        $error = 'O usuário deve ter no mínimo 3 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, insira um email válido.';
    } elseif (strlen($password) < 6) {
        $error = 'A senha deve ter no mínimo 6 caracteres.';
    } elseif ($password !== $confirm_password) {
        $error = 'As senhas não coincidem.';
    } else {
        // Simulação de criação de conta
        $success = 'Conta criada com sucesso! Redirecionando para login...';
        // Em produção, aqui salvaria os dados no banco de dados
        // Depois de alguns segundos, redirecionar para login
        header('Refresh: 2; url=index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Queue - Criar Conta</title>
    <link rel = 'stylesheet' href = 'criar.css'>
</head>

<body>
    <div class="login-container criar-container">
        <div class="logo">
            <h1>Question Queue</h1>
            <p>Crie sua conta agora!</p>
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
                <label for="nome_completo">Nome Completo</label>
                <input 
                    type="text" 
                    id="nome_completo" 
                    name="nome_completo" 
                    class="form-control" 
                    placeholder="Digite seu nome completo"
                    value="<?php echo isset($_POST['nome_completo']) ? htmlspecialchars($_POST['nome_completo']) : ''; ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="Digite seu email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="username">Usuário</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-control" 
                    placeholder="Escolha um usuário"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    required
                >
                <small class="form-hint">Mínimo 3 caracteres</small>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Digite uma senha segura"
                    required
                >
                <small class="form-hint">Mínimo 6 caracteres</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Senha</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    class="form-control" 
                    placeholder="Confirme sua senha"
                    required
                >
            </div>

            <button type="submit" class="btn-login btn-criar">
                CRIAR CONTA
            </button>

            <div class="form-footer">
                <p>Já tem uma conta? <a href="index.php">Faça login aqui</a></p>
            </div>
        </form>
    </div>

    <script>
        // Foco no campo de nome ao carregar a página
        document.getElementById('nome_completo').focus();

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

        // Validação de senhas em tempo real
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== '' && this.value !== passwordInput.value) {
                this.style.borderBottom = '2px solid #e74c3c';
            } else {
                this.style.borderBottom = 'none';
            }
        });
    </script>
</html>