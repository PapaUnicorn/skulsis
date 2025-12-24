<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Perpustakaan (E-Library)</h3>
        <a href="?page=library&action=add_book" class="btn btn-primary"><i class="ph ph-plus"></i> Tambah Buku</a>
    </div>

    <!-- Book List -->
    <?php
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    if ($action == 'list'):
        try {
            $books = $pdo->query("SELECT * FROM books ORDER BY title ASC LIMIT 50")->fetchAll();
        } catch (PDOException $e) { $books = []; }
    ?>
    
     <div style="overflow-x: auto; margin-bottom: 40px;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                <tr>
                    <th style="padding: 12px; text-align: left;">Kode / ISBN</th>
                    <th style="padding: 12px; text-align: left;">Judul Buku</th>
                    <th style="padding: 12px; text-align: left;">Pengarang</th>
                    <th style="padding: 12px; text-align: center;">Stok</th>
                    <th style="padding: 12px; text-align: left;">Rak</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($books as $b): ?>
                <tr style="border-bottom: 1px solid #E2E8F0;">
                    <td style="padding: 12px; color: #64748B;">
                        <div style="font-weight: 600; color: var(--primary);"><?php echo htmlspecialchars($b['code']); ?></div>
                        <div style="font-size: 0.8rem;"><?php echo htmlspecialchars($b['isbn']); ?></div>
                    </td>
                    <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars($b['title']); ?></td>
                    <td style="padding: 12px;"><?php echo htmlspecialchars($b['author']); ?></td>
                    <td style="padding: 12px; text-align: center;"><?php echo $b['stock']; ?></td>
                    <td style="padding: 12px; color: #64748B;"><?php echo htmlspecialchars($b['shelf_location']); ?></td>
                </tr>
                <?php endforeach; ?>
                 <?php if(empty($books)): ?>
                     <tr><td colspan="5" style="padding: 20px; text-align: center;">Belum ada data buku</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php elseif ($action == 'add_book'): ?>
    <div style="max-width: 600px;">
        <h4 style="margin-bottom: 20px;">Tambah Buku Baru</h4>
        <form action="../modules/library/book_save.php" method="POST">
             <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Kode Buku</label>
                    <input type="text" name="code" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">ISBN</label>
                    <input type="text" name="isbn" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
            </div>
            
             <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Judul Buku</label>
                <input type="text" name="title" required style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Pengarang</label>
                <input type="text" name="author" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>
            
             <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Penerbit</label>
                    <input type="text" name="publisher" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Stok</label>
                    <input type="number" name="stock" value="1" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
                </div>
            </div>
            
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500;">Lokasi Rak</label>
                <input type="text" name="shelf_location" placeholder="Misal: Rak A-2" style="width: 100%; padding: 10px; border: 1px solid #CBD5E1; border-radius: 8px;">
            </div>

             <div style="display: flex; gap: 10px;">
                <a href="?page=library" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Buku</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

</div>
