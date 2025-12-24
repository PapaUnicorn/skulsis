<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: ../../index.php?page=login&error=empty_fields");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active']) {
                // Set Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['related_id'] = $user['related_id'];
                
                // Redirect based on role not strictly necessary if shared dashboard, but good practice
                header("Location: ../../dashboard/index.php");
                exit();
            } else {
                header("Location: ../../index.php?page=login&error=account_inactive");
                exit();
            }
        } else {
            header("Location: ../../index.php?page=login&error=invalid_credentials");
            exit();
        }
    } catch (PDOException $e) {
        // Log error properly in real app
        header("Location: ../../index.php?page=login&error=system_error");
        exit();
    }
} else {
    header("Location: ../../index.php?page=login");
    exit();
}
?>
