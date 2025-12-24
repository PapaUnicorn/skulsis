<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Bimbingan Konseling (BK)</h3>
        <a href="?page=counseling&action=add_violation" class="btn btn-primary"><i class="ph ph-warning"></i> Catat Pelanggaran</a>
    </div>

    <!-- Tables for Violations -->
    <h4 style="margin-bottom: 15px; margin-top: 10px;">Riwayat Pelanggaran & Konseling</h4>
    
    <?php
    // Fetch Violations
    try {
        $sql = "SELECT v.*, s.full_name, s.nis, s.nisn 
                FROM violations v 
                JOIN students s ON v.student_id = s.id 
                ORDER BY v.violation_date DESC LIMIT 20";
        $violations = $pdo->query($sql)->fetchAll();
    } catch (PDOException $e) {
        $violations = [];
    }
    ?>
    
    <div style="overflow-x: auto; margin-bottom: 40px;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                <tr>
                    <th style="padding: 12px; text-align: left;">Tanggal</th>
                    <th style="padding: 12px; text-align: left;">Nama Siswa</th>
                    <th style="padding: 12px; text-align: left;">Jenis Pelanggaran</th>
                    <th style="padding: 12px; text-align: center;">Poin</th>
                    <th style="padding: 12px; text-align: left;">Sanksi</th>
                </tr>
            </thead>
            <tbody>
                 <?php if (count($violations) > 0): ?>
                    <?php foreach($violations as $v): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 12px;"><?php echo $v['violation_date']; ?></td>
                        <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars($v['full_name']); ?></td>
                         <td style="padding: 12px;"><span style="color: #EF4444;"><?php echo htmlspecialchars($v['violation_type']); ?></span></td>
                        <td style="padding: 12px; text-align: center; font-weight: bold;"><?php echo $v['points']; ?></td>
                        <td style="padding: 12px; color: #64748B;"><?php echo htmlspecialchars($v['sanction']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                     <tr><td colspan="5" style="padding: 20px; text-align: center;">Belum ada data pelanggaran</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Add Violation Form -->
    <?php if (isset($_GET['action']) && $_GET['action'] == 'add_violation'): ?>
    <div style="background: #FFF; border: 1px solid #E2E8F0; padding: 20px; border-radius: 8px;">
        <h4 style="margin-bottom: 20px;">Input Pelanggaran Siswa</h4>
        <form action="../modules/student_affairs/violation_save.php" method="POST">
             <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Cari Siswa</label>
                <select name="student_id" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                   <option value="">-- Pilih Siswa --</option>
                   <?php
                   // Load all students
                   $alls = $pdo->query("SELECT id, full_name, nis FROM students ORDER BY full_name ASC")->fetchAll();
                   foreach($alls as $s) {
                       echo "<option value='{$s['id']}'>{$s['full_name']} ({$s['nis']})</option>";
                   }
                   ?>
                </select>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tanggal Kejadian</label>
                <input type="date" name="violation_date" required value="<?php echo date('Y-m-d'); ?>" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>

             <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Jenis Pelanggaran</label>
                    <input type="text" name="violation_type" required placeholder="Contoh: Terlambat, Merokok, Bolos" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Poin Pelanggaran</label>
                    <input type="number" name="points" required value="5" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Sanksi / Tindak Lanjut</label>
                <textarea name="sanction" rows="3" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;"></textarea>
            </div>
            
             <div style="display: flex; gap: 10px;">
                <a href="?page=counseling" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

</div>
