<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// SQL logic constraints: 
// - Age in years
// - Age in years + months
// - Join year/month/day
// - Total visits
$query = "
    SELECT 
        patient_id, 
        name, 
        phone,
        join_date,
        TIMESTAMPDIFF(YEAR, dob, CURDATE()) AS age_years,
        TIMESTAMPDIFF(MONTH, dob, CURDATE()) % 12 AS age_months,
        YEAR(join_date) AS join_year,
        MONTH(join_date) AS join_month,
        DAY(join_date) AS join_day,
        (SELECT COUNT(*) FROM visits WHERE visits.patient_id = patients.patient_id) AS total_visits
    FROM patients
    ORDER BY name ASC
";
$patients = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="bi bi-people"></i> Patient List</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="add.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Patient</a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age (Years/Months)</th>
                <th>Join Date (Y-M-D)</th>
                <th>Total Visits</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($patients)): ?>
                <tr><td colspan="6" class="text-center">No patients found.</td></tr>
            <?php else: ?>
                <?php foreach ($patients as $p): ?>
                <tr>
                    <td><?= $p['patient_id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($p['name']) ?></strong><br>
                        <small class="text-muted"><i class="bi bi-telephone"></i> <?= htmlspecialchars($p['phone']) ?></small>
                    </td>
                    <td>
                        <?= $p['age_years'] ?>y, <?= $p['age_months'] ?>m
                    </td>
                    <td>
                        <!-- Utilizing SQL calculated join parts as required -->
                        <?= sprintf("%04d-%02d-%02d", $p['join_year'], $p['join_month'], $p['join_day']) ?>
                    </td>
                    <td>
                        <span class="badge bg-info text-dark"><?= $p['total_visits'] ?></span>
                    </td>
                    <td>
                        <a href="view.php?id=<?= $p['patient_id'] ?>" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                        <a href="edit.php?id=<?= $p['patient_id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
