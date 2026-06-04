<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'] === 'seller' ? 'seller' : 'buyer';

    if (empty($full_name) || empty($email) || empty($password)) {
        die("All fields are required. <a href='login.php'>Go back</a>");
    }

    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("Email already registered. <a href='login.php'>Try again</a>");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $insert->execute([$full_name, $email, $password_hash, $role]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['full_name'] = $full_name;
    $_SESSION['role'] = $role;

    if ($role === 'seller') {
        header('Location: seller-dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
} else {
    header('Location: login.php');
}
?>