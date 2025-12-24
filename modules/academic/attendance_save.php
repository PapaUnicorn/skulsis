<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../dashboard/index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $date = $_POST['date'];
    $enrollment_ids = $_POST['enrollment_id'];
    $statuses = $_POST['status'];
    $notes_arr = $_POST['notes'];

    try {
        $pdo->beginTransaction();

        foreach ($enrollment_ids as $eid) {
            $status = isset($statuses[$eid]) ? $statuses[$eid] : 'Hadir';
            $note = isset($notes_arr[$eid]) ? $notes_arr[$eid] : '';

            // Check if exists
            $check = $pdo->prepare("SELECT id FROM attendances WHERE student_enrollment_id = ? AND date = ?");
            $check->execute([$eid, $date]);
            $exists = $check->fetch();

            if ($exists) {
                // Update
                $sql = "UPDATE attendances SET status = ?, notes = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$status, $note, $exists['id']]);
            } else {
                // Insert
                $sql = "INSERT INTO attendances (student_enrollment_id, date, status, notes) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$eid, $date, $status, $note]);
            }
        }

        $pdo->commit();
        header("Location: ../../dashboard/index.php?page=attendance&class_id=$class_id&date=$date&status=saved");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error saving attendance: " . $e->getMessage();
        // In pro app, redirect with error
    }
}
?>
