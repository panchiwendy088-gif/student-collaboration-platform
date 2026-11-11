<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['student']);

$conn = getDatabaseConnection();
$stats = ['courses' => 0, 'materials' => 0, 'pending' => 0, 'submitted' => 0, 'grades' => 0];
$userId = $_SESSION['user']['id'];
if ($conn) {
    $stats['courses'] = (int) $conn->query("SELECT COUNT(*) AS total FROM enrollments WHERE student_id = $userId")->fetch_assoc()['total'];
    $stats['materials'] = (int) $conn->query("SELECT COUNT(*) AS total FROM materials WHERE course_id IN (SELECT course_id FROM enrollments WHERE student_id = $userId)")->fetch_assoc()['total'];
    $stats['submitted'] = (int) $conn->query("SELECT COUNT(*) AS total FROM submissions WHERE student_id = $userId")->fetch_assoc()['total'];
    $stats['pending'] = (int) $conn->query("SELECT COUNT(*) AS total FROM assignments a WHERE a.course_id IN (SELECT course_id FROM enrollments WHERE student_id = $userId) AND a.id NOT IN (SELECT assignment_id FROM submissions WHERE student_id = $userId)")->fetch_assoc()['total'];
    $stats['grades'] = (int) $conn->query("SELECT COUNT(*) AS total FROM submissions WHERE student_id = $userId AND grade IS NOT NULL")->fetch_assoc()['total'];
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Student dashboard</h1>
            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['full_name'], ENT_QUOTES, 'UTF-8'); ?>.</p>
        </div>
        <a href="<?php echo htmlspecialchars(url('auth/logout.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-danger">Logout</a>
    </div>
    <div class="row g-4 mb-4">
        <?php foreach ([['courses', 'Enrolled courses', 'fa-book', 'bg-primary'], ['materials', 'Learning materials', 'fa-file-lines', 'bg-success'], ['pending', 'Pending assignments', 'fa-clipboard-question', 'bg-warning'], ['submitted', 'Submitted assignments', 'fa-paper-plane', 'bg-info'], ['grades', 'Recent grades', 'fa-star', 'bg-danger']] as $metric): ?>
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
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 fw-bold">Quick actions</div>
                <div class="card-body d-grid gap-2">
                    <a href="courses.php" class="btn btn-outline-primary"><i class="fa-solid fa-book-open me-2"></i>Browse courses</a>
                    <a href="materials.php" class="btn btn-outline-success"><i class="fa-solid fa-download me-2"></i>Download materials</a>
                    <a href="assignments.php" class="btn btn-outline-info"><i class="fa-solid fa-list-check me-2"></i>My assignments</a>
                    <a href="grades.php" class="btn btn-outline-secondary"><i class="fa-solid fa-award me-2"></i>Check grades</a>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 fw-bold">Announcements</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">New course materials are available for download.</li>
                        <li class="list-group-item">Assignments with upcoming deadlines are marked for attention.</li>
                        <li class="list-group-item">Lecturer feedback is available in your grade review page.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
