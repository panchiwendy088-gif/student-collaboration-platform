<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin();

$conn = getDatabaseConnection();
$discussions = [];
if ($conn) {
    $query = 'SELECT d.id, d.title, d.content, d.created_at, u.full_name AS author_name, c.title AS course_title FROM discussions d JOIN users u ON u.id = d.user_id JOIN courses c ON c.id = d.course_id ORDER BY d.created_at DESC';
    $result = $conn->query($query);
    if ($result) {
        $discussions = $result->fetch_all(MYSQLI_ASSOC);
    }
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Course discussions</h2>
        <a href="create_discussion.php" class="btn btn-primary">Create discussion</a>
    </div>
    <div class="list-group">
        <?php if ($discussions): ?>
            <?php foreach ($discussions as $discussion): ?>
                <a href="view_discussion.php?id=<?php echo (int) $discussion['id']; ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-1"><?php echo htmlspecialchars($discussion['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($discussion['course_title'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <p class="mb-1"><?php echo htmlspecialchars(substr($discussion['content'], 0, 140), ENT_QUOTES, 'UTF-8'); ?></p>
                    <small class="text-muted">By <?php echo htmlspecialchars($discussion['author_name'], ENT_QUOTES, 'UTF-8'); ?> on <?php echo htmlspecialchars(formatDate($discussion['created_at'], 'M d, Y'), ENT_QUOTES, 'UTF-8'); ?></small>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No discussion topics found yet.</div>
        <?php endif; ?>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
