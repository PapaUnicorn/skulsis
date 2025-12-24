CREATE TABLE IF NOT EXISTS ppdb_registrations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    nisn VARCHAR(20) NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    birth_place VARCHAR(50) NOT NULL,
    birth_date DATE NOT NULL,
    origin_school VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    father_name VARCHAR(100),
    mother_name VARCHAR(100),
    parent_phone VARCHAR(20) NOT NULL,
    file_kk VARCHAR(255),
    file_akte VARCHAR(255),
    file_ijazah VARCHAR(255),
    status ENUM('Pending', 'Verified', 'Rejected', 'Accepted') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);