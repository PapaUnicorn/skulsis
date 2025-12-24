<?php
require_once 'config/database.php';

echo "Updating Subjects Table Schema (Adding Teacher)...\n";

try {
    // Add teacher_id column
    // Check if column exists first (naive check: just try add and catch exception or check info schema, simplified here by try-catch)
    
    $pdo->exec("ALTER TABLE subjects ADD COLUMN teacher_id BIGINT NULL AFTER major_id");
    $pdo->exec("ALTER TABLE subjects ADD CONSTRAINT fk_subject_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL");

    echo "Table 'subjects' updated with teacher_id.\n";

} catch (PDOException $e) {
    echo "Error (likely column already exists): " . $e->getMessage() . "\n";
}
?>
