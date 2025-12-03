# ⚠️ Solução: Erro de Session ini_set

## Problema

```
Warning: ini_set(): Session ini settings cannot be changed when a session is active 
in C:\xampp\htdocs\config.php on line 60
```

## Por que ocorre?

Este erro acontece quando você tenta usar `ini_set()` para configurar opções de sessão **DEPOIS** que `session_start()` foi chamado.

A ordem correta é:
1. ✅ Configurar com `ini_set()` 
2. ✅ Depois chamar `session_start()`

## ✅ Solução Implementada

O projeto foi atualizado para configurar sessões ANTES de iniciar:

**Em `includes_auth.php` (linhas 123-131):**
```php
if (session_status() == PHP_SESSION_NONE) {
    // Configurar ANTES de iniciar
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.gc_maxlifetime', 3600);
    
    session_start();  // Agora sim!
}
```

## 🔧 Se o erro continuar

### Opção 1: Limpar Cache do Navegador
1. Pressione `Ctrl + Shift + Delete`
2. Selecione "Cookies e dados de sites"
3. Clique "Limpar dados"
4. Recarregue a página

### Opção 2: Remover Arquivo config.php da raiz
Se há um `C:\xampp\htdocs\config.php`:
1. Renomeie para `config.php.bak`
2. Reinicie o navegador
3. Teste novamente

### Opção 3: Limpar Sessões (Windows)
```cmd
# Abra PowerShell como Administrador
Remove-Item -Recurse -Force C:\xampp\tmp\
```

Depois reinicie o Apache e MySQL no XAMPP.

## 📝 Checklist Final

- [ ] Erro desapareceu ao recarregar a página
- [ ] Consigo fazer login
- [ ] Consigo criar nova conta
- [ ] Nenhum aviso aparece no navegador
- [ ] Console (F12) não mostra erros

## 🎯 Como Verificar

1. Abra `http://localhost/questionQueue-06/test.php`
2. Procure por erros de sessão
3. Se tudo está verde ✅, está funcionando!

## 📚 Leitura Adicional

- [PHP: session_start](https://www.php.net/manual/en/function.session-start.php)
- [PHP: ini_set](https://www.php.net/manual/en/function.ini-set.php)
- [Session Security in PHP](https://www.php.net/manual/en/session.security.php)

---

**Problema resolvido! ✅**
