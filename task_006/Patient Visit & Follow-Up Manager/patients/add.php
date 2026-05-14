<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $join_date = $_POST['join_date'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    $errors = [];

    // Basic Validation
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($dob)) $errors[] = "Date of Birth is required.";
    if (empty($join_date)) $errors[] = "Join Date is required.";

    // Advanced Validation handled by SQL directly if we want to be strict, 
    // but PHP form validation for 'not future' DOB is done via logic or just rely on SQL if possible.
    // The prompt says "DOB must be real and not future", "Join date required".
    // We will do a simple check.
    if (!empty($dob) && strtotime($dob) > time()) {
        $errors[] = "Date of Birth cannot be in the future.";
    }

    if (empty($errors)) {
        // Insert patient
        $stmt = $pdo->prepare("INSERT INTO patients (name, dob, join_date, phone, address) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $dob, $join_date, $phone, $address])) {
            header("Location: list.php?msg=Patient+Added");
            exit;
        } else {
            $errors[] = "Failed to add patient.";
        }
    }
}

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-person-plus"></i> Add New Patient</h2>
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
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date of Birth *</label>
                    <!-- Max today to prevent future dates visually -->
                    <input type="date" name="dob" class="form-control" max="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Join Date *</label>
                    <input type="date" name="join_date" class="form-control" required value="<?= htmlspecialchars($_POST['join_date'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Patient</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
