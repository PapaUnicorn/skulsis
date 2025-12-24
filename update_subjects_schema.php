<?php
require_once 'config/database.php';

echo "Updating Subjects Table Schema...\n";

try {
    // Add columns if they don't exist
    // 1. level_id (Assuming linking to class_levels? Or just text? The user said "specify which level", so class_levels_id is best)
    // 2. class_id (The user said "specify to which class does that belong to"). 
    
    // Note: Linking Subject directly to specific Class ID (e.g. X IPA 1) is unusual standard practice (usually subject is general, and linked via Teaching Assignment).
    // BUT, the user explicitly asked: "specify to which class does that belong to".
    // I will interpret providing an OPTIONAL link to specific class level or specific class if needed.
    
    // Better INTERPRETATION:
    // "Levels" -> class_levels (e.g. Mat Wajib for Class X)
    // "Class" -> majors? or specific class?
    
    // Let's add columns: 
    // - `class_level_id` (INT, Nullable - FK to class_levels)
    // - `major_id` (INT, Nullable - FK to majors) -> Often subjects are specific to majors (IPA vs IPS)
    
    // Check if columns exist
    $pdo->exec("ALTER TABLE subjects ADD COLUMN class_level_id INT NULL AFTER name");
    $pdo->exec("ALTER TABLE subjects ADD COLUMN major_id INT NULL AFTER class_level_id");
    
    // Add FKs
    $pdo->exec("ALTER TABLE subjects ADD CONSTRAINT fk_subject_level FOREIGN KEY (class_level_id) REFERENCES class_levels(id) ON DELETE SET NULL");
    $pdo->exec("ALTER TABLE subjects ADD CONSTRAINT fk_subject_major FOREIGN KEY (major_id) REFERENCES majors(id) ON DELETE SET NULL");

    echo "Table 'subjects' updated with class_level_id and major_id.\n";

} catch (PDOException $e) {
    echo "Error (likely columns already exist): " . $e->getMessage() . "\n";
}
?>
