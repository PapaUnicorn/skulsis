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
    $code = htmlspecialchars($_POST['code']);
    $name = htmlspecialchars($_POST['name']);
    $type = $_POST['type'];
    $class_level_id = !empty($_POST['class_level_id']) ? $_POST['class_level_id'] : null;
    $major_id = !empty($_POST['major_id']) ? $_POST['major_id'] : null;
    $teacher_id = !empty($_POST['teacher_id']) ? $_POST['teacher_id'] : null;

    try {
        if ($id) {
            // Update
            $sql = "UPDATE subjects SET code=?, name=?, type=?, class_level_id=?, major_id=?, teacher_id=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$code, $name, $type, $class_level_id, $major_id, $teacher_id, $id]);
        } else {
            // Insert
            $sql = "INSERT INTO subjects (code, name, type, class_level_id, major_id, teacher_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$code, $name, $type, $class_level_id, $major_id, $teacher_id]);
        }
        
        header("Location: ../../dashboard/index.php?page=subjects&status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../dashboard/index.php?page=subjects&action=add&error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../../dashboard/index.php?page=subjects");
    exit();
}
?>
