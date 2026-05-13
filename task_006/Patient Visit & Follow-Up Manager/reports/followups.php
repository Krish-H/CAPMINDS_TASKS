<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// SQL logic constraint:
// 1. Upcoming follow-ups (next 7 days)
// 2. Overdue follow-ups (follow_up_due < today)
// 3. Missed follow-ups (no visit after due date) - implicitly same as overdue in this model, but we strictly enforce "no visit AFTER due date"
$query = "
    SELECT 
        v.visit_id,
        v.visit_date,
        v.follow_up_due,
        p.patient_id,
        p.name AS patient_name,
        p.phone,
        CASE
            WHEN v.follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 'Upcoming'
            WHEN v.follow_up_due < CURDATE() AND NOT EXISTS (
                SELECT 1 FROM visits v2 WHERE v2.patient_id = v.patient_id AND v2.visit_date > v.follow_up_due
            ) THEN 'Missed & Overdue'
            ELSE 'Other'
        END as status_category
    FROM visits v
    JOIN patients p ON v.patient_id = p.patient_id
    HAVING status_category IN ('Upcoming', 'Missed & Overdue')
    ORDER BY v.follow_up_due ASC
";

$followups = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mb-4">
    <div class="col-12">
        <h2><i class="bi bi-bell"></i> Follow-Up Report</h2>
        <p class="text-muted">Shows upcoming (next 7 days) and missed/overdue follow-ups (calculated entirely via SQL).</p>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Due Date</th>
                <th>Patient</th>
                <th>Phone</th>
                <th>Original Visit</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($followups)): ?>
                <tr><td colspan="6" class="text-center">No follow-ups require attention.</td></tr>
            <?php else: ?>
                <?php foreach ($followups as $f): ?>
                <tr class="<?= $f['status_category'] === 'Missed & Overdue' ? 'table-danger' : '' ?>">
                    <td><strong><?= htmlspecialchars($f['follow_up_due']) ?></strong></td>
                    <td>
                        <a href="../patients/view.php?id=<?= $f['patient_id'] ?>" class="text-decoration-none fw-bold">
                            <?= htmlspecialchars($f['patient_name']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($f['phone']) ?></td>
                    <td><?= htmlspecialchars($f['visit_date']) ?></td>
                    <td>
                        <?php if ($f['status_category'] === 'Upcoming'): ?>
                            <span class="badge bg-info text-dark"><i class="bi bi-clock"></i> Upcoming</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> Missed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="../visits/add.php?patient_id=<?= $f['patient_id'] ?>" class="btn btn-sm btn-primary">Log Visit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
