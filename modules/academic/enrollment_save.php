<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type']; // 'add' or 'remove'
    $class_id = $_POST['class_id'];
    $academic_year_id = $_POST['academic_year_id'];

    try {
        if ($type == 'add') {
            $student_ids = isset($_POST['student_ids']) ? $_POST['student_ids'] : [];
            if (!empty($student_ids)) {
                $sql = "INSERT INTO student_enrollments (student_id, class_id, academic_year_id, status) VALUES (?, ?, ?, 'Active')";
                $stmt = $pdo->prepare($sql);
                foreach ($student_ids as $sid) {
                    $stmt->execute([$sid, $class_id, $academic_year_id]);
                }
            }
        } elseif ($type == 'remove') {
            $enrollment_ids = isset($_POST['enrollment_ids']) ? $_POST['enrollment_ids'] : [];
            if (!empty($enrollment_ids)) {
                // We can delete or set to 'Moved'/'Dropped Out'. 
                // For 'Plotting', usually deleting the wrong entry is fine if it was a mistake. 
                // However, preserving history is better. Let's DELETE for now as this is "Plotting" phase (setup).
                $sql = "DELETE FROM student_enrollments WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                foreach ($enrollment_ids as $eid) {
                    $stmt->execute([$eid]);
                }
            }
        }

        header("Location: ../../dashboard/index.php?page=enrollments&class_id=$class_id&academic_year_id=$academic_year_id&status=success");
        exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
