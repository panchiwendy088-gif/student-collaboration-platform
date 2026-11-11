<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['student']);

$conn = getDatabaseConnection();
$grades = [];
$userId = $_SESSION['user']['id'];
if ($conn) {
    $stmt = $conn->prepare('SELECT s.grade, s.feedback, a.title AS assignment_title, c.title AS course_title FROM submissions s JOIN assignments a ON a.id = s.assignment_id JOIN courses c ON c.id = a.course_id WHERE s.student_id = ? AND s.grade IS NOT NULL ORDER BY s.updated_at DESC');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $grades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Grades & feedback</h2>
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
                            <th>Grade</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($grades): ?>
                            <?php foreach ($grades as $grade): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grade['course_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($grade['assignment_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="badge bg-success"><?php echo htmlspecialchars((string) $grade['grade'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td><?php echo nl2br(htmlspecialchars($grade['feedback'] ?? 'No feedback available.', ENT_QUOTES, 'UTF-8')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No marks recorded yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
