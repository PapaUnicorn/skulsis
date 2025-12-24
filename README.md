# Skulsis - Sistem Informasi Sekolah

Sistem Informasi Sekolah berbasis Web (PHP, MySQL, CSS Native).

## Instalasi Database

1. Buka PHPMyAdmin atau terminal database manager Anda.
2. Buat database baru bernama `skulsis`.
3. Import file `database.sql` yang ada di root direktori project ini.
   - File ini berisi seluruh struktur tabel dan User Admin default.

## Akun Login Default

- **Username**: `admin`
- **Password**: `admin123`
- **Role**: `admin`

## Fitur Utama

- **Portal Publik**: Beranda, Berita, Galeri, PPDB Online.
- **PPDB**: Form pendaftaran siswa baru.
- **Dashboard Multi-Role**: Admin, Guru (Teacher), Siswa (Student).
- **Manajemen User**: Struktur database mendukung RBAC.

## Struktur Folder

- `/assets`: CSS, IMG, JS
- `/config`: Koneksi Database
- `/dashboard`: Panel Admin/User (Back-end)
- `/modules`: Logika backend (Login, Proses PPDB, dll)
- `/views`: Tampilan Frontend
- `/uploads`: Folder penyimpanan file upload (PPDB, Tugas, dll)

## Pengembangan Selanjutnya

Untuk melanjutkan pengembangan, fokus pada:
1. Membuat CRUD (Create, Read, Update, Delete) untuk Data Master di Dashboard (Guru, Siswa, Mapel).
2. Mengaktifkan fitur PPDB agar data masuk ke database.
3. Mengembangkan fitur LMS (Jadwal, Jurnal, Nilai).
