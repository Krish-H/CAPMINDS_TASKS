<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/core/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if user_id column exists
    $result = $db->query("SHOW COLUMNS FROM patients LIKE 'user_id'");
    if ($result->rowCount() == 0) {
        $db->exec("ALTER TABLE patients ADD COLUMN user_id INT NOT NULL AFTER id");
        $db->exec("ALTER TABLE patients ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
        echo "Column user_id added successfully.\n";
    } else {
        echo "Column user_id already exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
