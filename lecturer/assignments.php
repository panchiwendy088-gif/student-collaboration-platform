<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['lecturer']);

$conn = getDatabaseConnection();
$assignments = [];
$userId = $_SESSION['user']['id'];
if ($conn) {
    $stmt = $conn->prepare('SELECT a.id, a.title, a.instructions, a.due_date, a.total_marks, c.title AS course_title FROM assignments a JOIN courses c ON c.id = a.course_id WHERE a.lecturer_id = ? ORDER BY a.due_date ASC');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $assignments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Assignments</h2>
        <a href="add_assignment.php" class="btn btn-primary">Create assignment</a>
    </div>
    <div class="row g-4">
        <?php if ($assignments): ?>
            <?php foreach ($assignments as $assignment): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-info"><?php echo htmlspecialchars($assignment['course_title'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="text-muted small">Due: <?php echo htmlspecialchars(formatDate($assignment['due_date'], 'M d, Y'), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($assignment['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($assignment['instructions'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <a href="submissions.php?assignment_id=<?php echo (int) $assignment['id']; ?>" class="btn btn-outline-primary btn-sm">View submissions</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12"><div class="alert alert-info">No assignments created yet.</div></div>
        <?php endif; ?>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
