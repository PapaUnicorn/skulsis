<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Jadwal Pelajaran Saya</h3>
        <?php
        // 1. Get Logged in Student Info
        // We assume $_SESSION['user_id'] is linked to 'users' table.
        // We need to find the student_id from users.related_id
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT related_id FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $u = $stmt->fetch();
        $student_id = $u['related_id'];

        // 2. Find Class Enrollment
        // Assuming latest active enrollment
        $stmt = $pdo->prepare("SELECT c.name as class_name, c.id as class_id, ay.name as year_name 
                               FROM student_enrollments se 
                               JOIN classes c ON se.class_id = c.id 
                               JOIN academic_years ay ON se.academic_year_id = ay.id
                               WHERE se.student_id = ? AND se.status = 'Active' 
                               ORDER BY se.id DESC LIMIT 1");
        $stmt->execute([$student_id]);
        $enrollment = $stmt->fetch();
        ?>
        <?php if ($enrollment): ?>
        <div class="badge" style="background: #EFF6FF; color: #2563EB; font-size: 0.9rem;">
            Kelas: <strong><?php echo htmlspecialchars($enrollment['class_name']); ?></strong> (<?php echo htmlspecialchars($enrollment['year_name']); ?>)
        </div>
        <?php endif; ?>
    </div>

    <?php if ($enrollment): ?>
        <?php
        // 3. Fetch Schedule for this Class
        $class_id = $enrollment['class_id'];
        $sql = "SELECT s.*, sub.name as subject_name, t.full_name as teacher_name 
                FROM schedules s
                JOIN teaching_assignments ta ON s.teaching_assignment_id = ta.id
                JOIN subjects sub ON ta.subject_id = sub.id
                JOIN teachers t ON ta.teacher_id = t.id
                WHERE ta.class_id = ?
                ORDER BY 
                CASE 
                    WHEN s.day = 'Senin' THEN 1
                    WHEN s.day = 'Selasa' THEN 2
                    WHEN s.day = 'Rabu' THEN 3
                    WHEN s.day = 'Kamis' THEN 4
                    WHEN s.day = 'Jumat' THEN 5
                    WHEN s.day = 'Sabtu' THEN 6
                    ELSE 7
                END, s.start_time ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$class_id]);
        $schedules = $stmt->fetchAll();
        
        // Group by Day
        $grouped = [];
        foreach($schedules as $sch) {
            $grouped[$sch['day']][] = $sch;
        }
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
            <?php foreach($days as $day): ?>
                <?php if (isset($grouped[$day])): ?>
                <div class="card" style="border: 1px solid #E2E8F0; box-shadow: none; padding: 0; overflow: hidden; margin-bottom: 0;">
                    <div style="background: #F8FAFC; padding: 12px 20px; border-bottom: 1px solid #E2E8F0; font-weight: 600; color: var(--primary);">
                        <?php echo $day; ?>
                    </div>
                    <div style="padding: 10px;">
                        <?php foreach($grouped[$day] as $item): ?>
                        <div style="display: flex; padding: 10px; border-bottom: 1px solid #F1F5F9;">
                            <div style="width: 100px; font-size: 0.9rem; color: #64748B; flex-shrink: 0;">
                                <?php echo substr($item['start_time'], 0, 5) . ' - ' . substr($item['end_time'], 0, 5); ?>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($item['subject_name']); ?></div>
                                <div style="font-size: 0.85rem; color: #64748B;"><?php echo htmlspecialchars($item['teacher_name']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <?php if (empty($grouped)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #94A3B8;">
                    Belum ada jadwal pelajaran yang diatur untuk kelas ini.
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 50px; background: #F8FAFC; border-radius: 12px; border: 1px dashed #CBD5E1;">
            <i class="ph ph-warning-circle" style="font-size: 2rem; color: #94A3B8; margin-bottom: 10px;"></i>
            <p style="color: #64748B;">Anda belum terdaftar dalam kelas aktif manapun.<br>Silakan hubungi Administrator atau Wali Kelas.</p>
        </div>
    <?php endif; ?>
</div>
