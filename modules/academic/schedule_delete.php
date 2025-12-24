<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM teaching_assignments WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../../dashboard/index.php?page=schedule&status=deleted");
    } catch (PDOException $e) {
        header("Location: ../../dashboard/index.php?page=schedule&error=delete_failed");
    }
} else {
    header("Location: ../../dashboard/index.php?page=schedule");
}
exit();
?>
