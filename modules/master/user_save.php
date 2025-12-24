<?php
session_start();
require_once '../../config/database.php';

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $role = $_POST['role'];
    $is_active = $_POST['is_active'];

    try {
        if ($id) {
            // Update
            if ($password) {
                // Update with password
                $sql = "UPDATE users SET username=?, email=?, password=?, role=?, is_active=? WHERE id=?";
                $params = [$username, $email, $password, $role, $is_active, $id];
            } else {
                // Update without password
                $sql = "UPDATE users SET username=?, email=?, role=?, is_active=? WHERE id=?";
                $params = [$username, $email, $role, $is_active, $id];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } else {
            // Insert
            $sql = "INSERT INTO users (username, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $email, $password, $role, $is_active]);
        }
        
        header("Location: ../../dashboard/index.php?page=users&status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../dashboard/index.php?page=users&action=add&error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../../dashboard/index.php?page=users");
    exit();
}
?>
