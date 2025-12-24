<section class="hero container" style="min-height: 40vh; padding: 40px 20px;">
    <div class="hero-content" style="text-align: center; margin: 0 auto;">
        <h1 class="hero-title" style="font-size: 2.5rem; margin-bottom: 20px;">Penerimaan Peserta Didik Baru</h1>
        <p class="hero-subtitle">Bergabunglah dengan kami untuk masa depan yang lebih cerah. Isi formulir di bawah ini untuk mendaftar.</p>
    </div>
</section>

<section class="container" style="margin-bottom: 80px;">
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div style="background: #D1FAE5; color: #065F46; padding: 20px; border-radius: 12px; margin-bottom: 30px; text-align: center; border: 1px solid #6EE7B7;">
        <h3 style="margin-bottom: 10px;">Pendaftaran Berhasil! 🎉</h3>
        <p>Data Anda telah kami terima dan akan segera diverifikasi oleh panitia. Silakan tunggu informasi selanjutnya via WhatsApp.</p>
    </div>
    <?php endif; ?>

    <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
        <form action="modules/ppdb/register.php" method="POST" enctype="multipart/form-data">
            <!-- Data Pribadi -->
            <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--accent-color); padding-bottom: 10px; display: inline-block;">Data Peserta Didik</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nama Lengkap</label>
                    <input type="text" name="full_name" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">NISN</label>
                    <input type="text" name="nisn" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tempat Lahir</label>
                    <input type="text" name="birth_place" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Tanggal Lahir</label>
                    <input type="date" name="birth_date" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Alamat Lengkap</label>
                    <textarea name="address" rows="3" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;"></textarea>
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Jenis Kelamin</label>
                    <select name="gender" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Asal Sekolah</label>
                    <input type="text" name="origin_school" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
            </div>

            <!-- Data Orang Tua -->
            <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--accent-color); padding-bottom: 10px; display: inline-block;">Data Orang Tua / Wali</h3>
             <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nama Ayah</label>
                    <input type="text" name="father_name" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Nama Ibu</label>
                    <input type="text" name="mother_name" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">No. Telepon / WA</label>
                    <input type="text" name="parent_phone" required style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>
            </div>

            <!-- Upload Berkas -->
            <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--accent-color); padding-bottom: 10px; display: inline-block;">Upload Berkas</h3>
            <div style="background: #F8FAFC; padding: 20px; border-radius: 8px; border: 1px dashed #CBD5E1; margin-bottom: 30px;">
                <p style="margin-bottom: 15px; color: var(--text-muted); font-size: 0.9rem;">Format: PDF/JPG, Maksimal 2MB per file.</p>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Kartu Keluarga (KK)</label>
                    <input type="file" name="file_kk" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                 <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Akte Kelahiran</label>
                    <input type="file" name="file_akte" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                 <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 500;">Ijazah / SKL Terakhir</label>
                    <input type="file" name="file_ijazah" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 1rem;">Kirim Pendaftaran</button>
            </div>
        </form>
    </div>
</section>
