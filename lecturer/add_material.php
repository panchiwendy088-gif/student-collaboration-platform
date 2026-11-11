<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin(['lecturer']);

$userId = $_SESSION['user']['id'];
$conn = getDatabaseConnection();
$courses = [];
if ($conn) {
    $stmt = $conn->prepare('SELECT id, code, title FROM courses WHERE lecturer_id = ? ORDER BY title');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $courseId = safeInt($_POST['course_id'], 0);
    $title = sanitizeText($_POST['title'] ?? '');
    $validation = validateUploadedFile($_FILES['material'] ?? [], ['pdf', 'doc', 'docx', 'ppt', 'pptx'], 10 * 1024 * 1024);
    if ($courseId <= 0 || $title === '' || !$validation['valid']) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => $validation['valid'] ? 'Please select a course and provide a valid title.' : $validation['message']];
        redirect(url('lecturer/add_material.php'));
    }
    $targetDir = __DIR__ . '/../uploads/materials/';
    ensureDirectory($targetDir);
    $storedName = uniqid('material_', true) . '.' . $validation['extension'];
    $target = $targetDir . $storedName;
    if (!move_uploaded_file($_FILES['material']['tmp_name'], $target)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'The file could not be uploaded.'];
        redirect(url('lecturer/add_material.php'));
    }

    if ($conn) {
        $stmt = $conn->prepare('INSERT INTO materials (course_id, lecturer_id, title, file_name, file_path, file_type, file_size, uploaded_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $fileName = $_FILES['material']['name'];
        $filePath = 'uploads/materials/' . $storedName;
        $fileType = $_FILES['material']['type'];
        $fileSize = (int) $_FILES['material']['size'];
        $stmt->bind_param('iisssii', $courseId, $userId, $title, $fileName, $filePath, $fileType, $fileSize);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Learning material uploaded successfully.'];
    redirect(url('lecturer/materials.php'));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Upload learning material</h2>
        <a href="materials.php" class="btn btn-outline-secondary">View materials</a>
    </div>
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <?php echo csrfField(); ?>
                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">Select course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars((string) $course['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($course['title'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">File</label>
                    <input type="file" name="material" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
