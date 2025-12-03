# 🚀 Guia de Instalação - QuestionQueue + XAMPP

## 📋 Pré-requisitos

- **XAMPP** instalado (com Apache, MySQL e PHP)
- Navegador moderno
- Windows, macOS ou Linux

## 🎯 Passos de Instalação

### 1️⃣ Iniciar XAMPP

#### Windows
```
1. Abra o painel de controle do XAMPP
   - Procure por "XAMPP Control Panel" no Menu Iniciar
   
2. Inicie os serviços
   - Clique em "Start" ao lado do Apache
   - Clique em "Start" ao lado do MySQL
   
3. Aguarde até que fiquem "Green" (verdes)
```

#### macOS
```
1. Abra /Applications/XAMPP/manager-osx.app
2. Clique nos botões Start para Apache e MySQL
```

#### Linux
```
sudo /opt/lampp/manager-linux.app
```

### 2️⃣ Verificar Localização do Projeto

O projeto deve estar em:
```
Windows:
  C:\xampp\htdocs\questionQueue-06\

macOS:
  /Applications/XAMPP/htdocs/questionQueue-06/

Linux:
  /opt/lampp/htdocs/questionQueue-06/
```

Se estiver em outro local, mova os arquivos para esta pasta.

### 3️⃣ Criar o Banco de Dados

#### Opção A: Via Interface Web (Mais Fácil) ⭐

1. Abra seu navegador e acesse:
   ```
   http://localhost/questionQueue-06/setup_database.php
   ```

2. Clique no botão **"✨ Criar Banco de Dados"**

3. Pronto! O banco foi criado automaticamente

#### Opção B: Via phpMyAdmin

1. Acesse: `http://localhost/phpmyadmin`

2. Copie todo o código SQL abaixo:

```sql
CREATE DATABASE IF NOT EXISTS questionqueue CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE questionqueue;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE games (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    mode VARCHAR(50) NOT NULL,
    score INT DEFAULT 0,
    duration INT DEFAULT 0,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    finished_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    game_id INT NOT NULL,
    question_number INT NOT NULL,
    answer TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_games_user_id ON games(user_id);
CREATE INDEX idx_games_created_at ON games(started_at);
CREATE INDEX idx_answers_game_id ON answers(game_id);

INSERT INTO users (name, email, password) VALUES 
('Teste', 'teste@teste.com', '$2y$10$H9O3E9QwqPa8GQ5V8Q5ZJO5zW9H8Z7Y6X5W4V3U2T1S0R9Q8P7');
```

3. Cole na aba SQL do phpMyAdmin e clique "Executar"

### 4️⃣ Verificar Instalação

Acesse: `http://localhost/questionQueue-06/test.php`

Você deve ver um relatório de diagnóstico verde confirmando:
- ✅ PHP conectado
- ✅ MySQL conectado
- ✅ Banco "questionqueue" criado
- ✅ Tabelas criadas

### 5️⃣ Acessar o Aplicativo

Abra seu navegador e vá para:
```
http://localhost/questionQueue-06/
```

## 🔐 Credenciais de Teste

Após a instalação, você pode fazer login com:

```
Email: teste@teste.com
Senha: 123456
```

## 📱 URLs Importantes

| URL | Descrição |
|-----|-----------|
| `http://localhost/questionQueue-06/` | 🔐 Login |
| `http://localhost/questionQueue-06/criar.php` | 📝 Cadastro |
| `http://localhost/questionQueue-06/setup_database.php` | ⚙️ Configuração |
| `http://localhost/questionQueue-06/test.php` | 🔍 Diagnóstico |
| `http://localhost/phpmyadmin/` | 💾 Gerenciar BD |

## ✅ Checklist de Verificação

- [ ] XAMPP iniciado (Apache e MySQL verdes)
- [ ] Projeto em `C:\xampp\htdocs\questionQueue-06\` (Windows)
- [ ] Banco de dados criado via `setup_database.php`
- [ ] `test.php` mostra todos os itens verdes
- [ ] Consigo fazer login com `teste@teste.com` / `123456`
- [ ] Consigo criar nova conta
- [ ] Consigo acessar a página de jogo após login

## 🆘 Solução de Problemas

### ❌ "Could not find driver"
**Solução:**
- Verifique se o PDO MySQL está habilitado em `php.ini`
- Windows: `C:\xampp\php\php.ini`
- Procure por `;extension=pdo_mysql` e remova o `;`

### ❌ "Access denied for user 'root'@'localhost'"
**Solução:**
- Verifique se MySQL está rodando no XAMPP Control Panel
- A senha padrão do root é vazia no XAMPP
- Não altere a senha sem motivo

### ❌ "Database 'questionqueue' doesn't exist"
**Solução:**
- Execute `setup_database.php` novamente
- Ou importe o SQL via phpMyAdmin

### ❌ Apache/MySQL não iniciam no XAMPP
**Solução:**
1. Clique em "Config" na linha do Apache/MySQL
2. Procure por "Port"
3. Se a porta estiver em uso, mude para 3307 (MySQL)
4. Salve e tente iniciar novamente

### ❌ Página em branco sem erros
**Solução:**
1. Verifique os logs:
   - `C:\xampp\apache\logs\error.log`
   - `C:\xampp\mysql\data\mysql_error.log`
2. Ative o debug em `config.php`:
   - Mude `DEBUG_MODE` para `true`

### ❌ "Já cadastro existe"
**Solução:**
- Limpe a tabela de usuários:
  ```sql
  DELETE FROM users WHERE email = 'seu@email.com';
  ```

## 🔄 Reiniciar Tudo

Se algo der errado, você pode resetar:

1. **Parar XAMPP:**
   - Clique Stop em Apache e MySQL

2. **Deletar o banco (opcional):**
   - Via phpMyAdmin: Clique no banco e escolha "Drop"

3. **Recomeçar:**
   - Inicie Apache e MySQL novamente
   - Acesse `setup_database.php`

## 📚 Estrutura de Arquivos

```
questionQueue-06/
├── index.php                 ← Página de login
├── criar.php                 ← Página de cadastro
├── home.php                  ← Dashboard do jogo
├── logout.php                ← Saída
├── setup_database.php        ← 🔧 Configurador (USAR PRIMEIRO!)
├── test.php                  ← 🔍 Diagnóstico
├── includes_auth.php         ← Sistema de autenticação
├── config.php                ← Configurações
├── database.sql              ← Script SQL (backup)
├── *.css                     ← Estilos
└── README.md                 ← Documentação
```

## 🎉 Pronto!

Agora você pode:
✅ Fazer login com `teste@teste.com` / `123456`
✅ Criar novas contas
✅ Jogar os diferentes modos de perguntas
✅ Ver seu histórico de jogos

## 💡 Dicas

- **Primeiro acesso:** Use a conta de teste
- **Criar conta:** Clique em "Criar conta" na página de login
- **Problemas:** Sempre comece por `test.php` para diagnóstico
- **Banco de dados:** Sempre use XAMPP com MySQL
- **Segurança:** Altere a senha do root em produção

---

**Desenvolvido com ❤️ para QuestionQueue**
**Última atualização: 3 de dezembro de 2025**
