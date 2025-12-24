<?php
// Simple Router Logic for Phase 1
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skulsis - Sistem Informasi Sekolah Terpadu</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Icons (Phosphor Icons for modern look) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

    <!-- Header -->
    <header class="main-header">
        <div class="container navbar">
            <a href="index.php" class="brand-logo">
                <i class="ph-fill ph-graduation-cap"></i>
                Skulsis
            </a>
            <nav>
                <ul class="nav-menu">
                    <li><a href="?page=home" class="nav-link <?php echo $page == 'home' ? 'active' : ''; ?>">Beranda</a></li>
                    <li><a href="?page=news" class="nav-link <?php echo $page == 'news' ? 'active' : ''; ?>">Informasi</a></li>
                    <li><a href="?page=ppdb" class="nav-link <?php echo $page == 'ppdb' ? 'active' : ''; ?>">PPDB Online</a></li>
                    <li><a href="?page=gallery" class="nav-link <?php echo $page == 'gallery' ? 'active' : ''; ?>">Galeri</a></li>
                    <li><a href="?page=login" class="btn btn-primary">Login Portal</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <?php
        $view_path = "views/front/{$page}.php";
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            // Default to home content inline if file doesn't exist yet, or 404
            if ($page === 'home') {
                ?>
                <section class="hero container">
                    <div class="hero-content">
                        <h1 class="hero-title">Mewujudkan Pendidikan Berkualitas Digital</h1>
                        <p class="hero-subtitle">Sistem Informasi Sekolah terintegrasi untuk manajemen akademik, kesiswaan, dan sarana prasarana yang lebih efisien dan transparan.</p>
                        <div class="hero-actions">
                            <a href="?page=ppdb" class="btn btn-primary">Daftar Sekarang (PPDB)</a>
                            <a href="#features" class="btn btn-outline" style="margin-left: 10px;">Pelajari Lebih Lanjut</a>
                        </div>
                    </div>
                    <div class="hero-image">
                       <!-- Placeholder for Hero Image -->
                       <div class="glass-card" style="width: 400px; height: 300px; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                            <i class="ph ph-image" style="font-size: 4rem;"></i>
                       </div>
                    </div>
                </section>

                <section id="features" class="container">
                    <div class="features-grid">
                        <div class="feature-card">
                            <i class="ph ph-chalkboard-teacher feature-icon"></i>
                            <h3 class="feature-title">Manajemen Akademik</h3>
                            <p class="feature-desc">Kelola jadwal pelajaran, nilai, dan absensi siswa dengan mudah dan terstruktur.</p>
                        </div>
                        <div class="feature-card">
                            <i class="ph ph-student feature-icon"></i>
                            <h3 class="feature-title">Portal Siswa & Ortu</h3>
                            <p class="feature-desc">Akses nilai, jadwal, dan informasi sekolah secara real-time dari mana saja.</p>
                        </div>
                        <div class="feature-card">
                            <i class="ph ph-files feature-icon"></i>
                            <h3 class="feature-title">PPDB Digital</h3>
                            <p class="feature-desc">Proses pendaftaran siswa baru yang paperless, cepat, dan transparan.</p>
                        </div>
                    </div>
                </section>
                <?php
            } elseif ($page === 'login') {
                // Quick Login View
                ?>
                <section class="container" style="display: flex; justify-content: center; padding: 100px 0;">
                    <div class="glass-card" style="width: 100%; max-width: 400px; padding: 40px;">
                        <h2 style="text-align: center; margin-bottom: 30px;">Login Portal</h2>
                        <form action="modules/auth/login.php" method="POST">
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Username / NIS / NIP</label>
                                <input type="text" name="username" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" placeholder="Masukkan ID Pengguna">
                            </div>
                            <div style="margin-bottom: 30px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Password</label>
                                <input type="password" name="password" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" placeholder="Masukkan Password">
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk Sekarang</button>
                        </form>
                    </div>
                </section>
                <?php
            } else {
                echo "<div class='container' style='padding: 100px 0; text-align: center;'><h2>Halaman tidak ditemukan</h2></div>";
            }
        }
        ?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Skulsis</h3>
                    <p style="color: #94A3B8;">Solusi digital manajemen sekolah masa kini.</p>
                </div>
                <div class="footer-section">
                    <h3>Tautan Cepat</h3>
                    <ul class="footer-links">
                        <li><a href="?page=home">Beranda</a></li>
                        <li><a href="?page=ppdb">PPDB</a></li>
                        <li><a href="?page=news">Berita</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Hubungi Kami</h3>
                    <ul class="footer-links">
                        <li>Telp: (021) 1234-5678</li>
                        <li>Email: info@sekolah.sch.id</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> Sistem Informasi Sekolah Skulsis. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
