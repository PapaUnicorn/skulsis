<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Jadwal & Plotting Guru (Teaching Assignments)</h3>
        <a href="?page=schedule&action=add" class="btn btn-primary"><i class="ph ph-plus"></i> Tambah Jadwal</a>
    </div>

    <?php
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    if ($action == 'list'):
        try {
            // Join tables to get readable names
            $sql = "SELECT ta.*, c.name as class_name, s.name as subject_name, t.full_name as teacher_name, ay.name as year_name
                    FROM teaching_assignments ta
                    JOIN classes c ON ta.class_id = c.id
                    JOIN subjects s ON ta.subject_id = s.id
                    JOIN teachers t ON ta.teacher_id = t.id
                    JOIN academic_years ay ON ta.academic_year_id = ay.id
                    ORDER BY c.name ASC, s.name ASC";
            $stmt = $pdo->query($sql);
            $assignments = $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            $assignments = [];
        }
    ?>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
            <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                <tr>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Kelas</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Mata Pelajaran</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Guru Pengampu</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Tahun Ajar</th>
                    <th style="text-align: right; padding: 12px; font-weight: 600; color: #475569;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($assignments) > 0): ?>
                    <?php foreach($assignments as $row): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 12px; font-weight: 600;"><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($row['subject_name']); ?></td>
                        <td style="padding: 12px; color: var(--primary); font-weight: 500;"><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                        <td style="padding: 12px; color: #64748B;"><?php echo htmlspecialchars($row['year_name']); ?></td>
                        <td style="padding: 12px; text-align: right;">
                             <a href="../modules/academic/schedule_delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus plotting ini?');" style="color: #EF4444;"><i class="ph ph-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: #64748B;">Belum ada jadwal plotting guru.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php elseif ($action == 'add'): 
        // Fetch Options
        $classes = $pdo->query("SELECT * FROM classes ORDER BY name ASC")->fetchAll();
        $subjects = $pdo->query("SELECT * FROM subjects ORDER BY name ASC")->fetchAll();
        $teachers = $pdo->query("SELECT * FROM teachers ORDER BY full_name ASC")->fetchAll();
        $years = $pdo->query("SELECT * FROM academic_years ORDER BY id DESC LIMIT 5")->fetchAll();
    ?>
    
    <div style="max-width: 600px;">
        <h4 style="margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 10px;">Tambah Plotting Jadwal (Guru Mapel)</h4>
        
        <form action="../modules/academic/schedule_save.php" method="POST">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tahun Ajaran</label>
                <select name="academic_year_id" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                    <?php foreach($years as $year): ?>
                    <option value="<?php echo $year['id']; ?>"><?php echo $year['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Kelas</label>
                <select name="class_id" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach($classes as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Mata Pelajaran</label>
                <select name="subject_id" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                    <option value="">-- Pilih Mapel --</option>
                    <?php foreach($subjects as $s): ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?> (<?php echo $s['code']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Guru Pengampu</label>
                <select name="teacher_id" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                    <option value="">-- Pilih Guru --</option>
                    <?php foreach($teachers as $t): ?>
                    <option value="<?php echo $t['id']; ?>"><?php echo $t['full_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Optional: Day & Time fields could go here for 'schedules' table, 
                 but keeping it to just 'Teaching Assignments' for simplicity in this step -->
            
            <div style="display: flex; gap: 10px;">
                <a href="?page=schedule" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Plotting</button>
            </div>
        </form>
    </div>

    <?php endif; ?>

</div>
