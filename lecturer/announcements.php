<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['lecturer']);

$conn = getDatabaseConnection();
$userId = $_SESSION['user']['id'];
$announcements = [];
if ($conn) {
    $stmt = $conn->prepare('SELECT a.id, a.title, a.message, a.created_at, c.title AS course_title FROM announcements a JOIN courses c ON c.id = a.course_id WHERE a.lecturer_id = ? ORDER BY a.created_at DESC');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $announcements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Announcements</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
    </div>
    <div class="list-group">
        <?php if ($announcements): ?>
            <?php foreach ($announcements as $announcement): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0"><?php echo htmlspecialchars($announcement['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <span class="badge bg-primary"><?php echo htmlspecialchars($announcement['course_title'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <p class="mb-1"><?php echo nl2br(htmlspecialchars($announcement['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <small class="text-muted"><?php echo htmlspecialchars(formatDate($announcement['created_at']), ENT_QUOTES, 'UTF-8'); ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No announcements yet.</div>
        <?php endif; ?>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
