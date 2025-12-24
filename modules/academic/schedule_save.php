<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $academic_year_id = $_POST['academic_year_id'];
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];

    if (empty($class_id) || empty($subject_id) || empty($teacher_id)) {
        header("Location: ../../dashboard/index.php?page=schedule&action=add&error=missing_fields");
        exit();
    }

    try {
        $sql = "INSERT INTO teaching_assignments (academic_year_id, class_id, subject_id, teacher_id) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$academic_year_id, $class_id, $subject_id, $teacher_id]);
        
        header("Location: ../../dashboard/index.php?page=schedule&status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../dashboard/index.php?page=schedule&action=add&error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../../dashboard/index.php?page=schedule");
    exit();
}
?>
