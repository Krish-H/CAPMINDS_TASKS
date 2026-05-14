<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_login();

$patient_id = isset($_GET['patient_id']) ? decode_id($_GET['patient_id']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'] ?? '';
    $visit_date = $_POST['visit_date'] ?? '';
    $consultation_fee = $_POST['consultation_fee'] ?? 0;
    $lab_fee = $_POST['lab_fee'] ?? 0;

    $errors = [];

    if (empty($patient_id)) $errors[] = "Patient is required.";
    if (empty($visit_date)) $errors[] = "Visit Date is required.";

    if (empty($errors)) {
        // Insert visit. SQL calculates follow_up_due by adding 7 days to visit_date.
        $stmt = $pdo->prepare("
            INSERT INTO visits (patient_id, visit_date, consultation_fee, lab_fee, follow_up_due) 
            VALUES (?, ?, ?, ?, DATE_ADD(?, INTERVAL 7 DAY))
        ");
        
        if ($stmt->execute([$patient_id, $visit_date, $consultation_fee, $lab_fee, $visit_date])) {
            $encoded_id = encode_id($patient_id);
            header("Location: ../patients/view.php?id=$encoded_id&msg=Visit+Added");
            exit;
        } else {
            $errors[] = "Failed to add visit.";
        }
    }
}


// Fetch all patients for the dropdown
$patients = $pdo->query("SELECT patient_id, name FROM patients ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-calendar-plus"></i> Record New Visit</h2>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Patient *</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">-- Select Patient --</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= $p['patient_id'] ?>" <?= ($p['patient_id'] == $patient_id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Visit Date *</label>
                    <!-- Default to today -->
                    <input type="date" name="visit_date" class="form-control" required value="<?= htmlspecialchars($_POST['visit_date'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Consultation Fee ($)</label>
                    <input type="number" step="0.01" name="consultation_fee" class="form-control" value="<?= htmlspecialchars($_POST['consultation_fee'] ?? '0.00') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lab Fee ($)</label>
                    <input type="number" step="0.01" name="lab_fee" class="form-control" value="<?= htmlspecialchars($_POST['lab_fee'] ?? '0.00') ?>">
                </div>
            </div>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> The follow-up date will be automatically set to 7 days after the Visit Date by the database.
            </div>
            <button type="submit" class="btn btn-primary">Save Visit</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
