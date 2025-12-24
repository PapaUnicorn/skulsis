-- Database Schema for Skulsis
-- Standard SQL for MySQL/MariaDB
SET FOREIGN_KEY_CHECKS = 0;
-- 1. USER & AUTH MODULE
CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM(
        'admin',
        'teacher',
        'student',
        'parent',
        'librarian'
    ) NOT NULL,
    related_id BIGINT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- 2. MASTER DATA & ACADEMIC MODULE
CREATE TABLE IF NOT EXISTS academic_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL COMMENT 'e.g., 2024/2025',
    status ENUM('active', 'inactive') DEFAULT 'inactive'
);
CREATE TABLE IF NOT EXISTS semesters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id INT NOT NULL,
    name ENUM('Ganjil', 'Genap') NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS class_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL COMMENT 'e.g., X, XI, XII or 7, 8, 9'
);
CREATE TABLE IF NOT EXISTS majors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL
);
CREATE TABLE IF NOT EXISTS teachers (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(30) UNIQUE COMMENT 'NIP or NUPTK',
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    status ENUM('PNS', 'Honorer', 'Tetap Yayasan'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS classes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id INT NOT NULL,
    class_level_id INT NOT NULL,
    major_id INT NULL,
    name VARCHAR(50) NOT NULL COMMENT 'e.g., X IPA 1',
    homeroom_teacher_id BIGINT NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    FOREIGN KEY (class_level_id) REFERENCES class_levels(id),
    FOREIGN KEY (major_id) REFERENCES majors(id),
    FOREIGN KEY (homeroom_teacher_id) REFERENCES teachers(id)
);
CREATE TABLE IF NOT EXISTS subjects (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(100) NOT NULL,
    type ENUM('Muatan Nasional', 'Muatan Lokal', 'Peminatan') NOT NULL
);
-- 3. STUDENT PROFILES
CREATE TABLE IF NOT EXISTS students (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nisn VARCHAR(20) UNIQUE,
    nis VARCHAR(20) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    birth_place VARCHAR(50),
    birth_date DATE,
    parent_phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS student_enrollments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT NOT NULL,
    class_id BIGINT NOT NULL,
    academic_year_id INT NOT NULL,
    status ENUM('Active', 'Moved', 'Graduated', 'Dropped Out') DEFAULT 'Active',
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
);
-- 4. SCHEDULE & LMS
CREATE TABLE IF NOT EXISTS teaching_assignments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id INT NOT NULL,
    class_id BIGINT NOT NULL,
    subject_id BIGINT NOT NULL,
    teacher_id BIGINT NOT NULL,
    kkm INT DEFAULT 75,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);
CREATE TABLE IF NOT EXISTS schedules (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    teaching_assignment_id BIGINT NOT NULL,
    day ENUM(
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
        'Minggu'
    ) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room_name VARCHAR(50),
    FOREIGN KEY (teaching_assignment_id) REFERENCES teaching_assignments(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS teaching_journals (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    schedule_id BIGINT NOT NULL,
    date DATE NOT NULL,
    topic TEXT NOT NULL,
    media VARCHAR(255),
    notes TEXT,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id)
);
-- 5. ATTENDANCE
CREATE TABLE IF NOT EXISTS attendances (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_enrollment_id BIGINT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Hadir', 'Sakit', 'Izin', 'Alpha', 'Terlambat') NOT NULL,
    time_in TIME,
    notes VARCHAR(255),
    FOREIGN KEY (student_enrollment_id) REFERENCES student_enrollments(id)
);
-- 6. GRADES
CREATE TABLE IF NOT EXISTS assessments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    teaching_assignment_id BIGINT NOT NULL,
    title VARCHAR(100) NOT NULL,
    type ENUM('Tugas', 'UH', 'PTS', 'PAS', 'Sikap') NOT NULL,
    weight INT DEFAULT 0,
    FOREIGN KEY (teaching_assignment_id) REFERENCES teaching_assignments(id)
);
CREATE TABLE IF NOT EXISTS student_grades (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    assessment_id BIGINT NOT NULL,
    student_enrollment_id BIGINT NOT NULL,
    score DECIMAL(5, 2),
    feedback TEXT,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id),
    FOREIGN KEY (student_enrollment_id) REFERENCES student_enrollments(id)
);
-- 7. COUNSELING (BK)
CREATE TABLE IF NOT EXISTS violations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT NOT NULL,
    violation_date DATE NOT NULL,
    violation_type VARCHAR(100),
    points INT DEFAULT 0,
    sanction TEXT,
    FOREIGN KEY (student_id) REFERENCES students(id)
);
CREATE TABLE IF NOT EXISTS counseling_sessions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT NOT NULL,
    teacher_id BIGINT NOT NULL,
    date DATETIME NOT NULL,
    problem_summary TEXT,
    solution TEXT,
    is_confidential BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);
-- 8. LIBRARY
CREATE TABLE IF NOT EXISTS books (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    isbn VARCHAR(50),
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100),
    publisher VARCHAR(100),
    stock INT DEFAULT 0,
    shelf_location VARCHAR(50)
);
CREATE TABLE IF NOT EXISTS loans (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    book_id BIGINT NOT NULL,
    loan_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    status ENUM('Active', 'Returned', 'Overdue', 'Lost') DEFAULT 'Active',
    fine_amount DECIMAL(10, 2) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);
SET FOREIGN_KEY_CHECKS = 1;
-- INSERT DEFAULT SUPER ADMIN
-- Password hash for 'admin123' (BCRYPT)
INSERT INTO users (username, email, password, role, is_active)
VALUES (
        'admin',
        'admin@skulsis.com',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'admin',
        1
    );