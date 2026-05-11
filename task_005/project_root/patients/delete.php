<?php
require_once '../config/db.php';

if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
    
    $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header("Location: list.php");
exit;
?>
