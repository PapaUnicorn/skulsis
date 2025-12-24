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
    $name = htmlspecialchars($_POST['name']);
    $class_level_id = $_POST['class_level_id'];
    $academic_year_id = $_POST['academic_year_id'];
    $homeroom_teacher_id = !empty($_POST['homeroom_teacher_id']) ? $_POST['homeroom_teacher_id'] : null;

    try {
        if ($id) {
            // Update
            $sql = "UPDATE classes SET name=?, class_level_id=?, academic_year_id=?, homeroom_teacher_id=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $class_level_id, $academic_year_id, $homeroom_teacher_id, $id]);
        } else {
            // Insert
            $sql = "INSERT INTO classes (name, class_level_id, academic_year_id, homeroom_teacher_id) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $class_level_id, $academic_year_id, $homeroom_teacher_id]);
        }
        
        header("Location: ../../dashboard/index.php?page=classes&status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../dashboard/index.php?page=classes&action=add&error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../../dashboard/index.php?page=classes");
    exit();
}
?>
