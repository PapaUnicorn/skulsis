<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Data Kelas (Rombongan Belajar)</h3>
        <a href="?page=classes&action=add" class="btn btn-primary"><i class="ph ph-plus"></i> Tambah Kelas</a>
    </div>

    <!-- Handling Actions View -->
    <?php
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    if ($action == 'list'):
        // Fetch Data with Joins to get Level name and Academic Year name
        try {
            // Note: In real app, we need to join with academic_years and class_levels. 
            // For now, assuming basic fetch or simple joins if tables are populated.
            // Let's create a simpler query assuming IDs are enough or join if those tables exist in the schema (they do).
            $sql = "SELECT c.*, t.full_name as walikelas_name, cl.name as level_name, ay.name as year_name 
                    FROM classes c 
                    LEFT JOIN teachers t ON c.homeroom_teacher_id = t.id
                    LEFT JOIN class_levels cl ON c.class_level_id = cl.id
                    LEFT JOIN academic_years ay ON c.academic_year_id = ay.id
                    ORDER BY c.name ASC";
            $stmt = $pdo->query($sql);
            $classes = $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            $classes = [];
        }
    ?>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
            <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                <tr>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Nama Kelas</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Tingkat</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Tahun Ajaran</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Wali Kelas</th>
                    <th style="text-align: right; padding: 12px; font-weight: 600; color: #475569;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($classes) > 0): ?>
                    <?php foreach($classes as $class): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 12px; font-weight: 600;"><?php echo htmlspecialchars($class['name']); ?></td>
                        <td style="padding: 12px; color: #64748B;"><?php echo htmlspecialchars($class['level_name']); ?></td>
                        <td style="padding: 12px; color: #64748B;"><?php echo htmlspecialchars($class['year_name']); ?></td>
                        <td style="padding: 12px; font-weight: 500; color: var(--primary);">
                            <?php echo $class['walikelas_name'] ? htmlspecialchars($class['walikelas_name']) : '<span style="color:#cbd5e1; font-style:italic;">Belum diatur</span>'; ?>
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="?page=classes&action=edit&id=<?php echo $class['id']; ?>" style="color: #F59E0B; margin-right: 10px;"><i class="ph ph-pencil-simple"></i> Edit</a>
                            <a href="../modules/master/class_delete.php?id=<?php echo $class['id']; ?>" onclick="return confirm('Yakin ingin menghapus kelas ini?');" style="color: #EF4444;"><i class="ph ph-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: #64748B;">Belum ada data kelas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php elseif ($action == 'add' || $action == 'edit'): 
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $edit_class = null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
            $stmt->execute([$id]);
            $edit_class = $stmt->fetch();
        }

        // Get Options
        try {
            $levels = $pdo->query("SELECT * FROM class_levels ORDER BY id ASC")->fetchAll();
            $teachers = $pdo->query("SELECT * FROM teachers ORDER BY full_name ASC")->fetchAll();
            $years = $pdo->query("SELECT * FROM academic_years WHERE status='active' ORDER BY id DESC")->fetchAll(); // Only active years usually
            // Fallback if no active year
            if (empty($years)) $years = $pdo->query("SELECT * FROM academic_years ORDER BY id DESC LIMIT 5")->fetchAll();
        } catch (PDOException $e) {
            $levels = []; $teachers = []; $years = [];
        }
    ?>
    
    <div style="max-width: 600px;">
        <h4 style="margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 10px;"><?php echo $action == 'add' ? 'Tambah Kelas Baru' : 'Edit Data Kelas'; ?></h4>
        
        <form action="../modules/master/class_save.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_class ? $edit_class['id'] : ''; ?>">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nama Kelas</label>
                <input type="text" name="name" required value="<?php echo $edit_class ? htmlspecialchars($edit_class['name']) : ''; ?>" placeholder="Contoh: X IPA 1" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tingkat</label>
                    <select name="class_level_id" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                        <option value="">Pilih Tingkat</option>
                        <?php foreach($levels as $lvl): ?>
                        <option value="<?php echo $lvl['id']; ?>" <?php echo ($edit_class && $edit_class['class_level_id'] == $lvl['id']) ? 'selected' : ''; ?>>
                            <?php echo $lvl['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tahun Ajaran</label>
                    <select name="academic_year_id" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                         <?php foreach($years as $year): ?>
                        <option value="<?php echo $year['id']; ?>" <?php echo ($edit_class && $edit_class['academic_year_id'] == $year['id']) ? 'selected' : ''; ?>>
                            <?php echo $year['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Wali Kelas</label>
                <select name="homeroom_teacher_id" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                    <option value="">-- Pilih Wali Kelas --</option>
                    <?php foreach($teachers as $t): ?>
                    <option value="<?php echo $t['id']; ?>" <?php echo ($edit_class && $edit_class['homeroom_teacher_id'] == $t['id']) ? 'selected' : ''; ?>>
                        <?php echo $t['full_name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <a href="?page=classes" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>

    <?php endif; ?>

</div>
