<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Penerimaan Peserta Didik Baru (PPDB)</h3>
        <div class="badge" style="background: #EFF6FF; color: #2563EB; font-size: 0.9rem;">
            Tahun Ajaran: 2024/2025
        </div>
    </div>

    <?php
    // Handle Actions (Accept/Reject)
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $reg_id = $_GET['id'];
        if ($_GET['action'] == 'accept') {
            // Logic to move to students table handled in specific file to keep view clean, 
            // or we can do a simple toggle here if logic is simple. 
            // Better to link to a processing script.
            echo "<script>window.location.href='../modules/ppdb/process.php?action=accept&id=$reg_id';</script>";
        } elseif ($_GET['action'] == 'reject') {
            echo "<script>window.location.href='../modules/ppdb/process.php?action=reject&id=$reg_id';</script>";
        }
    }

    // Fetch Registrations
    try {
        $stmt = $pdo->query("SELECT * FROM ppdb_registrations ORDER BY created_at DESC");
        $registrations = $stmt->fetchAll();
    } catch (PDOException $e) {
        $registrations = [];
    }
    ?>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                <tr>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Tanggal</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Nama Calon Siswa</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">NISN / Asal Sekolah</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Ortu / Wali</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Status</th>
                    <th style="text-align: right; padding: 12px; font-weight: 600; color: #475569;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($registrations) > 0): ?>
                    <?php foreach($registrations as $reg): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 12px; color: #64748B; font-size: 0.9rem;">
                            <?php echo date('d M Y', strtotime($reg['created_at'])); ?>
                        </td>
                        <td style="padding: 12px; font-weight: 500;">
                            <?php echo htmlspecialchars($reg['full_name']); ?>
                            <br>
                            <span style="font-size: 0.8rem; color: #64748B;"><?php echo $reg['gender'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
                        </td>
                        <td style="padding: 12px;">
                            <div style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($reg['nisn']); ?></div>
                            <div style="font-size: 0.8rem; color: #64748B;"><?php echo htmlspecialchars($reg['origin_school']); ?></div>
                        </td>
                        <td style="padding: 12px; color: #64748B;">
                            <div><?php echo htmlspecialchars($reg['father_name']); ?> (Ayah)</div>
                            <div style="font-size: 0.8rem;"><i class="ph-fill ph-phone"></i> <?php echo htmlspecialchars($reg['parent_phone']); ?></div>
                        </td>
                        <td style="padding: 12px;">
                            <?php
                            $status_colors = [
                                'Pending' => ['bg' => '#FFF7ED', 'text' => '#EA580C'],
                                'Verified' => ['bg' => '#EFF6FF', 'text' => '#2563EB'],
                                'Accepted' => ['bg' => '#F0FDF4', 'text' => '#16A34A'],
                                'Rejected' => ['bg' => '#FEF2F2', 'text' => '#DC2626'],
                            ];
                            $st = $reg['status'];
                            $col = isset($status_colors[$st]) ? $status_colors[$st] : $status_colors['Pending'];
                            ?>
                            <span style="background: <?php echo $col['bg']; ?>; color: <?php echo $col['text']; ?>; padding: 4px 10px; border-radius: 99px; font-size: 0.8rem; font-weight: 600;">
                                <?php echo htmlspecialchars($st); ?>
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <!-- View Files (Simple link for now) -->
                           <div style="margin-bottom: 5px;">
                                <?php if($reg['file_kk']): ?> <a href="../<?php echo $reg['file_kk']; ?>" target="_blank" style="font-size: 0.8rem; color: var(--accent-color);">[KK]</a> <?php endif; ?>
                                <?php if($reg['file_akte']): ?> <a href="../<?php echo $reg['file_akte']; ?>" target="_blank" style="font-size: 0.8rem; color: var(--accent-color);">[Akte]</a> <?php endif; ?>
                           </div>

                           <?php if ($reg['status'] == 'Pending'): ?>
                                <a href="?page=ppdb&action=accept&id=<?php echo $reg['id']; ?>" onclick="return confirm('Terima siswa ini? Data akan dipindahkan ke Data Siswa.');" class="btn btn-outline btn-sm" style="color: #16A34A; border-color: #16A34A; padding: 4px 8px; font-size: 0.8rem;"><i class="ph ph-check"></i> Terima</a>
                                <a href="?page=ppdb&action=reject&id=<?php echo $reg['id']; ?>" onclick="return confirm('Tolak pendaftaran ini?');" class="btn btn-outline btn-sm" style="color: #EF4444; border-color: #EF4444; padding: 4px 8px; font-size: 0.8rem;"><i class="ph ph-x"></i> Tolak</a>
                           <?php else: ?>
                                <span style="color: #94A3B8; font-size: 0.8rem;">Selesai</span>
                           <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding: 24px; text-align: center; color: #64748B;">Belum ada data pendaftaran PPDB.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
