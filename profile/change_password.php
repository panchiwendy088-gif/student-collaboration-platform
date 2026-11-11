<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $user = currentUser();
    $conn = getDatabaseConnection();

    if ($newPassword !== $confirmPassword || strlen($newPassword) < 8) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'New password must match confirmation and be at least 8 characters.'];
        redirect(url('profile/change_password.php'));
    }

    if ($conn && $user) {
        $stmt = $conn->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row && password_verify($oldPassword, $row['password_hash'])) {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $updateStmt->bind_param('si', $newHash, $user['id']);
            $updateStmt->execute();
            $updateStmt->close();
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Password updated successfully.'];
            redirect(url('profile/change_password.php'));
        }
    }

    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Current password is incorrect.'];
    redirect(url('profile/change_password.php'));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Change password</h2>
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
                <div class="mb-3">
                    <label class="form-label">Current password</label>
                    <input type="password" name="old_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Update password</button>
            </form>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
