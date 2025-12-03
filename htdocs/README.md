# QuestionQueue - Sistema de Login Funcional

## ✅ Funcionalidades Implementadas

- ✅ Sistema de cadastro com validação
- ✅ Sistema de login com email e senha
- ✅ Armazenamento de usuários em banco de dados MySQL
- ✅ Hash seguro de senhas com bcrypt
- ✅ Proteção CSRF em formulários
- ✅ Sessões seguras
- ✅ Logout
- ✅ Redirecionamento automático para usuários não logados

## 📋 Pré-requisitos

- PHP 7.4+
- MySQL/MariaDB
- Apache/Nginx com suporte a PHP
- Extensão PDO para MySQL ativa

## 🚀 Instalação

### 1. Criar o Banco de Dados

Abra seu cliente MySQL (phpMyAdmin, MySQL Workbench, ou linha de comando) e execute:

```sql
-- Abra o arquivo database.sql e copie todo o conteúdo para executar
-- OU use a linha de comando:
mysql -u root < database.sql
```

**Ou manualmente pelo phpMyAdmin:**

1. Acesse `http://localhost/phpmyadmin`
2. Clique em "Nova"
3. Insira como nome do banco: `questionqueue`
4. Clique em "Criar"
5. Copie e cole todo o conteúdo do arquivo `database.sql` na aba SQL
6. Clique em "Executar"

### 2. Configurar Conexão com o Banco

O arquivo `includes_auth.php` já possui as configurações padrão:

```php
$host = 'localhost';
$dbname = 'questionqueue';
$username = 'root';
$password = '';
```

Se suas credenciais forem diferentes, edite o arquivo `includes_auth.php` na função `getDBConnection()`.

### 3. Testar o Sistema

1. Acesse: `http://localhost/questionQueue-06/`
2. Você verá a página de login

## 🔐 Credenciais de Teste

Um usuário de teste é criado automaticamente:

- **Email:** `teste@teste.com`
- **Senha:** `123456`

## 📝 Como Usar

### Criar Nova Conta

1. Clique em "Criar conta" na página de login
2. Preencha os dados:
   - Nome de usuário
   - Email válido
   - Senha (mín. 6 caracteres)
   - Confirme a senha
3. Clique em "Criar Minha Conta"
4. Será redirecionado automaticamente para a página de jogos

### Fazer Login

1. Preencha email e senha
2. Clique em "ENTRAR"
3. Será redirecionado para a página inicial (home.php)

### Sair

1. Clique no botão "Sair" no canto superior direito
2. Confirme a ação
3. Será redirecionado para a página de login

## 📁 Estrutura de Arquivos

```
questionQueue-06/
├── index.php              # Página de login
├── criar.php              # Página de cadastro
├── home.php               # Dashboard após login
├── logout.php             # Logout
├── includes_auth.php      # Classe de autenticação
├── database.sql           # Script SQL para criar banco
├── indexStyle.css         # Estilos do login
├── criar.css              # Estilos do cadastro
├── homeStyle.css          # Estilos do dashboard
└── README.md              # Este arquivo
```

## 🔧 Solução de Problemas

### Erro: "Could not find driver"

- Verifique se a extensão `php_pdo_mysql` está ativa no `php.ini`

### Erro: "Access denied for user 'root'@'localhost'"

- Verifique as credenciais no arquivo `includes_auth.php`
- Confirme a senha do MySQL

### Erro: "Database 'questionqueue' doesn't exist"

- Execute o script SQL fornecido (`database.sql`)

### Não consigo fazer login após cadastro

- Verifique se o MySQL está rodando
- Verifique os logs de erro do PHP em `error_log`
- Teste com o usuário de teste: `teste@teste.com` / `123456`

## 🔒 Segurança

- ✅ Senhas com hash bcrypt (PASSWORD_DEFAULT)
- ✅ Proteção contra CSRF com tokens
- ✅ Validação de email
- ✅ SQL Injection prevention com prepared statements
- ✅ XSS prevention com htmlspecialchars()

## 📊 Próximos Passos

Você pode adicionar:

- [ ] Recuperação de senha por email
- [ ] Autenticação de dois fatores
- [ ] Perfil do usuário
- [ ] Histórico de jogos
- [ ] Sistema de ranking
- [ ] Integração com redes sociais

## 📞 Suporte

Para dúvidas sobre a implementação, verifique:

1. Console de erros do navegador (F12)
2. Logs do Apache/Nginx
3. Logs do PHP (`error_log`)
4. Status do MySQL

---

**Sistema implementado com sucesso! 🎉**
