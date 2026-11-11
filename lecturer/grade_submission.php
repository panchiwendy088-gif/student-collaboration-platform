<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['lecturer']);

$conn = getDatabaseConnection();
$id = safeInt($_GET['id'] ?? 0, 0);
$submission = null;
if ($conn && $id > 0) {
    $stmt = $conn->prepare('SELECT s.id, s.assignment_id, s.student_id, s.file_path, s.file_name, s.grade, s.feedback, a.title AS assignment_title, u.full_name AS student_name FROM submissions s JOIN assignments a ON a.id = s.assignment_id JOIN users u ON u.id = s.student_id WHERE s.id = ? AND a.lecturer_id = ?');
    $userId = $_SESSION['user']['id'];
    $stmt->bind_param('ii', $id, $userId);
    $stmt->execute();
    $submission = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $grade = $_POST['grade'] ?? null;
    $feedback = trim($_POST['feedback'] ?? '');
    $submissionId = safeInt($_POST['submission_id'], 0);
    if ($submissionId > 0 && $conn) {
    if (!is_numeric($grade)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'A valid numeric grade is required.'];
        redirect(url('lecturer/submissions.php'));
    }
    $stmt = $conn->prepare('UPDATE submissions SET grade = ?, feedback = ?, status = "reviewed", updated_at = NOW() WHERE id = ? AND assignment_id IN (SELECT id FROM assignments WHERE lecturer_id = ?)');
    $gradeValue = (float) $grade;
    $stmt->bind_param('dsii', $gradeValue, $feedback, $submissionId, $_SESSION['user']['id']);
    $stmt->execute();
    $stmt->close();
}
$_SESSION['flash'] = ['type' => 'success', 'message' => 'Submission marked and feedback saved.'];
redirect(url('lecturer/submissions.php'));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Grade submission</h2>
        <a href="submissions.php" class="btn btn-outline-secondary">Back</a>
    </div>
    <?php if ($submission): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <p><strong>Student:</strong> <?php echo htmlspecialchars($submission['student_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Assignment:</strong> <?php echo htmlspecialchars($submission['assignment_title'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Attachment:</strong> <a href="<?php echo htmlspecialchars(url($submission['file_path']), ENT_QUOTES, 'UTF-8'); ?>" target="_blank"><?php echo htmlspecialchars($submission['file_name'], ENT_QUOTES, 'UTF-8'); ?></a></p>
                <form method="post">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="submission_id" value="<?php echo (int) $submission['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Grade</label>
                        <input type="number" min="0" max="100" step="0.01" name="grade" value="<?php echo htmlspecialchars((string) ($submission['grade'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feedback</label>
                        <textarea name="feedback" class="form-control" rows="5"><?php echo htmlspecialchars($submission['feedback'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save grade</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No submission found to grade.</div>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
