<div class="card">
    <h3>Input Nilai (Grading)</h3>
    <p style="color: #64748B; margin-bottom: 20px;">Pilih mata pelajaran dan kelas untuk mulai mengisi nilai.</p>

    <!-- Filter Form -->
    <form action="" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 30px;">
        <input type="hidden" name="page" value="grading">
        
        <!-- Assignment Dropdown (Combines Class & Subject) -->
        <select name="teaching_id" required style="padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; min-width: 300px;">
            <option value="">-- Pilih Jadwal Mengajar --</option>
            <?php
            // Only show assignments for this teacher if role is teacher
            $teacher_filter = "";
            $params = [];
            // Assuming we linked logged in user to teacher table via related_id
            // For now, if role=admin, show all. If teacher, show only theirs.
            // Simplified: Show all for prototype.
            
            $sql = "SELECT ta.id, c.name as class_name, s.name as subject_name 
                    FROM teaching_assignments ta
                    JOIN classes c ON ta.class_id = c.id
                    JOIN subjects s ON ta.subject_id = s.id
                    ORDER BY c.name, s.name ASC";
            $assigns = $pdo->query($sql)->fetchAll();
            
            $selected_ta = isset($_GET['teaching_id']) ? $_GET['teaching_id'] : '';
            foreach ($assigns as $a) {
                $sel = ($selected_ta == $a['id']) ? 'selected' : '';
                echo "<option value='{$a['id']}' $sel>{$a['class_name']} - {$a['subject_name']}</option>";
            }
            ?>
        </select>
        
        <!-- Assessment Type -->
        <select name="type" required style="padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            <option value="Tugas" <?php echo (isset($_GET['type']) && $_GET['type']=='Tugas')?'selected':''; ?>>Tugas</option>
            <option value="UH" <?php echo (isset($_GET['type']) && $_GET['type']=='UH')?'selected':''; ?>>Ulangan Harian</option>
            <option value="PTS" <?php echo (isset($_GET['type']) && $_GET['type']=='PTS')?'selected':''; ?>>PTS</option>
            <option value="PAS" <?php echo (isset($_GET['type']) && $_GET['type']=='PAS')?'selected':''; ?>>PAS</option>
        </select>
        
        <input type="text" name="title" placeholder="Judul (Mis: Tugas 1)" value="<?php echo isset($_GET['title']) ? htmlspecialchars($_GET['title']) : ''; ?>" style="padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">

        <button type="submit" class="btn btn-primary">Input Nilai</button>
    </form>
    
    <?php
    if (!empty($_GET['teaching_id'])):
        $ta_id = $_GET['teaching_id'];
        $type = isset($_GET['type']) ? $_GET['type'] : 'Tugas';
        $title = isset($_GET['title']) ? $_GET['title'] : 'Nilai Baru';
        
        // 1. Get Class ID from Teaching Assignment to find students
        $stmt_ta = $pdo->prepare("SELECT class_id FROM teaching_assignments WHERE id = ?");
        $stmt_ta->execute([$ta_id]);
        $ta_data = $stmt_ta->fetch();
        
        if ($ta_data) {
            $class_id = $ta_data['class_id'];
            
            // 2. Get Students (via enrollments)
            // Using logic from Attendance: linking students via enrollments
            $sql_s = "SELECT s.id, s.nis, s.full_name, enroll.id as enrollment_id
                      FROM students s
                      JOIN student_enrollments enroll ON s.id = enroll.student_id
                      WHERE enroll.class_id = ? AND enroll.status = 'Active'
                      ORDER BY s.full_name ASC";
            $stmt_s = $pdo->prepare($sql_s);
            $stmt_s->execute([$class_id]);
            $students = $stmt_s->fetchAll();
            
            // 3. Check if assessment exists headers, or just create on save?
            // For prototype simplicity: We are inputting new values. 
            // Ideally we'd select an EXISTING assessment or CREATE NEW.
            // Let's assume we are creating/updating grades for a specific TITLE.
        } else {
            $students = [];
        }
    ?>
    
    <?php if (count($students) > 0): ?>
        <form action="../modules/academic/grading_save.php" method="POST">
            <input type="hidden" name="teaching_assignment_id" value="<?php echo $ta_id; ?>">
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <input type="hidden" name="title" value="<?php echo $title; ?>">
            
            <h4 style="margin-bottom: 20px;">Input Nilai: <?php echo htmlspecialchars($title); ?> (<?php echo $type; ?>)</h4>
            
            <div style="overflow-x: auto; margin-bottom: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                        <tr>
                            <th style="text-align: left; padding: 12px; width: 50px;">No</th>
                            <th style="text-align: left; padding: 12px;">Nama Siswa</th>
                            <th style="text-align: left; padding: 12px; width: 150px;">Nilai (0-100)</th>
                            <th style="text-align: left; padding: 12px;">Umpan Balik (Feedback)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach($students as $s): 
                        ?>
                        <tr style="border-bottom: 1px solid #E2E8F0;">
                            <td style="padding: 12px;"><?php echo $no++; ?></td>
                            <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars($s['full_name']); ?> <span style="color:#94a3b8; font-size:0.8rem;"><?php echo $s['nis']; ?></span></td>
                            <td style="padding: 12px;">
                                <input type="number" name="score[<?php echo $s['enrollment_id']; ?>]" min="0" max="100" step="0.01" style="width: 100%; padding: 8px; border: 1px solid #CBD5E1; border-radius: 6px;">
                            </td>
                            <td style="padding: 12px;">
                                <input type="text" name="feedback[<?php echo $s['enrollment_id']; ?>]" style="width: 100%; padding: 8px; border: 1px solid #CBD5E1; border-radius: 6px;">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary">Simpan Nilai</button>
            </div>
        </form>
    <?php else: ?>
         <div style="background: #FFFBEB; padding: 20px; border-radius: 8px; color: #B45309;">
            Tidak ada siswa terdaftar di kelas ini.
         </div>
    <?php endif; ?>
    
    <?php endif; ?>
</div>
