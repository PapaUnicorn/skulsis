<?php
require_once 'config/database.php';

echo "Seeding Master Data...\n";

try {
    // 1. Seed Academic Years (Tahun Ajaran)
    $years = ['2023/2024', '2024/2025', '2025/2026'];
    foreach ($years as $y) {
        $chk = $pdo->prepare("SELECT id FROM academic_years WHERE name = ?");
        $chk->execute([$y]);
        if (!$chk->fetch()) {
            $is_active = ($y == '2024/2025') ? 'active' : 'inactive';
            $stmt = $pdo->prepare("INSERT INTO academic_years (name, status) VALUES (?, ?)");
            $stmt->execute([$y, $is_active]);
            echo "Inserted Academic Year: $y ($is_active)\n";
        }
    }

    // 2. Seed Class Levels (Tingkat / Jenjang)
    // Assuming SMA (X, XI, XII) based on typical context, but adding SMP (7,8,9) just in case to cover both.
    $levels = ['7', '8', '9', 'X', 'XI', 'XII'];
    foreach ($levels as $l) {
        $chk = $pdo->prepare("SELECT id FROM class_levels WHERE name = ?");
        $chk->execute([$l]);
        if (!$chk->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO class_levels (name) VALUES (?)");
            $stmt->execute([$l]);
            echo "Inserted Class Level: $l\n";
        }
    }

    // 3. Seed Majors (Jurusan) - Optional but good to have
    $majors = [
        ['code' => 'MIPA', 'name' => 'Matematika dan Ilmu Pengetahuan Alam'],
        ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial'],
        ['code' => 'BHS', 'name' => 'Bahasa dan Budaya'],
        ['code' => 'UMUM', 'name' => 'Umum (SD/SMP)']
    ];
    foreach ($majors as $m) {
        $chk = $pdo->prepare("SELECT id FROM majors WHERE code = ?");
        $chk->execute([$m['code']]);
        if (!$chk->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO majors (code, name) VALUES (?, ?)");
            $stmt->execute([$m['code'], $m['name']]);
            echo "Inserted Major: {$m['name']}\n";
        }
    }

    echo "Seeding Completed Successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
