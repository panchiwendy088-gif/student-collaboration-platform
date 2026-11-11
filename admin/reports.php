<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['admin']);

$conn = getDatabaseConnection();
$stats = ['students' => 0, 'lecturers' => 0, 'courses' => 0, 'materials' => 0, 'assignments' => 0];
if ($conn) {
    $queries = [
        'students' => "SELECT COUNT(*) AS total FROM users WHERE role = 'student'",
        'lecturers' => "SELECT COUNT(*) AS total FROM users WHERE role = 'lecturer'",
        'courses' => "SELECT COUNT(*) AS total FROM courses",
        'materials' => "SELECT COUNT(*) AS total FROM materials",
        'assignments' => "SELECT COUNT(*) AS total FROM assignments",
    ];
    foreach ($queries as $key => $sql) {
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $stats[$key] = (int) $result->fetch_assoc()['total'];
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Platform reports</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back to dashboard</a>
    </div>
    <div class="row g-4">
        <?php foreach ($stats as $key => $value): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <span class="text-capitalize"><?php echo htmlspecialchars(str_replace('_', ' ', $key), ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="display-6 fw-bold"><?php echo $value; ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
