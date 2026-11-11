<?php
require_once __DIR__ . '/../config/app.php';

if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    redirect(url($role === 'admin' ? 'admin/dashboard.php' : ($role === 'lecturer' ? 'lecturer/dashboard.php' : 'student/dashboard.php')));
}

include __DIR__ . '/../includes/header.php';
?>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="mb-3 text-center">Welcome back</h2>
                    <p class="text-muted text-center">Sign in to continue to your workspace.</p>
                    <?php if (isset($_SESSION['flash'])): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8'); ?>" role="alert">
                            <?php echo htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <?php unset($_SESSION['flash']); ?>
                    <?php endif; ?>
                    <form method="post" action="process_login.php">
                        <?php echo csrfField(); ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input id="email" type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password" class="form-control" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-lg" type="submit">Login</button>
                        </div>
                    </form>
                    <p class="mt-4 text-center mb-0">
                        Need an account? <a href="register.php">Create one here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
