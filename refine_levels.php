<?php
require_once 'config/database.php';

echo "Refining Class Levels description...\n";

try {
    $updates = [
        '1' => 'SD - Kelas 1',
        '2' => 'SD - Kelas 2',
        '3' => 'SD - Kelas 3',
        '4' => 'SD - Kelas 4',
        '5' => 'SD - Kelas 5',
        '6' => 'SD - Kelas 6',
        '7' => 'SMP - Kelas 7',
        '8' => 'SMP - Kelas 8',
        '9' => 'SMP - Kelas 9',
        'X' => 'SMA - Kelas X',
        'XI' => 'SMA - Kelas XI',
        'XII' => 'SMA - Kelas XII'
    ];

    $pdo->beginTransaction();

    foreach ($updates as $old => $new) {
        // Update name where name exactly matches the old key
        $stmt = $pdo->prepare("UPDATE class_levels SET name = ? WHERE name = ?");
        $stmt->execute([$new, $old]);
        
        if ($stmt->rowCount() > 0) {
            echo "Updated '$old' to '$new'\n";
        }
    }
    
    $pdo->commit();
    echo "Class levels updated successfully.\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
?>
