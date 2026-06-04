<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        // Admin / staff go to admin dashboard
        if (in_array($user['role'], ['admin', 'moderator', 'dispatcher'])) {
            header('Location: admin/index.php');
        } elseif ($user['role'] === 'seller') {
            header('Location: seller-dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        echo "Invalid email or password. <a href='login.php'>Try again</a>";
    }
} else {
    header('Location: login.php');
}
?>