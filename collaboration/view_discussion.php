<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin();

$id = safeInt($_GET['id'] ?? 0, 0);
$conn = getDatabaseConnection();
$discussion = null;
$replies = [];
if ($conn && $id > 0) {
    $stmt = $conn->prepare('SELECT d.id, d.title, d.content, d.created_at, u.full_name AS author_name, c.title AS course_title FROM discussions d JOIN users u ON u.id = d.user_id JOIN courses c ON c.id = d.course_id WHERE d.id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $discussion = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $replyStmt = $conn->prepare('SELECT r.id, r.content, r.created_at, u.full_name AS author_name FROM discussion_replies r JOIN users u ON u.id = r.user_id WHERE r.discussion_id = ? ORDER BY r.created_at ASC');
    $replyStmt->bind_param('i', $id);
    $replyStmt->execute();
    $replies = $replyStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $replyStmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $discussionId = safeInt($_POST['discussion_id'], 0);
    $content = trim($_POST['content'] ?? '');
    if ($discussionId > 0 && $content !== '' && $conn) {
        $stmt = $conn->prepare('INSERT INTO discussion_replies (discussion_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())');
        $userId = $_SESSION['user']['id'];
        $stmt->bind_param('iis', $discussionId, $userId, $content);
        $stmt->execute();
        $stmt->close();
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Reply posted successfully.'];
    }
    redirect(url('collaboration/view_discussion.php?id=' . $discussionId));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Discussion</h2>
        <a href="discussions.php" class="btn btn-outline-secondary">Back to discussions</a>
    </div>
    <?php if ($discussion): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0"><?php echo htmlspecialchars($discussion['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                    <span class="badge bg-primary"><?php echo htmlspecialchars($discussion['course_title'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <p class="text-muted small mb-3">Posted by <?php echo htmlspecialchars($discussion['author_name'], ENT_QUOTES, 'UTF-8'); ?> on <?php echo htmlspecialchars(formatDate($discussion['created_at'], 'M d, Y'), ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($discussion['content'], ENT_QUOTES, 'UTF-8')); ?></p>
            </div>
        </div>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="mb-3">Replies</h5>
                <?php if ($replies): ?>
                    <?php foreach ($replies as $reply): ?>
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong><?php echo htmlspecialchars($reply['author_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <small class="text-muted"><?php echo htmlspecialchars(formatDate($reply['created_at'], 'M d, Y'), ENT_QUOTES, 'UTF-8'); ?></small>
                            </div>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($reply['content'], ENT_QUOTES, 'UTF-8')); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="mb-0 text-muted">No replies yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3">Add a reply</h5>
                <form method="post">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="discussion_id" value="<?php echo (int) $discussion['id']; ?>">
                    <div class="mb-3">
                        <textarea name="content" rows="4" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Post reply</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">Discussion not found.</div>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
