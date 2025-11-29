<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['usuario']) ? sanitizeInput($_POST['usuario']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    
    if ($usuario === LOGIN_USER && $senha === LOGIN_PASS) {
        $_SESSION['logged_in'] = true;
        $_SESSION['usuario'] = $usuario;
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['login_error'] = 'Usuário ou senha incorretos';
        header('Location: login.php');
        exit;
    }
}

function checkAuth() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

