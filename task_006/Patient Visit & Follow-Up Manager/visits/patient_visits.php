<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_login();

if (!isset($_GET['id']) || !($patient_id = decode_id($_GET['id']))) {
    header("Location: ../patients/list.php");
    exit;
}

// Get Patient Info
$pStmt = $pdo->prepare("SELECT name FROM patients WHERE patient_id = ?");
$pStmt->execute([$patient_id]);
$patient = $pStmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "Patient not found.";
    exit;
}

// SQL logic constraints: Total visits, Days between first and last visit
$statsStmt = $pdo->prepare("
    SELECT 
        COUNT(*) AS total_visits,
        DATEDIFF(MAX(visit_date), MIN(visit_date)) AS days_between_first_last
    FROM visits 
    WHERE patient_id = ?
");
$statsStmt->execute([$patient_id]);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Get Visit History
$vStmt = $pdo->prepare("
    SELECT * 
    FROM visits 
    WHERE patient_id = ? 
    ORDER BY visit_date DESC
");
$vStmt->execute([$patient_id]);
$visits = $vStmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-journal-medical"></i> Visit History for <?= htmlspecialchars($patient['name']) ?></h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="../patients/view.php?id=<?= encode_id($patient_id) ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Patient</a>
        <a href="add.php?patient_id=<?= encode_id($patient_id) ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Visit</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title">History Summary (SQL Calculated)</h5>
                <p class="card-text mb-1"><strong>Total Visits:</strong> <?= $stats['total_visits'] ?></p>
                <p class="card-text"><strong>Days between First & Last Visit:</strong> 
                    <?= $stats['days_between_first_last'] !== null ? $stats['days_between_first_last'] . ' days' : 'N/A (Not enough visits)' ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Visit Date</th>
                <th>Consultation Fee</th>
                <th>Lab Fee</th>
                <th>Follow-up Due</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($visits)): ?>
                <tr><td colspan="4" class="text-center">No visits found.</td></tr>
            <?php else: ?>
                <?php foreach ($visits as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['visit_date']) ?></td>
                    <td>$<?= htmlspecialchars($v['consultation_fee']) ?></td>
                    <td>$<?= htmlspecialchars($v['lab_fee']) ?></td>
                    <td>
                        <?= htmlspecialchars($v['follow_up_due']) ?>
                        <?php if (strtotime($v['follow_up_due']) < time() && strtotime($v['follow_up_due']) > 0): ?>
                            <span class="badge bg-warning text-dark ms-2">Passed</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
