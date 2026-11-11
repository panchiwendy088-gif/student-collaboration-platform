<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin();

$conn = getDatabaseConnection();
$userId = $_SESSION['user']['id'];
$courses = [];
if ($conn) {
    $query = 'SELECT id, title FROM courses WHERE status = "active"';
    if ($_SESSION['user']['role'] === 'student') {
        $query = 'SELECT c.id, c.title FROM courses c JOIN enrollments e ON e.course_id = c.id WHERE e.student_id = ? AND c.status = "active"';
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $result = $conn->query($query);
        if ($result) {
            $courses = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $courseId = safeInt($_POST['course_id'], 0);
    $title = sanitizeText($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($courseId <= 0 || $title === '' || $content === '') {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Please provide a valid course, title, and discussion content.'];
        redirect(url('collaboration/create_discussion.php'));
    }

    if ($conn) {
        $stmt = $conn->prepare('INSERT INTO discussions (course_id, user_id, title, content, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->bind_param('iiss', $courseId, $userId, $title, $content);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Discussion created successfully.'];
    redirect(url('collaboration/discussions.php'));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Create discussion</h2>
        <a href="discussions.php" class="btn btn-outline-secondary">Back</a>
    </div>
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="post">
                <?php echo csrfField(); ?>
                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">Choose a course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars((string) $course['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" rows="6" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post discussion</button>
            </form>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
