<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'patient_manager_db';

try {
    // First, connect without database to create it if it doesn't exist
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert default users if table is empty
    $stmtUsers = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmtUsers->fetchColumn() == 0) {
        $adminHash = password_hash('password123', PASSWORD_DEFAULT);
        $userHash = password_hash('user123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, password_hash, role) VALUES 
            ('admin', '$adminHash', 'admin'),
            ('user', '$userHash', 'user')
        ");
    }

    // Create patients table
    $pdo->exec("CREATE TABLE IF NOT EXISTS patients (
        patient_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        dob DATE NOT NULL,
        join_date DATE NOT NULL,
        phone VARCHAR(50),
        address TEXT
    )");

    // Create visits table
    $pdo->exec("CREATE TABLE IF NOT EXISTS visits (
        visit_id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        visit_date DATE NOT NULL,
        consultation_fee DECIMAL(10,2) DEFAULT 0.00,
        lab_fee DECIMAL(10,2) DEFAULT 0.00,
        follow_up_due DATE,
        FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
    )");

    // Check if we need to insert dummy data
    $stmt = $pdo->query("SELECT COUNT(*) FROM patients");
    if ($stmt->fetchColumn() == 0) {
        // Insert 8 patients
        $pdo->exec("INSERT INTO patients (name, dob, join_date, phone, address) VALUES
            ('John Doe', '1985-04-12', DATE_SUB(CURDATE(), INTERVAL 18 MONTH), '555-0101', '123 Main St'),
            ('Jane Smith', '1992-08-25', DATE_SUB(CURDATE(), INTERVAL 6 MONTH), '555-0102', '456 Oak Ave'),
            ('Robert Brown', '1976-11-30', DATE_SUB(CURDATE(), INTERVAL 2 YEAR), '555-0103', '789 Pine Rd'),
            ('Emily Davis', '2001-02-15', DATE_SUB(CURDATE(), INTERVAL 2 MONTH), '555-0104', '321 Elm St'),
            ('Michael Wilson', '1965-06-20', DATE_SUB(CURDATE(), INTERVAL 10 MONTH), '555-0105', '654 Maple Dr'),
            ('Sarah Taylor', '1988-12-05', DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '555-0106', '987 Cedar Ln'),
            ('David Anderson', '1995-09-18', DATE_SUB(CURDATE(), INTERVAL 3 YEAR), '555-0107', '159 Birch Blvd'),
            ('Laura Thomas', '1982-03-22', DATE_SUB(CURDATE(), INTERVAL 5 DAY), '555-0108', '753 Walnut St')
        ");

        // Insert 15 visits
        $pdo->exec("INSERT INTO visits (patient_id, visit_date, consultation_fee, lab_fee, follow_up_due) VALUES
            (1, DATE_SUB(CURDATE(), INTERVAL 400 DAY), 100.00, 50.00, DATE_SUB(CURDATE(), INTERVAL 393 DAY)),
            (1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 120.00, 0.00, DATE_ADD(CURDATE(), INTERVAL 2 DAY)),
            (2, DATE_SUB(CURDATE(), INTERVAL 150 DAY), 100.00, 200.00, DATE_SUB(CURDATE(), INTERVAL 143 DAY)),
            (2, CURDATE(), 100.00, 0.00, DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
            (3, DATE_SUB(CURDATE(), INTERVAL 700 DAY), 150.00, 0.00, DATE_SUB(CURDATE(), INTERVAL 693 DAY)),
            (3, DATE_SUB(CURDATE(), INTERVAL 300 DAY), 150.00, 100.00, DATE_SUB(CURDATE(), INTERVAL 293 DAY)),
            (3, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 150.00, 0.00, DATE_SUB(CURDATE(), INTERVAL 3 DAY)), -- Overdue follow-up
            (4, DATE_SUB(CURDATE(), INTERVAL 50 DAY), 80.00, 20.00, DATE_SUB(CURDATE(), INTERVAL 43 DAY)),
            (5, DATE_SUB(CURDATE(), INTERVAL 200 DAY), 200.00, 500.00, DATE_SUB(CURDATE(), INTERVAL 193 DAY)),
            (5, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 100.00, 0.00, DATE_ADD(CURDATE(), INTERVAL 4 DAY)),
            (6, DATE_SUB(CURDATE(), INTERVAL 25 DAY), 120.00, 80.00, DATE_SUB(CURDATE(), INTERVAL 18 DAY)),
            (7, DATE_SUB(CURDATE(), INTERVAL 1000 DAY), 90.00, 0.00, DATE_SUB(CURDATE(), INTERVAL 993 DAY)),
            (7, DATE_SUB(CURDATE(), INTERVAL 500 DAY), 90.00, 0.00, DATE_SUB(CURDATE(), INTERVAL 493 DAY)),
            (7, DATE_SUB(CURDATE(), INTERVAL 200 DAY), 90.00, 30.00, DATE_SUB(CURDATE(), INTERVAL 193 DAY)),
            (8, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 110.00, 0.00, DATE_ADD(CURDATE(), INTERVAL 5 DAY))
        ");
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
