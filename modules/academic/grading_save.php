<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teaching_id = $_POST['teaching_assignment_id'];
    $type = $_POST['type'];
    $title = $_POST['title'];
    
    $scores = $_POST['score'];
    $feedbacks = $_POST['feedback'];

    try {
        $pdo->beginTransaction();

        // 1. Create or Find Assessment
        // Simplified: Check if exists by title + teaching_id + type
        $stmt = $pdo->prepare("SELECT id FROM assessments WHERE teaching_assignment_id = ? AND title = ? AND type = ?");
        $stmt->execute([$teaching_id, $title, $type]);
        $assessment = $stmt->fetch();

        if (!$assessment) {
            $stmt_ins = $pdo->prepare("INSERT INTO assessments (teaching_assignment_id, title, type) VALUES (?, ?, ?)");
            $stmt_ins->execute([$teaching_id, $title, $type]);
            $assessment_id = $pdo->lastInsertId();
        } else {
            $assessment_id = $assessment['id'];
        }

        // 2. Insert/Update Grades
        foreach ($scores as $enrollment_id => $score) {
            $feedback = isset($feedbacks[$enrollment_id]) ? $feedbacks[$enrollment_id] : '';
            
            // Skip empty scores unless we want to record 0? Allow 0. Skip if empty string.
            if ($score === '') continue;

            // Check if grade exists
            $check = $pdo->prepare("SELECT id FROM student_grades WHERE assessment_id = ? AND student_enrollment_id = ?");
            $check->execute([$assessment_id, $enrollment_id]);
            $exists = $check->fetch();

            if ($exists) {
                $upd = $pdo->prepare("UPDATE student_grades SET score = ?, feedback = ? WHERE id = ?");
                $upd->execute([$score, $feedback, $exists['id']]);
            } else {
                $ins = $pdo->prepare("INSERT INTO student_grades (assessment_id, student_enrollment_id, score, feedback) VALUES (?, ?, ?, ?)");
                $ins->execute([$assessment_id, $enrollment_id, $score, $feedback]);
            }
        }

        $pdo->commit();
        header("Location: ../../dashboard/index.php?page=grading&teaching_id=$teaching_id&status=saved");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
