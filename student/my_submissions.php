<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['student']);

$conn = getDatabaseConnection();
$submissions = [];
$userId = $_SESSION['user']['id'];
if ($conn) {
    $stmt = $conn->prepare('SELECT s.id, s.file_name, s.file_path, s.submitted_at, s.grade, s.feedback, a.title AS assignment_title, c.title AS course_title FROM submissions s JOIN assignments a ON a.id = s.assignment_id JOIN courses c ON c.id = a.course_id WHERE s.student_id = ? ORDER BY s.submitted_at DESC');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">My submissions</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Assignment</th>
                            <th>Submitted</th>
                            <th>Grade</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($submissions): ?>
                            <?php foreach ($submissions as $submission): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($submission['course_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($submission['assignment_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars(formatDate($submission['submitted_at'], 'M d, Y'), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($submission['grade'] !== null ? (string) $submission['grade'] : 'Pending', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($submission['feedback'] ?? 'No feedback yet.', ENT_QUOTES, 'UTF-8')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">No assignment submissions recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
