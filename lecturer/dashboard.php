<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['lecturer']);

$conn = getDatabaseConnection();
$stats = ['courses' => 0, 'students' => 0, 'materials' => 0, 'assignments' => 0, 'submissions' => 0];
$userId = $_SESSION['user']['id'];
if ($conn) {
    $stats['courses'] = (int) $conn->query("SELECT COUNT(*) AS total FROM courses WHERE lecturer_id = $userId")->fetch_assoc()['total'];
    $stats['students'] = (int) $conn->query("SELECT COUNT(DISTINCT e.student_id) AS total FROM enrollments e JOIN courses c ON c.id = e.course_id WHERE c.lecturer_id = $userId")->fetch_assoc()['total'];
    $stats['materials'] = (int) $conn->query("SELECT COUNT(*) AS total FROM materials WHERE lecturer_id = $userId")->fetch_assoc()['total'];
    $stats['assignments'] = (int) $conn->query("SELECT COUNT(*) AS total FROM assignments WHERE lecturer_id = $userId")->fetch_assoc()['total'];
    $stats['submissions'] = (int) $conn->query("SELECT COUNT(*) AS total FROM submissions s JOIN assignments a ON a.id = s.assignment_id WHERE a.lecturer_id = $userId")->fetch_assoc()['total'];
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Lecturer dashboard</h1>
            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user']['full_name'], ENT_QUOTES, 'UTF-8'); ?>.</p>
        </div>
        <a href="<?php echo htmlspecialchars(url('auth/logout.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-danger">Logout</a>
    </div>
    <div class="row g-4 mb-4">
        <?php foreach ([['courses', 'Assigned courses', 'fa-book', 'bg-primary'], ['students', 'Enrolled students', 'fa-users', 'bg-success'], ['materials', 'Uploaded materials', 'fa-file-lines', 'bg-warning'], ['assignments', 'Created assignments', 'fa-clipboard-check', 'bg-info'], ['submissions', 'Pending submissions', 'fa-paper-plane', 'bg-danger']] as $metric): ?>
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
                    <a href="add_material.php" class="btn btn-outline-primary"><i class="fa-solid fa-upload me-2"></i>Upload material</a>
                    <a href="add_assignment.php" class="btn btn-outline-success"><i class="fa-solid fa-plus me-2"></i>Create assignment</a>
                    <a href="submissions.php" class="btn btn-outline-info"><i class="fa-solid fa-file-arrow-down me-2"></i>View submissions</a>
                    <a href="announcements.php" class="btn btn-outline-secondary"><i class="fa-solid fa-bullhorn me-2"></i>Announcements</a>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-0 fw-bold">Course overview</div>
                <div class="card-body">
                    <p class="text-muted mb-0">Monitor your assigned courses, student performance, and assessments from this dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
