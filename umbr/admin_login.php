<?php
session_start();
require_once 'db.php';

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if($user && $password == 'admin123') {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $user['username'];
        header('Location: admin.php');
        exit;
    } else {
        $_SESSION['login_error'] = 'Неверный логин или пароль';
        header('Location: index.php?page=main#login');
        exit;
    }
}
?>