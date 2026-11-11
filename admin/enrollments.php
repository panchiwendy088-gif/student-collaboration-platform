<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['admin']);

$conn = getDatabaseConnection();
$records = [];
if ($conn) {
    $query = 'SELECT e.id, c.title AS course_title, u.full_name AS student_name, e.enrolled_at FROM enrollments e JOIN courses c ON c.id = e.course_id JOIN users u ON u.id = e.student_id ORDER BY e.enrolled_at DESC';
    $result = $conn->query($query);
    if ($result) {
        $records = $result->fetch_all(MYSQLI_ASSOC);
    }
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Student enrollments</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back to dashboard</a>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($records): ?>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['student_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($record['course_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars(formatDate($record['enrolled_at'], 'M d, Y'), ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">No enrollment records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
