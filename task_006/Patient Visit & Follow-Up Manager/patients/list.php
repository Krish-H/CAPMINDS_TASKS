<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_login();
require_once '../includes/header.php';

$search = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$where = "1=1";
$params = [];
if ($search !== '') {
    $where .= " AND (name LIKE ? OR phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Get total count for pagination
$countQuery = "SELECT COUNT(*) FROM patients WHERE $where";
$stmtCount = $pdo->prepare($countQuery);
$stmtCount->execute($params);
$total_records = $stmtCount->fetchColumn();
$total_pages = ceil($total_records / $limit);

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
    WHERE $where
    ORDER BY name ASC
    LIMIT $limit OFFSET $offset
";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<div class="row mb-4 align-items-center">
    <div class="col-md-5 col-lg-6 mb-3 mb-md-0">
        <h2 class="mb-0 fw-bold text-dark"><i class="bi bi-people text-primary me-2"></i> Patient List</h2>
    </div>
    <div class="col-md-7 col-lg-6">
        <div class="d-flex flex-column flex-sm-row justify-content-md-end gap-3 align-items-stretch align-items-sm-center">
            
            <form class="modern-search-form d-flex align-items-center bg-white shadow-sm border p-1 m-0 flex-grow-1" method="GET">
                <div class="d-flex align-items-center px-3 w-100">
                    <i class="bi bi-search text-muted pe-2"></i>
                    <input type="text" name="search" class="modern-search-input form-control border-0 shadow-none bg-transparent p-0" placeholder="Search name or phone" value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="d-flex gap-1 ms-auto">
                    <button type="submit" class="btn btn-dark rounded-pill px-3 fw-medium">
                        Search
                    </button>
                    <?php if ($search): ?>
                    <a href="list.php" class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center text-secondary" style="width: 38px; height: 38px;" title="Clear Search">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </form>
            
            <a href="add.php" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-2" style="white-space: nowrap; height: 46px;">
                <i class="bi bi-plus-circle-fill"></i> Add Patient
            </a>

        </div>
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
