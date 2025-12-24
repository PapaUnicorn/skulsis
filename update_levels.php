<?php
require_once 'config/database.php';

echo "Updating Master Data (Levels & Years)...\n";

try {
    // 1. Check if we can reset Class Levels (only if no classes exist yet to preserve FK)
    $stmt = $pdo->query("SELECT COUNT(*) FROM classes");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Safe to reset
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("TRUNCATE TABLE class_levels");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "Resetting Class Levels...\n";

        $levels = [
            '1' => 'SD', '2' => 'SD', '3' => 'SD', '4' => 'SD', '5' => 'SD', '6' => 'SD',
            '7' => 'SMP', '8' => 'SMP', '9' => 'SMP',
            'X' => 'SMA', 'XI' => 'SMA', 'XII' => 'SMA'
        ];

        foreach ($levels as $l => $desc) {
            // We just store the name '1', 'X', etc.
            $stmt = $pdo->prepare("INSERT INTO class_levels (name) VALUES (?)");
            $stmt->execute([$l]);
        }
        echo "Inserted Sorted Levels: SD (1-6), SMP (7-9), SMA (X-XII).\n";
    } else {
        // Appending SD levels if they don't exist (Order might be messy in DB ID, but we have data)
        $new_levels = ['1', '2', '3', '4', '5', '6'];
        foreach ($new_levels as $l) {
            $chk = $pdo->prepare("SELECT id FROM class_levels WHERE name = ?");
            $chk->execute([$l]);
            if (!$chk->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO class_levels (name) VALUES (?)");
                $stmt->execute([$l]);
                echo "Appended Level: $l\n";
            }
        }
    }

    // 2. Academic Years - User wants "two school years"
    // We will ensure 2024/2025 and 2025/2026 exist.
    $target_years = ['2024/2025', '2025/2026'];
    
    foreach ($target_years as $y) {
        $chk = $pdo->prepare("SELECT id FROM academic_years WHERE name = ?");
        $chk->execute([$y]);
        if (!$chk->fetch()) {
            $status = ($y == '2024/2025') ? 'active' : 'inactive';
            $stmt = $pdo->prepare("INSERT INTO academic_years (name, status) VALUES (?, ?)");
            $stmt->execute([$y, $status]);
            echo "Inserted Year: $y\n";
        }
    }
    
    // Optional: Hide others/Delete others if no dependencies? 
    // Safest is to just update status of others to inactive
    $pdo->exec("UPDATE academic_years SET status = 'inactive' WHERE name NOT IN ('2024/2025', '2025/2026')");
    $pdo->exec("UPDATE academic_years SET status = 'active' WHERE name = '2024/2025'");
    
    echo "Academic Years Updated. Active: 2024/2025.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
