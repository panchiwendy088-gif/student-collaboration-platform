<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin();

$conn = getDatabaseConnection();
$user = currentUser();
if ($conn && $user) {
    $stmt = $conn->prepare('SELECT full_name, email, phone, bio FROM users WHERE id = ?');
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $fullName = sanitizeText($_POST['full_name'] ?? '');
    $phone = sanitizeText($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    if ($conn && $user) {
        $stmt = $conn->prepare('UPDATE users SET full_name = ?, phone = ?, bio = ?, updated_at = NOW() WHERE id = ?');
        $stmt->bind_param('sssi', $fullName, $phone, $bio, $user['id']);
        $stmt->execute();
        $stmt->close();
        $_SESSION['user']['full_name'] = $fullName;
    }
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile updated successfully.'];
    redirect(url('profile/edit_profile.php'));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Edit profile</h2>
        <a href="<?php echo htmlspecialchars(url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-secondary">Home</a>
    </div>
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="post">
                <?php echo csrfField(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($profile['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($profile['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" rows="4" class="form-control"><?php echo htmlspecialchars($profile['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                </div>
                <button class="btn btn-primary mt-4" type="submit">Save profile</button>
            </form>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
