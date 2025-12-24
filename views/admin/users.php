<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Manajemen Pengguna (User)</h3>
        <a href="?page=users&action=add" class="btn btn-primary"><i class="ph ph-plus"></i> Tambah User</a>
    </div>

    <!-- Handling Actions View -->
    <?php
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    if ($action == 'list'):
        // Fetch Data
        try {
            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
            $users = $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    ?>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                <tr>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Username</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Email</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Role</th>
                    <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Status</th>
                    <th style="text-align: right; padding: 12px; font-weight: 600; color: #475569;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach($users as $user): ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars($user['username']); ?></td>
                        <td style="padding: 12px; color: #64748B;"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="padding: 12px;">
                            <span style="background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 99px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                                <?php echo htmlspecialchars($user['role']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px;">
                             <?php if ($user['is_active']): ?>
                                <span style="color: #10B981; font-weight: 600; display: flex; align-items: center; gap: 4px;"><i class="ph-fill ph-check-circle"></i> Aktif</span>
                             <?php else: ?>
                                <span style="color: #EF4444; font-weight: 600; display: flex; align-items: center; gap: 4px;"><i class="ph-fill ph-x-circle"></i> Non-Aktif</span>
                             <?php endif; ?>
                        </td>
                        <td style="padding: 12px; text-align: right;">
                            <a href="?page=users&action=edit&id=<?php echo $user['id']; ?>" style="color: #F59E0B; margin-right: 10px;"><i class="ph ph-pencil-simple"></i> Edit</a>
                            <?php if ($user['username'] !== 'admin'): // Prevent deleting the main admin ?>
                            <a href="../modules/master/user_delete.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Yakin ingin menghapus user ini?');" style="color: #EF4444;"><i class="ph ph-trash"></i> Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: #64748B;">Belum ada data user.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php elseif ($action == 'add' || $action == 'edit'): 
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $edit_user = null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $edit_user = $stmt->fetch();
        }
    ?>
    
    <div style="max-width: 600px;">
        <h4 style="margin-bottom: 20px; border-bottom: 1px solid #E2E8F0; padding-bottom: 10px;"><?php echo $action == 'add' ? 'Tambah User Baru' : 'Edit User'; ?></h4>
        
        <form action="../modules/master/user_save.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_user ? $edit_user['id'] : ''; ?>">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Username</label>
                <input type="text" name="username" required value="<?php echo $edit_user ? htmlspecialchars($edit_user['username']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Email</label>
                <input type="email" name="email" required value="<?php echo $edit_user ? htmlspecialchars($edit_user['email']) : ''; ?>" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Password <?php echo $edit_user ? '(Kosongkan jika tidak ingin mengubah)' : ''; ?></label>
                <input type="password" name="password" <?php echo $edit_user ? '' : 'required'; ?> style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Role</label>
                    <select name="role" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                        <option value="admin" <?php echo ($edit_user && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="teacher" <?php echo ($edit_user && $edit_user['role'] == 'teacher') ? 'selected' : ''; ?>>Guru</option>
                        <option value="student" <?php echo ($edit_user && $edit_user['role'] == 'student') ? 'selected' : ''; ?>>Siswa</option>
                        <option value="parent" <?php echo ($edit_user && $edit_user['role'] == 'parent') ? 'selected' : ''; ?>>Orang Tua</option>
                        <option value="librarian" <?php echo ($edit_user && $edit_user['role'] == 'librarian') ? 'selected' : ''; ?>>Pustakawan</option>
                    </select>
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Status Akun</label>
                    <select name="is_active" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px; background: white;">
                        <option value="1" <?php echo ($edit_user && $edit_user['is_active'] == 1) ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?php echo ($edit_user && $edit_user['is_active'] == 0) ? 'selected' : ''; ?>>Non-Aktif</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <a href="?page=users" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan User</button>
            </div>
        </form>
    </div>

    <?php endif; ?>

</div>
