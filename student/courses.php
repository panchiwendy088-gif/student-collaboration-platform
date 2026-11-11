<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['student']);

$conn = getDatabaseConnection();
$enrolledCourseIds = [];
if ($conn) {
    $stmt = $conn->prepare('SELECT course_id FROM enrollments WHERE student_id = ?');
    $studentId = $_SESSION['user']['id'];
    $stmt->bind_param('i', $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $enrolledCourseIds[] = (int) $row['course_id'];
    }
    $stmt->close();
}

$allCourses = [];
if ($conn) {
    $result = $conn->query('SELECT c.id, c.code, c.title, c.description, u.full_name AS lecturer_name FROM courses c LEFT JOIN users u ON u.id = c.lecturer_id WHERE c.status = "active" ORDER BY c.title');
    if ($result) {
        $allCourses = $result->fetch_all(MYSQLI_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $courseId = safeInt($_POST['course_id'], 0);
    if ($conn && $courseId > 0) {
        $stmt = $conn->prepare('INSERT INTO enrollments (course_id, student_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE course_id = VALUES(course_id)');
        $studentId = $_SESSION['user']['id'];
        $stmt->bind_param('ii', $courseId, $studentId);
        $stmt->execute();
        $stmt->close();
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Course enrolled successfully.'];
    }
    redirect(url('student/courses.php'));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Available courses</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
    </div>
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <div class="row g-4">
        <?php foreach ($allCourses as $course): ?>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($course['code'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php if (in_array((int) $course['id'], $enrolledCourseIds, true)): ?>
                                <span class="badge bg-success">Enrolled</span>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($course['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="small text-muted mb-3">Lecturer: <?php echo htmlspecialchars($course['lecturer_name'] ?? 'TBA', ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php if (!in_array((int) $course['id'], $enrolledCourseIds, true)): ?>
                            <form method="post">
                                <?php echo csrfField(); ?>
                                <input type="hidden" name="course_id" value="<?php echo (int) $course['id']; ?>">
                                <button type="submit" class="btn btn-primary">Enroll</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
