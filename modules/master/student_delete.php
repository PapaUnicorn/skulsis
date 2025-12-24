<?php
session_start();
require_once '../../config/database.php';

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../../dashboard/index.php?page=students&status=deleted");
    } catch (PDOException $e) {
        header("Location: ../../dashboard/index.php?page=students&error=delete_failed");
    }
} else {
    header("Location: ../../dashboard/index.php?page=students");
}
exit();
?>
