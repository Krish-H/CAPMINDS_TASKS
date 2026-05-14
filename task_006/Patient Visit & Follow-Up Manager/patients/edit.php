<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_login();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$patient_id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $join_date = $_POST['join_date'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';

    $errors = [];

    if (empty($name)) $errors[] = "Name is required.";
    if (empty($dob)) $errors[] = "Date of Birth is required.";
    if (empty($join_date)) $errors[] = "Join Date is required.";

    if (!empty($dob) && strtotime($dob) > time()) {
        $errors[] = "Date of Birth cannot be in the future.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE patients SET name=?, dob=?, join_date=?, phone=?, address=? WHERE patient_id=?");
        if ($stmt->execute([$name, $dob, $join_date, $phone, $address, $patient_id])) {
            header("Location: view.php?id=$patient_id&msg=Updated");
            exit;
        } else {
            $errors[] = "Failed to update patient.";
        }
    }
}

// Fetch current details
$stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "Patient not found.";
    exit;
}

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-pencil-square"></i> Edit Patient</h2>
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
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? $patient['name']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? $patient['phone']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="dob" class="form-control" max="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($_POST['dob'] ?? $patient['dob']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Join Date *</label>
                    <input type="date" name="join_date" class="form-control" required value="<?= htmlspecialchars($_POST['join_date'] ?? $patient['join_date']) ?>">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($_POST['address'] ?? $patient['address']) ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Patient</button>
            <a href="view.php?id=<?= $patient_id ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
