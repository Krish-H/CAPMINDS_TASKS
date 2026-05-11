
<?php
require_once '../config/db.php';
include '../includes/header.php';


// Pagination setup
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search setup
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchParam = "%$search%";

// Sorting setup
$allowedSortColumns = ['patient_name', 'age'];

$sortColumn = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns)
    ? $_GET['sort']
    : 'id';

$sortOrder = isset($_GET['order']) && strtolower($_GET['order']) == 'desc'
    ? 'DESC'
    : 'ASC';

// Toggle sort
$nextOrderName = ($sortColumn == 'patient_name' && $sortOrder == 'ASC')
    ? 'desc'
    : 'asc';

$nextOrderAge = ($sortColumn == 'age' && $sortOrder == 'ASC')
    ? 'desc'
    : 'asc';

// Count Query
if ($search !== '') {

    $countQuery = "
        SELECT COUNT(*) as total
        FROM patients
        WHERE patient_name LIKE ?
        OR diagnosis LIKE ?
    ";

    $stmt = $conn->prepare($countQuery);
    $stmt->bind_param("ss", $searchParam, $searchParam);

} else {

    $countQuery = "SELECT COUNT(*) as total FROM patients";
    $stmt = $conn->prepare($countQuery);
}

$stmt->execute();

$countResult = $stmt->get_result();

$totalRows = $countResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

$stmt->close();

// Data Query
if ($search !== '') {

    $dataQuery = "
        SELECT *
        FROM patients
        WHERE patient_name LIKE ?
        OR diagnosis LIKE ?
        ORDER BY $sortColumn $sortOrder
        LIMIT ? OFFSET ?
    ";

    $stmt = $conn->prepare($dataQuery);

    $stmt->bind_param(
        "ssii",
        $searchParam,
        $searchParam,
        $limit,
        $offset
    );

} else {

    $dataQuery = "
        SELECT *
        FROM patients
        ORDER BY $sortColumn $sortOrder
        LIMIT ? OFFSET ?
    ";

    $stmt = $conn->prepare($dataQuery);

    $stmt->bind_param(
        "ii",
        $limit,
        $offset
    );
}

$stmt->execute();

$result = $stmt->get_result();
?>



<div class="container py-4">

    <!-- Top Dashboard Section -->

    <div class="modern-card p-4 mb-4">

        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">

            <!-- Left -->

            <div>

                <h1 class="dashboard-title mb-1">
                    Patients Dashboard
                </h1>

                <p class="dashboard-subtitle mb-0">
                    Manage and monitor all patient records
                </p>

            </div>

            <!-- Right -->

            <div class="d-flex flex-column flex-md-row align-items-stretch gap-3 w-100 justify-content-lg-end">

                <!-- Search Form -->

                <form method="GET"
                      action="list.php"
                      class="d-flex align-items-center gap-2 flex-grow-1"
                      style="max-width: 520px;">

                    <div class="position-relative flex-grow-1">


                        <input
                            type="text"
                            name="search"
                            class="form-control search-box"
                            placeholder="Search patients..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                    </div>

                    <button type="submit"
                            class="btn btn-dark px-4 py-3 fw-semibold">

                        Search

                    </button>

                    <?php if ($search !== ''): ?>

                        <a href="list.php"
                           class="btn btn-light border px-4 fw-semibold">

                            Clear

                        </a>

                    <?php endif; ?>

                    <!-- Preserve Sort -->

                    <input type="hidden"
                           name="sort"
                           value="<?php echo htmlspecialchars($sortColumn); ?>">

                    <input type="hidden"
                           name="order"
                           value="<?php echo htmlspecialchars(strtolower($sortOrder)); ?>">

                </form>

                <!-- Add Button -->

                <a href="create.php"
                   class="btn btn-gradient px-4 fw-semibold d-flex align-items-center justify-content-center gap-2"
                   style="min-width: 180px;">

                    <span style="font-size:18px;">＋</span>

                    Add Patient

                </a>

            </div>

        </div>

    </div>

    <!-- Table -->

    <div class="modern-card p-3">

        <div class="table-responsive">

            <table class="table table-modern align-middle mb-0">

                <thead>

                    <tr>

                        <th class="ps-4">#</th>

                        <th>

                            <a href="?search=<?php echo urlencode($search); ?>&sort=patient_name&order=<?php echo $nextOrderName; ?>"
                               class="text-dark text-decoration-none fw-bold">

                                Patient Name

                                <?php
                                if($sortColumn == 'patient_name'){
                                    echo $sortOrder == 'ASC' ? ' ▲' : ' ▼';
                                } else {
                                    echo ' ↕';
                                }
                                ?>

                            </a>

                        </th>

                        <th>Email</th>

                        <th>Phone</th>

                        <th>

                            <a href="?search=<?php echo urlencode($search); ?>&sort=age&order=<?php echo $nextOrderAge; ?>"
                               class="text-dark text-decoration-none fw-bold">

                                Age

                                <?php
                                if($sortColumn == 'age'){
                                    echo $sortOrder == 'ASC' ? ' ▲' : ' ▼';
                                } else {
                                    echo ' ↕';
                                }
                                ?>

                            </a>

                        </th>

                        <th>Gender</th>

                        <th>Diagnosis</th>

                        <th class="text-center">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if ($result->num_rows > 0): ?>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <tr>

                                <td class="fw-bold ps-4">
                                    #<?php echo $row['id']; ?>
                                </td>

                                <td>

                                    <div class="fw-bold text-dark">

                                        <?php echo htmlspecialchars($row['patient_name']); ?>

                                    </div>

                                </td>

                                <td class="text-secondary">

                                    <?php echo htmlspecialchars($row['email']); ?>

                                </td>

                                <td>

                                    <?php echo htmlspecialchars($row['phone']); ?>

                                </td>

                                <td>

                                    <span class="badge bg-light text-dark border badge-soft">

                                        <?php echo $row['age']; ?> yrs

                                    </span>

                                </td>

                                <td>

                                    <?php if($row['gender'] == 'Male'): ?>

                                        <span class="badge bg-primary-subtle text-primary badge-soft">

                                            Male

                                        </span>

                                    <?php elseif($row['gender'] == 'Female'): ?>

                                        <span class="badge bg-danger-subtle text-danger badge-soft">

                                            Female

                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary-subtle text-secondary badge-soft">

                                            Other

                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td class="text-secondary">

                                    <?php echo htmlspecialchars($row['diagnosis']); ?>

                                </td>

                                <td class="text-center">

                                    <div class="d-flex justify-content-center gap-2">

                                        <form method="POST" action="edit.php" class="m-0">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-light border px-3 fw-semibold">
                                                ✏ Edit
                                            </button>
                                        </form>

                                        <form method="POST" action="delete.php" class="m-0" onsubmit="return confirm('Are you sure you want to delete <?php echo htmlspecialchars(addslashes($row['patient_name'])); ?>?');">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger px-3 fw-semibold">
                                                Delete
                                            </button>
                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="8">

                                <div class="empty-box text-center">

                                    <div class="empty-icon mb-3">
                                        🩺
                                    </div>

                                    <h4 class="fw-bold">
                                        No Patients Found
                                    </h4>

                                    <p class="text-muted">
                                        Try searching with another keyword
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Pagination -->

    <?php if ($totalPages > 1): ?>

        <nav class="mt-4">

            <ul class="pagination justify-content-center">

                <!-- Previous -->

                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">

                    <a class="page-link px-4"

                       href="?search=<?php echo urlencode($search); ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo strtolower($sortOrder); ?>&page=<?php echo $page - 1; ?>">

                        Previous

                    </a>

                </li>

                <!-- Page Numbers -->

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">

                        <a class="page-link"

                           href="?search=<?php echo urlencode($search); ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo strtolower($sortOrder); ?>&page=<?php echo $i; ?>">

                            <?php echo $i; ?>

                        </a>

                    </li>

                <?php endfor; ?>

                <!-- Next -->

                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">

                    <a class="page-link px-4"

                       href="?search=<?php echo urlencode($search); ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo strtolower($sortOrder); ?>&page=<?php echo $page + 1; ?>">

                        Next

                    </a>

                </li>

            </ul>

        </nav>

    <?php endif; ?>

</div>

<?php
$stmt->close();
include '../includes/footer.php';
?>
