<?php
require_once 'includes_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$auth->logout();
header('Location: index.php');
exit();
?>
