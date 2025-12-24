<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Data Mata Pelajaran</h3>
        <a href="?page=subjects&action=add" class="btn btn-primary"><i class="ph ph-plus"></i> Tambah Mapel</a>
    </div>

    <!-- Handling Actions View -->
    <?php
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    if ($action == 'list'):
        // Fetch Data
        try {
            $sql = "SELECT s.*, cl.name as level_name, m.name as major_name, t.full_name as teacher_name 
                    FROM subjects s 
                    LEFT JOIN class_levels cl ON s.class_level_id = cl.id 
                    LEFT JOIN majors m ON s.major_id = m.id 
                    LEFT JOIN teachers t ON s.teacher_id = t.id
                    ORDER BY s.code ASC";
            $stmt = $pdo->query($sql);
            $subjects = $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    ?>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                <tr>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Kode</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Nama Mata Pelajaran</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Tingkat</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Jurusan</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Guru</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Kelompok</th>
                    <th style="text-align: right; padding: 12px; font-weight: 600; color: #475569;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($subjects) > 0): ?>
                    <?php foreach($subjects as $subject): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 12px; font-weight: 600; color: #475569;"><?php echo htmlspecialchars($subject['code']); ?></td>
                        <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars($subject['name']); ?></td>
                        <td style="padding: 12px; font-size: 0.9rem; color: #64748B;"><?php echo htmlspecialchars($subject['level_name'] ?? '-'); ?></td>
                        <td style="padding: 12px; font-size: 0.9rem; color: #64748B;"><?php echo htmlspecialchars($subject['major_name'] ?? 'Umum'); ?></td>
                        <td style="padding: 12px; font-size: 0.9rem; color: var(--primary);">
                            <?php echo $subject['teacher_name'] ? htmlspecialchars($subject['teacher_name']) : '<span style="font-style:italic; color:#cbd5e1;">-</span>'; ?>
                        </td>
                        <td style="padding: 12px;">
                            <span style="background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 99px; font-size: 0.8rem; font-weight: 600;">
                                <?php echo htmlspecialchars($subject['type']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="?page=subjects&action=edit&id=<?php echo $subject['id']; ?>" style="color: #F59E0B; margin-right: 10px;"><i class="ph ph-pencil-simple"></i> Edit</a>
                            <a href="../modules/master/subject_delete.php?id=<?php echo $subject['id']; ?>" onclick="return confirm('Yakin ingin menghapus mapel ini?');" style="color: #EF4444;"><i class="ph ph-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="padding: 24px; text-align: center; color: #64748B;">Belum ada data mata pelajaran.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php elseif ($action == 'add' || $action == 'edit'): 
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $edit_subject = null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
            $stmt->execute([$id]);
            $edit_subject = $stmt->fetch();
        }
    ?>
    
    <div style="max-width: 600px;">
        <h4 style="margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 10px;"><?php echo $action == 'add' ? 'Tambah Mapel Baru' : 'Edit Mata Pelajaran'; ?></h4>
        
        <form action="../modules/master/subject_save.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_subject ? $edit_subject['id'] : ''; ?>">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Kode Mapel</label>
                <input type="text" name="code" required value="<?php echo $edit_subject ? htmlspecialchars($edit_subject['code']) : ''; ?>" placeholder="Contoh: MTK-W, BING-P" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nama Mata Pelajaran</label>
                <input type="text" name="name" required value="<?php echo $edit_subject ? htmlspecialchars($edit_subject['name']) : ''; ?>" placeholder="Contoh: Matematika Wajib" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>
            
            <?php
            // Fetch Options
            try {
                $levels = $pdo->query("SELECT * FROM class_levels ORDER BY id ASC")->fetchAll();
                $majors = $pdo->query("SELECT * FROM majors ORDER BY name ASC")->fetchAll();
            } catch (PDOException $e) { $levels=[]; $majors=[]; }
            ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tingkat (Level)</label>
                    <select name="class_level_id" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                        <option value="">-- Pilih Tingkat --</option>
                        <?php foreach($levels as $lvl): ?>
                        <option value="<?php echo $lvl['id']; ?>" <?php echo ($edit_subject && $edit_subject['class_level_id'] == $lvl['id']) ? 'selected' : ''; ?>>
                            <?php echo $lvl['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                     <label style="display: block; margin-bottom: 8px; font-weight: 500;">Jurusan (Opsional)</label>
                    <select name="major_id" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                        <option value="">-- Semua Jurusan / Umum --</option>
                        <?php foreach($majors as $mjr): ?>
                        <option value="<?php echo $mjr['id']; ?>" <?php echo ($edit_subject && $edit_subject['major_id'] == $mjr['id']) ? 'selected' : ''; ?>>
                            <?php echo $mjr['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Guru Pengampu (Default)</label>
                <select name="teacher_id" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                    <option value="">-- Pilih Guru --</option>
                    <?php 
                    // Re-use or fetch teachers if not available in current scope (it wasn't fetched in previous block)
                    if (!isset($teachers_list)) { 
                        $teachers_list = $pdo->query("SELECT * FROM teachers ORDER BY full_name ASC")->fetchAll(); 
                    }
                    foreach($teachers_list as $tc): 
                    ?>
                    <option value="<?php echo $tc['id']; ?>" <?php echo ($edit_subject && $edit_subject['teacher_id'] == $tc['id']) ? 'selected' : ''; ?>>
                        <?php echo $tc['full_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Kelompok / Tipe</label>
                <select name="type" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                    <option value="Muatan Nasional" <?php echo ($edit_subject && $edit_subject['type'] == 'Muatan Nasional') ? 'selected' : ''; ?>>Muatan Nasional</option>
                    <option value="Muatan Lokal" <?php echo ($edit_subject && $edit_subject['type'] == 'Muatan Lokal') ? 'selected' : ''; ?>>Muatan Lokal</option>
                    <option value="Peminatan" <?php echo ($edit_subject && $edit_subject['type'] == 'Peminatan') ? 'selected' : ''; ?>>Peminatan (Kejuruan)</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <a href="?page=subjects" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>

    <?php endif; ?>

</div>
