<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['student']);

$assignmentId = safeInt($_GET['id'] ?? 0, 0);
$conn = getDatabaseConnection();
$assignment = null;
if ($conn && $assignmentId > 0) {
    $stmt = $conn->prepare('SELECT a.id, a.title, a.instructions, a.due_date, c.title AS course_title FROM assignments a JOIN courses c ON c.id = a.course_id WHERE a.id = ? AND a.course_id IN (SELECT course_id FROM enrollments WHERE student_id = ?)');
    $userId = $_SESSION['user']['id'];
    $stmt->bind_param('ii', $assignmentId, $userId);
    $stmt->execute();
    $assignment = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $assignmentId = safeInt($_POST['assignment_id'], 0);
    $validation = validateUploadedFile($_FILES['submission'] ?? [], ['pdf', 'doc', 'docx', 'ppt', 'pptx'], 12 * 1024 * 1024);
    if ($assignmentId <= 0 || !$validation['valid']) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $validation['valid'] ? 'Please choose a valid assignment.' : $validation['message']];
        redirect(url('student/assignments.php'));
    }
    $targetDir = __DIR__ . '/../uploads/submissions/';
    ensureDirectory($targetDir);
    $storedName = uniqid('submission_', true) . '.' . $validation['extension'];
    $target = $targetDir . $storedName;
    if (!move_uploaded_file($_FILES['submission']['tmp_name'], $target)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'The assignment file could not be uploaded.'];
        redirect(url('student/assignments.php'));
    }

    if ($conn) {
        $stmt = $conn->prepare('INSERT INTO submissions (assignment_id, student_id, file_name, file_path, submitted_at, status) VALUES (?, ?, ?, ?, NOW(), "pending") ON DUPLICATE KEY UPDATE file_name = VALUES(file_name), file_path = VALUES(file_path), submitted_at = NOW(), status = "pending"');
        $fileName = $_FILES['submission']['name'];
        $filePath = 'uploads/submissions/' . $storedName;
        $stmt->bind_param('iiss', $assignmentId, $_SESSION['user']['id'], $fileName, $filePath);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Assignment submitted successfully.'];
    redirect(url('student/my_submissions.php'));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Submit assignment</h2>
        <a href="assignments.php" class="btn btn-outline-secondary">Back</a>
    </div>
    <?php if ($assignment): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5><?php echo htmlspecialchars($assignment['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <p class="mb-1 text-muted"><?php echo htmlspecialchars($assignment['course_title'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($assignment['instructions'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="assignment_id" value="<?php echo (int) $assignment['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Upload assignment file</label>
                        <input type="file" name="submission" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No valid assignment was selected.</div>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
