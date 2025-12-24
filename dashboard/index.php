<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?page=login");
    exit();
}

// User Data
$user_role = $_SESSION['role'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Skulsis</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Dashboard CSS -->
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 64px;
            --primary: #0F172A;
            --accent: #3B82F6;
            --bg-body: #F1F5F9;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            display: flex;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            border-right: 1px solid #E2E8F0;
            display: flex;
            flex-direction: column;
            z-index: 50;
        }
        
        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid #E2E8F0;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary);
        }
        
        .sidebar-menu {
            padding: 24px 16px;
            flex: 1;
            overflow-y: auto;
        }
        
        .menu-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #94A3B8;
            font-weight: 600;
            margin-bottom: 10px;
            margin-top: 20px;
            padding-left: 12px;
        }
        
        .menu-label:first-child { margin-top: 0; }
        
        .nav-item {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            color: #64748B;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s;
            font-weight: 500;
        }
        
        .nav-item:hover, .nav-item.active {
            background-color: #EFF6FF;
            color: var(--accent);
        }
        
        .nav-item i {
            margin-right: 12px;
            font-size: 1.25rem;
        }
        
        /* Main Content */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .top-header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .avatar {
            width: 36px;
            height: 36px;
            background-color: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .content {
            padding: 32px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: #EFF6FF;
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-info h4 {
            font-size: 0.875rem;
            color: #64748B;
            font-weight: 500;
        }
        
        .stat-info .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
    </style>
</head>
<body>

    <!-- Sidebar Include -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="ph-fill ph-graduation-cap" style="margin-right: 10px; color: var(--accent);"></i>
            Skulsis Pro
        </div>
        
        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="?page=home" class="nav-item <?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : ''; ?>">
                <i class="ph ph-squares-four"></i> Dashboard
            </a>
            <?php if ($user_role == 'admin'): ?>
            <a href="?page=ppdb" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'ppdb') ? 'active' : ''; ?>">
                <i class="ph ph-user-plus"></i> PPDB Online
            </a>
            <?php endif; ?>
            
            <?php if ($user_role == 'admin'): ?>
            <div class="menu-label">Data Master</div>
            <a href="?page=users" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'users') ? 'active' : ''; ?>"><i class="ph ph-users"></i> Pengguna</a>
            <a href="?page=teachers" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'teachers') ? 'active' : ''; ?>"><i class="ph ph-chalkboard-teacher"></i> Guru & Tendik</a>
            <a href="?page=students" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'students') ? 'active' : ''; ?>"><i class="ph ph-student"></i> Siswa</a>
            <a href="?page=subjects" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'subjects') ? 'active' : ''; ?>"><i class="ph ph-books"></i> Mata Pelajaran</a>
            <a href="?page=classes" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'classes') ? 'active' : ''; ?>"><i class="ph ph-door"></i> Kelas</a>
            <?php endif; ?>
            
            <?php if ($user_role == 'teacher' || $user_role == 'admin'): ?>
            <div class="menu-label">Akademik</div>
            <a href="?page=enrollments" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'enrollments') ? 'active' : ''; ?>"><i class="ph ph-users-three"></i> Plotting Kelas</a>
            <a href="?page=schedule" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'schedule') ? 'active' : ''; ?>"><i class="ph ph-calendar"></i> Jadwal Mengajar</a>
            <a href="?page=grading" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'grading') ? 'active' : ''; ?>"><i class="ph ph-exam"></i> Input Nilai</a>
            <a href="?page=attendance" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'attendance') ? 'active' : ''; ?>"><i class="ph ph-check-circle"></i> Absensi</a>
            <?php endif; ?>

            <?php if ($user_role == 'student'): ?>
            <div class="menu-label">Akademik Saya</div>
            <a href="?page=my_schedule" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'my_schedule') ? 'active' : ''; ?>"><i class="ph ph-calendar"></i> Jadwal Pelajaran</a>
            <a href="?page=my_grades" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'my_grades') ? 'active' : ''; ?>"><i class="ph ph-exam"></i> Lihat Nilai</a>
            <?php endif; ?>

            <div class="menu-label">Kesiswaan & Sarpras</div>
            <a href="?page=counseling" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'counseling') ? 'active' : ''; ?>"><i class="ph ph-heart-beat"></i> Konseling (BK)</a>
            <a href="?page=library" class="nav-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'library') ? 'active' : ''; ?>"><i class="ph ph-book-open"></i> Perpustakaan</a>
            
            <div class="menu-label">Pengaturan</div>
            <a href="../modules/auth/logout.php" class="nav-item" style="color: #EF4444;">
                <i class="ph ph-sign-out"></i> Logout
            </a>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="top-header">
            <h2 style="font-size: 1.25rem; font-weight: 600;">Dashboard <?php echo ucfirst($user_role); ?></h2>
            <div class="user-profile">
                <div style="text-align: right;">
                    <div style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($username); ?></div>
                    <div style="font-size: 0.8rem; color: #64748B;"><?php echo ucfirst($user_role); ?></div>
                </div>
                <div class="avatar">
                    <?php echo strtoupper(substr($username, 0, 1)); ?>
                </div>
            </div>
        </header>
        
        <main class="content">
            <?php
            // Alert System
            if (isset($_GET['status']) && $_GET['status'] == 'success') {
                echo '<div style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #16A34A; padding: 15px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center;"><i class="ph-fill ph-check-circle" style="font-size: 1.2rem; margin-right: 10px;"></i> Berhasil menyimpan data.</div>';
            }
            if (isset($_GET['status']) && $_GET['status'] == 'deleted') {
                echo '<div style="background: #FFF7ED; border: 1px solid #FFEDD5; color: #EA580C; padding: 15px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center;"><i class="ph-fill ph-trash" style="font-size: 1.2rem; margin-right: 10px;"></i> Data telah dihapus.</div>';
            }
            if (isset($_GET['error'])) {
                $err = htmlspecialchars($_GET['error']);
                echo "<div style='background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; padding: 15px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center;'><i class='ph-fill ph-warning-circle' style='font-size: 1.2rem; margin-right: 10px;'></i> Terjadi kesalahan: $err</div>";
            }

            // Simple Router for Dashboard
            $page = isset($_GET['page']) ? $_GET['page'] : 'home';
            $allowed_pages = ['home', 'users', 'teachers', 'students', 'subjects', 'classes', 'schedule', 'grading', 'attendance', 'enrollments', 'counseling', 'library', 'ppdb', 'my_schedule', 'my_grades'];
            
            if (in_array($page, $allowed_pages)) {
                $view_file = "../views/admin/{$page}.php";
                if (file_exists($view_file)) {
                    include $view_file;
                } else {
                     echo "<div class='card'><h3>Halaman tidak ditemukan</h3><p>File view belum tersedia.</p></div>";
                }
            } else {
                 echo "<div class='card'><h3>Halaman tidak ditemukan</h3></div>";
            }
            ?>
        </main>
    </div>

</body>
</html>
