<?php
// Verifica se a classe Auth já foi definida
if (!class_exists('Auth')) {

class Auth {
    private $pdo;
    private $user = null;
    
    public function __construct($pdo = null) {
        if ($pdo === null) {
            // Modo desenvolvimento sem banco: não loga automaticamente para
            // evitar redirecionamentos silenciosos (criar.php precisa permitir cadastro).
            $this->pdo = null;
            return;
        }
        
        $this->pdo = $pdo;
        $this->checkSession();
    }
    
    private function checkSession() {
        if (isset($_SESSION['user_id'])) {
            if ($this->pdo) {
                $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $this->user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $this->user = $_SESSION['user'] ?? ['id' => 1, 'name' => 'User'];
            }
        }
    }
    
    public function isLoggedIn() {
        return $this->user !== null;
    }
    
    public function getUser() {
        return $this->user;
    }
    
    public function login($email, $password) {
        if ($this->pdo) {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $this->user = $user;
                return true;
            }
            return 'Email ou senha inválidos.';
        } else {
            // Modo desenvolvimento sem DB: permitir login automático para testes
            // (mas não forçar login ao construir a classe)
            $_SESSION['user_id'] = 1;
            $_SESSION['user'] = ['id' => 1, 'name' => 'Developer', 'email' => $email];
            $this->user = $_SESSION['user'];
            return true;
        }
    }

    /**
     * Registra um novo usuário no banco.
     * Retorna true em caso de sucesso ou uma mensagem de erro string.
     */
    public function register($name, $email, $password) {
        if (!$this->pdo) {
            return 'Registro não disponível no modo de desenvolvimento.';
        }

        try {
            // Verifica se email já existe
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                return 'Este email já está cadastrado.';
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $hashed]);

            return true;
        } catch (PDOException $e) {
            error_log('Register error: ' . $e->getMessage());
            return 'Erro ao cadastrar usuário. Por favor, tente novamente mais tarde.';
        }
    }
    
    public function logout() {
        session_destroy();
        $this->user = null;
    }
}

} // Fim do if !class_exists('Auth')

// Função de conexão com banco
if (!function_exists('getDBConnection')) {
    function getDBConnection() {
        try {
            // Configurações para XAMPP
            $host = 'localhost';
            $dbname = 'questionqueue';
            $username = 'root';
            $password = '';
            $charset = 'utf8mb4';
            
            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch(PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            return null;
        }
    }
}

// Inicialização da sessão e auth
if (session_status() == PHP_SESSION_NONE) {
    // Configurar opções de sessão ANTES de iniciar
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.gc_maxlifetime', 3600);
    
    session_start();
}

// Inicializa autenticação apenas se não existir
if (!isset($auth)) {
    try {
        $pdo = getDBConnection();
        $auth = new Auth($pdo);
    } catch(Exception $e) {
        $auth = new Auth(); // Fallback sem banco
    }
}
?>