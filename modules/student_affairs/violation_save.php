<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $violation_date = $_POST['violation_date'];
    $violation_type = htmlspecialchars($_POST['violation_type']);
    $points = intval($_POST['points']);
    $sanction = htmlspecialchars($_POST['sanction']);

    try {
        $sql = "INSERT INTO violations (student_id, violation_date, violation_type, points, sanction) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$student_id, $violation_date, $violation_type, $points, $sanction]);
        
        header("Location: ../../dashboard/index.php?page=counseling&status=success");
        exit();
    } catch (PDOException $e) {
        header("Location: ../../dashboard/index.php?page=counseling&action=add_violation&error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>
