<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['admin']);

$conn = getDatabaseConnection();
$stats = ['students' => 0, 'lecturers' => 0, 'courses' => 0, 'materials' => 0, 'assignments' => 0];
if ($conn) {
    $queries = [
        'students' => "SELECT COUNT(*) AS total FROM users WHERE role = 'student' AND status = 'active'",
        'lecturers' => "SELECT COUNT(*) AS total FROM users WHERE role = 'lecturer' AND status = 'active'",
        'courses' => "SELECT COUNT(*) AS total FROM courses WHERE status = 'active'",
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Administrator dashboard</h1>
            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['full_name'], ENT_QUOTES, 'UTF-8'); ?>.</p>
        </div>
        <a href="<?php echo htmlspecialchars(url('auth/logout.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-danger">Logout</a>
    </div>
    <div class="row g-4 mb-4">
        <?php foreach ([['students', 'Students', 'fa-users', 'bg-primary'], ['lecturers', 'Lecturers', 'fa-chalkboard-user', 'bg-success'], ['courses', 'Courses', 'fa-book-open', 'bg-info'], ['materials', 'Learning materials', 'fa-file-lines', 'bg-warning'], ['assignments', 'Assignments', 'fa-clipboard-list', 'bg-danger']] as $metric): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted text-uppercase small"><?php echo $metric[1]; ?></div>
                            <div class="display-6 fw-bold"><?php echo $stats[$metric[0]]; ?></div>
                        </div>
                        <div class="icon-box <?php echo $metric[3]; ?> rounded-circle"><i class="fa-solid <?php echo $metric[2]; ?>"></i></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 fw-bold">Quick actions</div>
                <div class="card-body d-grid gap-2">
                    <a href="manage_students.php" class="btn btn-outline-primary"><i class="fa-solid fa-user-plus me-2"></i>Manage students</a>
                    <a href="manage_lecturers.php" class="btn btn-outline-success"><i class="fa-solid fa-user-tie me-2"></i>Manage lecturers</a>
                    <a href="manage_courses.php" class="btn btn-outline-info"><i class="fa-solid fa-book me-2"></i>Manage courses</a>
                    <a href="reports.php" class="btn btn-outline-secondary"><i class="fa-solid fa-chart-bar me-2"></i>Platform reports</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 fw-bold">Recent activity</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Platform health checks are running normally.</li>
                        <li class="list-group-item">New enrollments and submissions are available for review.</li>
                        <li class="list-group-item">System activity logs are available from the reports area.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
