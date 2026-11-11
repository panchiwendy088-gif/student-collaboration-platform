<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['student']);

$conn = getDatabaseConnection();
$materials = [];
$userId = $_SESSION['user']['id'];
if ($conn) {
    $stmt = $conn->prepare('SELECT m.id, m.title, m.file_name, m.file_path, c.title AS course_title FROM materials m JOIN courses c ON c.id = m.course_id WHERE m.course_id IN (SELECT course_id FROM enrollments WHERE student_id = ?) ORDER BY m.uploaded_at DESC');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $materials = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Learning materials</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Material</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($materials): ?>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($material['course_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($material['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><a href="<?php echo htmlspecialchars(url($material['file_path']), ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn btn-sm btn-primary">Download</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">No materials available for your enrolled courses.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
