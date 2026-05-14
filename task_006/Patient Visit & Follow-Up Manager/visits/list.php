<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_login();
require_once '../includes/header.php';

$search = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$where = "1=1";
$params = [];

if ($search !== '') {
    $where .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}
if ($date_from !== '') {
    $where .= " AND v.visit_date >= ?";
    $params[] = $date_from;
}
if ($date_to !== '') {
    $where .= " AND v.visit_date <= ?";
    $params[] = $date_to;
}

$countQuery = "SELECT COUNT(*) FROM visits v JOIN patients p ON v.patient_id = p.patient_id WHERE $where";
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
        DATEDIFF(CURDATE(), v.visit_date) AS days_since_visit,
        (v.follow_up_due >= CURDATE()) AS is_upcoming,
        (v.follow_up_due < CURDATE() AND NOT EXISTS (
            SELECT 1 FROM visits v2 WHERE v2.patient_id = v.patient_id AND v2.visit_date > v.follow_up_due
        )) AS is_overdue
    FROM visits v
    JOIN patients p ON v.patient_id = p.patient_id
    WHERE $where
    ORDER BY v.visit_date DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<div class="row mb-4 align-items-center">
    <div class="col-xl-3 col-lg-2 mb-3 mb-lg-0">
        <h2 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-check text-primary me-2"></i> All Visits</h2>
    </div>
    <div class="col-xl-9 col-lg-10">
        <div class="d-flex flex-column flex-lg-row justify-content-lg-end gap-3 align-items-stretch align-items-lg-center">
            
            <form class="modern-search-form d-flex flex-column flex-md-row align-items-stretch align-items-md-center bg-white shadow-sm border p-1 m-0 flex-grow-1 flex-lg-grow-0" method="GET">
                
                <!-- Search -->
                <div class="d-flex align-items-center px-3 py-2 py-md-0 flex-grow-1">
                    <i class="bi bi-search text-muted pe-2"></i>
                    <input type="text" name="search" class="modern-search-input form-control border-0 shadow-none bg-transparent p-0" placeholder="Patient Name" value="<?= htmlspecialchars($search) ?>" style="min-width: 140px;">
                </div>
                
                <div class="vr d-none d-md-block text-black-50" style="min-height: 25px;"></div>
                <hr class="d-md-none m-0 text-black-50">
                
                <!-- From Date -->
                <div class="d-flex align-items-center px-3 py-2 py-md-0">
                    <label class="small text-muted me-2 mb-0 fw-medium">From</label>
                    <input type="date" name="date_from" class="modern-search-input form-control border-0 shadow-none bg-transparent p-0 text-muted" value="<?= htmlspecialchars($date_from) ?>" style="width: 125px;">
                </div>
                
                <div class="vr d-none d-md-block text-black-50" style="min-height: 25px;"></div>
                <hr class="d-md-none m-0 text-black-50">
                
                <!-- To Date -->
                <div class="d-flex align-items-center px-3 py-2 py-md-0">
                    <label class="small text-muted me-2 mb-0 fw-medium">To</label>
                    <input type="date" name="date_to" class="modern-search-input form-control border-0 shadow-none bg-transparent p-0 text-muted" value="<?= htmlspecialchars($date_to) ?>" style="width: 125px;">
                </div>
                
                <hr class="d-md-none m-0 text-black-50">
                
                <!-- Buttons -->
                <div class="d-flex px-1 py-2 py-md-0 gap-1 ms-md-auto">
                    <button type="submit" class="btn btn-dark rounded-pill px-4 flex-grow-1 flex-md-grow-0 fw-medium">
                        Filter
                    </button>
                    <?php if ($search || $date_from || $date_to): ?>
                    <a href="list.php" class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center flex-grow-0 text-secondary" style="width: 38px; height: 38px;" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    <?php endif; ?>
                </div>

            </form>
            
            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-2" style="white-space: nowrap; height: 46px;">
                <i class="bi bi-plus-circle-fill"></i><span>Add Visit</span>
            </a>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Patient</th>
                <th>Days Since Visit</th>
                <th>Follow-up Due</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($visits)): ?>
                <tr><td colspan="6" class="text-center">No visits found.</td></tr>
            <?php else: ?>
                <?php foreach ($visits as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['visit_date']) ?></td>
                    <td>
                        <a href="../patients/view.php?id=<?= $v['patient_id'] ?>" class="text-decoration-none fw-bold">
                            <?= htmlspecialchars($v['patient_name']) ?>
                        </a>
                    </td>
                    <td>
                        <?= $v['days_since_visit'] ?> days ago
                    </td>
                    <td><?= htmlspecialchars($v['follow_up_due']) ?></td>
                    <td>
                        <?php if ($v['is_overdue']): ?>
                            <span class="badge bg-danger">Overdue</span>
                        <?php elseif ($v['is_upcoming']): ?>
                            <span class="badge bg-info text-dark">Upcoming</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Passed / Completed</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="patient_visits.php?id=<?= $v['patient_id'] ?>" class="btn btn-sm btn-outline-secondary" title="View Patient History"><i class="bi bi-clock-history"></i> History</a>
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
      <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&date_from=<?= urlencode($date_from) ?>&date_to=<?= urlencode($date_to) ?>">
        <i class="bi bi-chevron-left me-1"></i> Prev
      </a>
    </li>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
      <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&date_from=<?= urlencode($date_from) ?>&date_to=<?= urlencode($date_to) ?>"><?= $i ?></a>
    </li>
    <?php endfor; ?>
    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
      <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&date_from=<?= urlencode($date_from) ?>&date_to=<?= urlencode($date_to) ?>">
        Next <i class="bi bi-chevron-right ms-1"></i>
      </a>
    </li>
  </ul>
</nav>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
