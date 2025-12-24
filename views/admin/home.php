<?php
// Fetch Stats Based on Role
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$related_id = $_SESSION['related_id'] ?? null; // Make sure this is set in login

// 1. ADMIN DASHBOARD LOGIC
if ($role == 'admin') {
    try {
        $c_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $c_teachers = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
        $c_classes = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn(); // Rombel
        $c_ppdb = $pdo->query("SELECT COUNT(*) FROM ppdb_registrations WHERE status='Pending'")->fetchColumn(); // New PPDB
        
        // Recent Students
        $recent_students = $pdo->query("SELECT full_name, created_at FROM students ORDER BY id DESC LIMIT 5")->fetchAll();
    } catch (PDOException $e) {
        $c_students = $c_teachers = $c_classes = $c_ppdb = 0;
        $recent_students = [];
    }
}

// 2. TEACHER DASHBOARD LOGIC
elseif ($role == 'teacher') {
    try {
        // Fetch Teacher Table ID
        $stmt = $pdo->prepare("SELECT related_id FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $uInfo = $stmt->fetch();
        $teacher_id = $uInfo['related_id'];

        // My Classes (Homeroom)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM classes WHERE homeroom_teacher_id = ?");
        $stmt->execute([$teacher_id]);
        $my_homeroom = $stmt->fetchColumn();

        // My Teaching Schedule (Count Sections)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM teaching_assignments WHERE teacher_id = ?");
        $stmt->execute([$teacher_id]);
        $my_sections = $stmt->fetchColumn();
        
        // Count Students in those assignments (Approx)
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT se.student_id) 
                               FROM teaching_assignments ta 
                               JOIN student_enrollments se ON ta.class_id = se.class_id 
                               WHERE ta.teacher_id = ? AND se.status='Active'");
        $stmt->execute([$teacher_id]);
        $my_students = $stmt->fetchColumn();

    } catch (PDOException $e) {
        $my_homeroom = $my_sections = $my_students = 0;
    }
}

// 3. STUDENT DASHBOARD LOGIC
elseif ($role == 'student') {
     try {
        // Fetch Student Table ID
        $stmt = $pdo->prepare("SELECT related_id FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $uInfo = $stmt->fetch();
        $student_id = $uInfo['related_id'];

        // Get Active Class
        $stmt = $pdo->prepare("SELECT c.name, ay.name as year 
                               FROM student_enrollments se 
                               JOIN classes c ON se.class_id = c.id
                               JOIN academic_years ay ON se.academic_year_id = ay.id 
                               WHERE se.student_id = ? AND se.status = 'Active' LIMIT 1");
        $stmt->execute([$student_id]);
        $my_class = $stmt->fetch();

        // Get Assessment/Grades count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM student_grades WHERE student_enrollment_id IN (SELECT id FROM student_enrollments WHERE student_id = ?)", [$student_id]);
        $stmt->execute([$student_id]);
        $my_grades_count = $stmt->fetchColumn();

    } catch (PDOException $e) {
        $my_class = null;
        $my_grades_count = 0;
    }
}
?>

<!-- Welcome Section -->
<div class="card" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; border: none;">
    <h3 style="margin-bottom: 10px; color: white;">Selamat Datang, <?php echo htmlspecialchars($username); ?>! 👋</h3>
    <p style="color: #94A3B8;">Anda login sebagai <strong><?php echo ucfirst($user_role); ?></strong>.
    <?php if ($role == 'student' && $my_class): ?>
        Anda tergabung di kelas <strong><?php echo htmlspecialchars($my_class['name']); ?></strong> (<?php echo htmlspecialchars($my_class['year']); ?>).
    <?php else: ?>
        Semoga harimu menyenangkan!
    <?php endif; ?>
    </p>
</div>

<!-- DASHBOARD CONTENT BASED ON ROLE -->

<?php if ($role == 'admin'): ?>
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;"><i class="ph-fill ph-student"></i></div>
        <div class="stat-info">
            <h4>Total Siswa</h4>
            <div class="value"><?php echo number_format($c_students); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #F0FDF4; color: #16A34A;"><i class="ph-fill ph-chalkboard-teacher"></i></div>
        <div class="stat-info">
            <h4>Total Guru</h4>
            <div class="value"><?php echo number_format($c_teachers); ?></div>
        </div>
    </div>
    <div class="stat-card">
         <div class="stat-icon" style="background: #FFF7ED; color: #EA580C;"><i class="ph-fill ph-door"></i></div>
        <div class="stat-info">
            <h4>Rombel Kelas</h4>
            <div class="value"><?php echo number_format($c_classes); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #FEF2F2; color: #DC2626;"><i class="ph-fill ph-user-plus"></i></div>
        <div class="stat-info">
            <h4>PPDB Pending</h4>
            <div class="value"><?php echo number_format($c_ppdb); ?></div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Quick Actions -->
    <div class="card">
        <h3 style="margin-bottom: 20px;">Aksi Cepat</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="?page=students&action=add" class="btn btn-soft"><i class="ph ph-plus" style="margin-right: 6px;"></i> Siswa Baru</a>
            <a href="?page=teachers&action=add" class="btn btn-soft"><i class="ph ph-plus" style="margin-right: 6px;"></i> Guru Baru</a>
            <a href="?page=enrollments" class="btn btn-soft"><i class="ph ph-users-three" style="margin-right: 6px;"></i> Plotting Kelas</a>
            <a href="?page=ppdb" class="btn btn-soft"><i class="ph ph-check-circle" style="margin-right: 6px;"></i> Cek PPDB</a>
        </div>
    </div>

    <!-- Recent Data -->
    <div class="card">
        <h3 style="margin-bottom: 15px;">Siswa Terbaru</h3>
        <?php if (!empty($recent_students)): ?>
        <ul style="list-style: none;">
            <?php foreach($recent_students as $rs): ?>
            <li style="padding: 10px 0; border-bottom: 1px solid #F1F5F9; font-size: 0.9rem; display: flex; justify-content: space-between;">
                <span style="font-weight: 500;"><?php echo htmlspecialchars($rs['full_name']); ?></span>
                <span style="color: #94A3B8; font-size: 0.8rem;"><?php echo date('d M', strtotime($rs['created_at'])); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
            <p style="color: #94A3B8;">Belum ada siswa.</p>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($role == 'teacher'): ?>
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;"><i class="ph-fill ph-chalkboard"></i></div>
        <div class="stat-info">
            <h4>Jadwal Mengajar</h4>
            <div class="value"><?php echo number_format($my_sections); ?> <span style="font-size: 0.9rem; font-weight: normal; color: #64748B;">Kelas</span></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #F0FDF4; color: #16A34A;"><i class="ph-fill ph-users-three"></i></div>
        <div class="stat-info">
            <h4>Total Siswa Ajar</h4>
            <div class="value"><?php echo number_format($my_students); ?></div>
        </div>
    </div>
    <?php if ($my_homeroom > 0): ?>
    <div class="stat-card">
        <div class="stat-icon" style="background: #FFF7ED; color: #EA580C;"><i class="ph-fill ph-star"></i></div>
        <div class="stat-info">
            <h4>Wali Kelas</h4>
            <div class="value"><?php echo $my_homeroom; ?> <span style="font-size: 0.9rem; font-weight: normal; color: #64748B;">Kelas</span></div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px;">Aksi Guru</h3>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="?page=grading" class="btn btn-soft"><i class="ph ph-exam" style="margin-right: 6px;"></i> Input Nilai</a>
        <a href="?page=attendance" class="btn btn-soft"><i class="ph ph-check-circle" style="margin-right: 6px;"></i> Absensi Harian</a>
        <a href="?page=schedule" class="btn btn-soft"><i class="ph ph-calendar" style="margin-right: 6px;"></i> Lihat Jadwal</a>
    </div>
</div>

<?php elseif ($role == 'student'): ?>
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;"><i class="ph-fill ph-exam"></i></div>
        <div class="stat-info">
            <h4>Nilai Masuk</h4>
            <div class="value"><?php echo number_format($my_grades_count); ?></div>
        </div>
    </div>
     <div class="stat-card">
        <div class="stat-icon" style="background: #F0FDF4; color: #16A34A;"><i class="ph-fill ph-check-circle"></i></div>
        <div class="stat-info">
            <h4>Kehadiran</h4>
            <div class="value">98% <span style="font-size: 0.8rem; color:#64748B;">Target</span></div>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px;">Menu Siswa</h3>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="?page=my_schedule" class="btn btn-soft"><i class="ph ph-calendar" style="margin-right: 6px;"></i> Jadwal Pelajaran</a>
        <a href="?page=my_grades" class="btn btn-soft"><i class="ph ph-exam" style="margin-right: 6px;"></i> Lihat KHS / Raport</a>
    </div>
</div>
<?php endif; ?>
