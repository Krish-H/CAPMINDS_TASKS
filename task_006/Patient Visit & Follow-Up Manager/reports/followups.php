<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_role('admin');
require_once '../includes/header.php';

$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$where = "1=1";
$params = [];
if ($search !== '') {
    $where .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}

$countQuery = "
    SELECT COUNT(*) FROM (
        SELECT 
            CASE
                WHEN v.follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 'Upcoming'
                WHEN v.follow_up_due < CURDATE() AND NOT EXISTS (
                    SELECT 1 FROM visits v2 WHERE v2.patient_id = v.patient_id AND v2.visit_date > v.follow_up_due
                ) THEN 'Missed & Overdue'
                ELSE 'Other'
            END as status_category
        FROM visits v
        JOIN patients p ON v.patient_id = p.patient_id
        WHERE $where
    ) as subquery
    WHERE status_category IN ('Upcoming', 'Missed & Overdue')
";
$stmtCount = $pdo->prepare($countQuery);
$stmtCount->execute($params);
$total_records = $stmtCount->fetchColumn();
$total_pages = ceil($total_records / $limit);

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
    WHERE $where
    HAVING status_category IN ('Upcoming', 'Missed & Overdue')
    ORDER BY v.follow_up_due ASC
    LIMIT $limit OFFSET $offset
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$followups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<div class="row mb-4 align-items-center">
    <div class="col-md-6 col-lg-7 mb-3 mb-md-0">
        <h2 class="mb-1 fw-bold text-dark"><i class="bi bi-bell text-primary me-2"></i> Follow-Up Report</h2>
        <p class="text-muted mb-0 small">Shows upcoming (next 7 days) and missed/overdue follow-ups.</p>
    </div>
    <div class="col-md-6 col-lg-5">
        <div class="d-flex flex-column flex-sm-row justify-content-md-end gap-3 align-items-stretch align-items-sm-center">
            
            <form class="modern-search-form d-flex align-items-center bg-white shadow-sm border p-1 m-0 flex-grow-1" method="GET">
                <div class="d-flex align-items-center px-3 w-100">
                    <i class="bi bi-search text-muted pe-2"></i>
                    <input type="text" name="search" class="modern-search-input form-control border-0 shadow-none bg-transparent p-0" placeholder="Search patient name" value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="d-flex gap-1 ms-auto">
                    <button type="submit" class="btn btn-dark rounded-pill px-3 fw-medium">
                        Search
                    </button>
                    <?php if ($search): ?>
                    <a href="followups.php" class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center text-secondary" style="width: 38px; height: 38px;" title="Clear Search">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </form>

        </div>
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
                        <a href="../patients/view.php?id=<?= encode_id($f['patient_id']) ?>" class="text-decoration-none fw-bold">
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
                        <a href="../visits/add.php?patient_id=<?= encode_id($f['patient_id']) ?>" class="btn btn-sm btn-primary">Log Visit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($total_pages > 1): ?>
<nav aria-label="Page navigation" class="mt-4 mb-5">
  <ul class="pagination modern-pagination justify-content-center border-0">
    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
      <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">
        <i class="bi bi-chevron-left me-1"></i> Prev
      </a>
    </li>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
      <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
    </li>
    <?php endfor; ?>
    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
      <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">
        Next <i class="bi bi-chevron-right ms-1"></i>
      </a>
    </li>
  </ul>
</nav>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
