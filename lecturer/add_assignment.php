<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['lecturer']);

$conn = getDatabaseConnection();
$userId = $_SESSION['user']['id'];
$courses = [];
if ($conn) {
    $stmt = $conn->prepare('SELECT id, code, title FROM courses WHERE lecturer_id = ? ORDER BY title');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $courseId = safeInt($_POST['course_id'], 0);
    $title = sanitizeText($_POST['title'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $dueDate = $_POST['due_date'] ?? '';
    $totalMarks = safeInt($_POST['total_marks'], 100);

    if ($courseId <= 0 || $title === '' || $instructions === '' || $dueDate === '') {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Please complete all assignment details.'];
        redirect(url('lecturer/add_assignment.php'));
    }

    if ($conn) {
        $stmt = $conn->prepare('INSERT INTO assignments (course_id, lecturer_id, title, instructions, due_date, total_marks, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->bind_param('iisssi', $courseId, $userId, $title, $instructions, $dueDate, $totalMarks);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Assignment created successfully.'];
    redirect(url('lecturer/assignments.php'));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Create assignment</h2>
        <a href="assignments.php" class="btn btn-outline-secondary">View assignments</a>
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
                        <option value="">Select course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars((string) $course['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Assignment title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Instructions</label>
                    <textarea name="instructions" class="form-control" rows="6" required></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Due date</label>
                        <input type="datetime-local" name="due_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Total marks</label>
                        <input type="number" name="total_marks" class="form-control" min="1" value="100" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-4">Create assignment</button>
            </form>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
