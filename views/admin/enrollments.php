<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Plotting Kelas (Student Enrollments)</h3>
    </div>

    <!-- Filter Form to select Class -->
    <form action="" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 30px;">
        <input type="hidden" name="page" value="enrollments">
        
        <select name="class_id" required onchange="this.form.submit()" style="padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; min-width: 200px;">
            <option value="">-- Pilih Kelas --</option>
            <?php
            $classes = $pdo->query("SELECT * FROM classes ORDER BY name ASC")->fetchAll();
            $selected_class = isset($_GET['class_id']) ? $_GET['class_id'] : '';
            foreach ($classes as $c) {
                $sel = ($selected_class == $c['id']) ? 'selected' : '';
                echo "<option value='{$c['id']}' $sel>{$c['name']}</option>";
            }
            ?>
        </select>
        
         <select name="academic_year_id" style="padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            <?php
            $years = $pdo->query("SELECT * FROM academic_years ORDER BY id DESC LIMIT 5")->fetchAll();
            $selected_year = isset($_GET['academic_year_id']) ? $_GET['academic_year_id'] : ($years[0]['id'] ?? '');
            foreach ($years as $y) {
                $sel = ($selected_year == $y['id']) ? 'selected' : '';
                echo "<option value='{$y['id']}' $sel>{$y['name']}</option>";
            }
            ?>
        </select>
    </form>
    
    <?php
    if ($selected_class && $selected_year):
        // 1. Get Students CURRENTLY in this class
        $sql_in = "SELECT enroll.id as enrollment_id, s.full_name, s.nisn, s.gender 
                   FROM student_enrollments enroll
                   JOIN students s ON enroll.student_id = s.id
                   WHERE enroll.class_id = ? AND enroll.academic_year_id = ? AND enroll.status = 'Active'";
        $stmt_in = $pdo->prepare($sql_in);
        $stmt_in->execute([$selected_class, $selected_year]);
        $students_in = $stmt_in->fetchAll();

        // 2. Get Students NOT enrolled in ANY class for this academic year (Available students)
        // complex query: Select students where ID NOT IN (select student_id from enrollments where year = ?)
        $sql_av = "SELECT s.id, s.full_name, s.nisn, s.gender 
                   FROM students s
                   WHERE s.id NOT IN (
                       SELECT student_id FROM student_enrollments WHERE academic_year_id = ? AND status = 'Active'
                   )
                   ORDER BY s.full_name ASC";
        $stmt_av = $pdo->prepare($sql_av);
        $stmt_av->execute([$selected_year]);
        $students_av = $stmt_av->fetchAll();
    ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        
        <!-- Available Students -->
        <div style="background: white; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px;">
            <h4 style="margin-bottom: 15px; color: #475569;">Siswa Belum Dapat Kelas</h4>
            <form action="../modules/academic/enrollment_save.php" method="POST">
                <input type="hidden" name="type" value="add">
                <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
                <input type="hidden" name="academic_year_id" value="<?php echo $selected_year; ?>">
                
                <div style="height: 400px; overflow-y: auto; border: 1px solid #F1F5F9; margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <?php foreach($students_av as $s): ?>
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 8px;"><input type="checkbox" name="student_ids[]" value="<?php echo $s['id']; ?>"></td>
                            <td style="padding: 8px; font-size: 0.9rem;"><?php echo htmlspecialchars($s['full_name']); ?></td>
                            <td style="padding: 8px; color: #94A3B8; font-size: 0.8rem;"><?php echo htmlspecialchars($s['nisn']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($students_av)): ?>
                            <tr><td colspan="3" style="padding: 20px; text-align: center; color: #94A3B8;">Tidak ada siswa tersedia.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk ke Kelas >></button>
            </form>
        </div>

        <!-- Enrolled Students (In Class) -->
        <div style="background: white; border: 1px solid #E2E8F0; border-radius: 8px; padding: 20px;">
            <h4 style="margin-bottom: 15px; color: #475569;">Anggota Kelas Ini (<?php echo count($students_in); ?>)</h4>
            <form action="../modules/academic/enrollment_save.php" method="POST">
                <input type="hidden" name="type" value="remove">
                <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
                 <input type="hidden" name="academic_year_id" value="<?php echo $selected_year; ?>">
                
                <div style="height: 400px; overflow-y: auto; border: 1px solid #F1F5F9; margin-bottom: 15px;">
                     <table style="width: 100%; border-collapse: collapse;">
                        <?php foreach($students_in as $s): ?>
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 8px;"><input type="checkbox" name="enrollment_ids[]" value="<?php echo $s['enrollment_id']; ?>"></td>
                            <td style="padding: 8px; font-size: 0.9rem; font-weight: 500;"><?php echo htmlspecialchars($s['full_name']); ?></td>
                            <td style="padding: 8px; color: #94A3B8; font-size: 0.8rem;"><?php echo htmlspecialchars($s['nisn']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($students_in)): ?>
                            <tr><td colspan="3" style="padding: 20px; text-align: center; color: #94A3B8;">Kelas masih kosong.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>
                <button type="submit" class="btn btn-outline" style="width: 100%; border-color: #EF4444; color: #EF4444;"><< Keluarkan dari Kelas</button>
            </form>
        </div>

    </div>
    
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #94A3B8; border: 2px dashed #E2E8F0; border-radius: 12px;">
            Silakan pilih kelas terlebih dahulu untuk mengatur anggota rombel.
        </div>
    <?php endif; ?>

</div>
