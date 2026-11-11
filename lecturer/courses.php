<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['lecturer']);

$conn = getDatabaseConnection();
$courses = [];
$userId = $_SESSION['user']['id'];
if ($conn) {
    $stmt = $conn->prepare('SELECT id, code, title, description, status FROM courses WHERE lecturer_id = ? ORDER BY created_at DESC');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">My courses</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
    </div>
    <div class="row g-4">
        <?php if ($courses): ?>
            <?php foreach ($courses as $course): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($course['code'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="badge bg-<?php echo $course['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($course['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="text-muted"><?php echo htmlspecialchars($course['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No courses assigned yet.</div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
