<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['admin']);

$conn = getDatabaseConnection();
$lecturers = [];
if ($conn) {
    $stmt = $conn->prepare('SELECT id, full_name, email, status, created_at FROM users WHERE role = ? ORDER BY created_at DESC');
    $role = 'lecturer';
    $stmt->bind_param('s', $role);
    $stmt->execute();
    $lecturers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Manage lecturers</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back to dashboard</a>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($lecturers): ?>
                            <?php foreach ($lecturers as $lecturer): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($lecturer['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($lecturer['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><span class="badge bg-<?php echo $lecturer['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($lecturer['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td><?php echo htmlspecialchars(formatDate($lecturer['created_at']), ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No lecturers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
