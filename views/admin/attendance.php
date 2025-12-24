<div class="card">
    <h3>Absensi Siswa</h3>
    <p style="color: #64748B; margin-bottom: 20px;">Silakan pilih kelas dan tanggal untuk mengisi kehadiran.</p>

    <!-- Filter Form -->
    <form action="" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 30px;">
        <input type="hidden" name="page" value="attendance">
        
        <select name="class_id" required style="padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
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
        
        <input type="date" name="date" required value="<?php echo isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); ?>" style="padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
        
        <button type="submit" class="btn btn-primary">Tampilkan Siswa</button>
    </form>

    <!-- Attendance List -->
    <?php
    if (!empty($_GET['class_id']) && !empty($_GET['date'])):
        $class_id = $_GET['class_id'];
        $date = $_GET['date'];

        // Get students in class (Assuming we are just getting all students for now since Enrollments table might be empty in this prototype phase)
        // In real app: JOIN student_enrollments se ON s.id = se.student_id WHERE se.class_id = ?
        // For Phase 1 prototype: Let's assume user enrolled via logic I haven't fully built yet. 
        // fallback: select all students if no enrollment logic found, OR query student_enrollments properly.
        // Let's query enrollments properly, assuming I might have manually added data or will later.
        // If empty, I'll show a message.
        
        // Let's construct a query that can find students even if I just link them loosely for now? 
        // No, standard SQL:
        $sql = "SELECT s.id, s.nis, s.full_name, enroll.id as enrollment_id
                FROM students s
                JOIN student_enrollments enroll ON s.id = enroll.students_id 
                WHERE enroll.class_id = ? AND enroll.status = 'Active' 
                ORDER BY s.full_name ASC";
        
        // Wait, table is student_enrollments, column is student_id (singular) in my schema schema.
        // Correction: student_id
        $sql = "SELECT s.id, s.nis, s.full_name, enroll.id as enrollment_id
                FROM students s
                JOIN student_enrollments enroll ON s.id = enroll.student_id 
                WHERE enroll.class_id = ? AND enroll.status = 'Active' 
                ORDER BY s.full_name ASC";
                
        // NOTE: Since I haven't built an Enrollment Interface, this might return 0 results.
        // To make it usable for the user right now (demo purpose), I will fallback to showing ALL students if no enrollments found? 
        // No, that's dangerous. I will show a message explaining "Belum ada siswa di kelas ini".
        
        try {
           $stmt = $pdo->prepare($sql);
           $stmt->execute([$class_id]);
           $students = $stmt->fetchAll();
        } catch (PDOException $e) { $students = []; }
    ?>

    <?php if (count($students) > 0): ?>
        <form action="../modules/academic/attendance_save.php" method="POST">
            <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
            <input type="hidden" name="date" value="<?php echo $date; ?>">
            
            <div style="overflow-x: auto; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                        <tr>
                            <th style="text-align: left; padding: 12px;">NIS</th>
                            <th style="text-align: left; padding: 12px;">Nama Siswa</th>
                            <th style="text-align: center; padding: 12px;">Hadir</th>
                            <th style="text-align: center; padding: 12px;">Sakit</th>
                            <th style="text-align: center; padding: 12px;">Izin</th>
                            <th style="text-align: center; padding: 12px;">Alpha</th>
                            <th style="text-align: left; padding: 12px;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $s): 
                            // Check existing attendance
                            $att_sql = "SELECT * FROM attendances WHERE student_enrollment_id = ? AND date = ?";
                            $att_stmt = $pdo->prepare($att_sql);
                            $att_stmt->execute([$s['enrollment_id'], $date]);
                            $existing = $att_stmt->fetch();
                            $status = $existing ? $existing['status'] : 'Hadir';
                            $notes = $existing ? $existing['notes'] : '';
                        ?>
                        <tr style="border-bottom: 1px solid #E2E8F0;">
                            <td style="padding: 12px;"><?php echo $s['nis']; ?></td>
                            <td style="padding: 12px; font-weight: 500;"><?php echo $s['full_name']; ?></td>
                            <!-- Enrollment ID hidden -->
                            <input type="hidden" name="enrollment_id[]" value="<?php echo $s['enrollment_id']; ?>">
                            
                            <td style="text-align: center;">
                                <input type="radio" name="status[<?php echo $s['enrollment_id']; ?>]" value="Hadir" <?php echo $status=='Hadir'?'checked':''; ?>>
                            </td>
                            <td style="text-align: center;">
                                <input type="radio" name="status[<?php echo $s['enrollment_id']; ?>]" value="Sakit" <?php echo $status=='Sakit'?'checked':''; ?>>
                            </td>
                            <td style="text-align: center;">
                                <input type="radio" name="status[<?php echo $s['enrollment_id']; ?>]" value="Izin" <?php echo $status=='Izin'?'checked':''; ?>>
                            </td>
                            <td style="text-align: center;">
                                <input type="radio" name="status[<?php echo $s['enrollment_id']; ?>]" value="Alpha" <?php echo $status=='Alpha'?'checked':''; ?>>
                            </td>
                            <td style="padding: 12px;">
                                <input type="text" name="notes[<?php echo $s['enrollment_id']; ?>]" value="<?php echo htmlspecialchars($notes); ?>" placeholder="..." style="width: 100%; padding: 6px; border: 1px solid #CBD5E1; border-radius: 4px;">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary">Simpan Absensi</button>
            </div>
        </form>
    <?php else: ?>
        <div style="background: #FFFBEB; padding: 20px; border-radius: 8px; color: #B45309; border: 1px solid #FCD34D;">
            <strong>Data Siswa Kosong.</strong><br>
            Tidak ada siswa yang terdaftar di kelas ini (Tabel <code>student_enrollments</code> kosong).<br>
            Silakan masukkan siswa ke dalam kelas terlebih dahulu (Fitur ini belum ada di UI prototype, harus via database manual untuk saat ini).
            <br><br>
            <em>Hint for Developer: Insert into student_enrollments manually in database to test this.</em>
        </div>
    <?php endif; ?>

    <?php endif; ?>
</div>
