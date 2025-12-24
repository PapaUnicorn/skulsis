<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Data Siswa</h3>
        <div>
            <a href="?page=students&action=import" class="btn btn-outline" style="margin-right: 10px;"><i class="ph ph-upload-simple"></i> Import CSV</a>
            <a href="?page=students&action=add" class="btn btn-primary"><i class="ph ph-plus"></i> Tambah Siswa</a>
        </div>
    </div>

    <!-- Handling Actions View -->
    <?php
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    if ($action == 'import'): ?>
    <div style="max-width: 600px;">
        <h4 style="margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 10px;">Import Data Siswa (CSV)</h4>
        <div class="card" style="background: #F8FAFC; border: 1px dashed #94A3B8; box-shadow: none;">
            <p style="margin-bottom: 15px;">Silakan unduh template CSV di bawah ini, isi data dengan benar, lalu upload kembali.</p>
            <a href="../assets/templates/template_students.csv" download class="btn btn-outline btn-sm" style="margin-bottom: 20px;"><i class="ph ph-download-simple"></i> Download Template CSV</a>
            
            <form action="../modules/master/import_students.php" method="POST" enctype="multipart/form-data">
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Upload File CSV</label>
                    <input type="file" name="file_csv" accept=".csv" required style="width: 100%; padding: 10px; background: white; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="?page=students" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Proses Import</button>
                </div>
            </form>
        </div>
    </div>
    <?php elseif ($action == 'list'):
        // Fetch Data
        try {
            $stmt = $pdo->query("SELECT * FROM students ORDER BY full_name ASC LIMIT 50");
            $students = $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    ?>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
            <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                <tr>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">NISN / NIS</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Nama Lengkap</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">L/P</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Tempat, Tgl Lahir</th>
                    <th style="text-align: right; padding: 12px; font-weight: 600; color: #475569;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach($students as $student): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 12px; color: #64748B;">
                            <div style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($student['nisn']); ?></div>
                            <div style="font-size: 0.8rem;"><?php echo htmlspecialchars($student['nis']); ?></div>
                        </td>
                        <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars($student['full_name']); ?></td>
                         <td style="padding: 12px;"><?php echo htmlspecialchars($student['gender']); ?></td>
                        <td style="padding: 12px; color: #64748B;">
                            <?php echo htmlspecialchars($student['birth_place']) . ', ' . $student['birth_date']; ?>
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="?page=students&action=edit&id=<?php echo $student['id']; ?>" style="color: #F59E0B; margin-right: 10px;"><i class="ph ph-pencil-simple"></i> Edit</a>
                            <a href="../modules/master/student_delete.php?id=<?php echo $student['id']; ?>" onclick="return confirm('Yakin ingin menghapus siswa ini?');" style="color: #EF4444;"><i class="ph ph-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: #64748B;">Belum ada data siswa. Silakan tambah data baru.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php elseif ($action == 'add' || $action == 'edit'): 
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $edit_student = null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$id]);
            $edit_student = $stmt->fetch();
        }
    ?>
    
    <div style="max-width: 800px;">
        <h4 style="margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 10px;"><?php echo $action == 'add' ? 'Tambah Siswa Baru' : 'Edit Data Siswa'; ?></h4>
        
        <form action="../modules/master/student_save.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_student ? $edit_student['id'] : ''; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nama Lengkap</label>
                    <input type="text" name="full_name" required value="<?php echo $edit_student ? htmlspecialchars($edit_student['full_name']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Jenis Kelamin</label>
                    <select name="gender" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                        <option value="L" <?php echo ($edit_student && $edit_student['gender'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo ($edit_student && $edit_student['gender'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">NISN</label>
                    <input type="text" name="nisn" required value="<?php echo $edit_student ? htmlspecialchars($edit_student['nisn']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">NIS</label>
                    <input type="text" name="nis" value="<?php echo $edit_student ? htmlspecialchars($edit_student['nis']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tempat Lahir</label>
                    <input type="text" name="birth_place" required value="<?php echo $edit_student ? htmlspecialchars($edit_student['birth_place']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tanggal Lahir</label>
                    <input type="date" name="birth_date" required value="<?php echo $edit_student ? $edit_student['birth_date'] : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
            </div>
            
             <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">No. Telepon Orang Tua</label>
                <input type="text" name="parent_phone" value="<?php echo $edit_student ? htmlspecialchars($edit_student['parent_phone']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Alamat Lengkap</label>
                <textarea name="address" rows="3" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;"><?php echo $edit_student ? htmlspecialchars($edit_student['address']) : ''; ?></textarea>
            </div>

            <?php if ($action == 'add'): ?>
            <div style="background: #F8FAFC; padding: 20px; border-radius: 12px; border: 1px dashed #CBD5E1; margin-bottom: 30px;">
                <h5 style="margin-bottom: 15px; color: var(--accent-color); font-weight: 600;">Buat Akun Login</h5>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email</label>
                        <input type="email" name="user_email" required placeholder="siswa@sekolah.com" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Username</label>
                        <input type="text" name="user_username" required placeholder="User Login" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Password</label>
                        <input type="password" name="user_password" required placeholder="******" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div style="display: flex; gap: 10px;">
                <a href="?page=students" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>

    <?php endif; ?>

</div>
