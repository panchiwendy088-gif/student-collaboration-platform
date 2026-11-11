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
        <div class="col-lg-7">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="mb-3 text-center">Create your account</h2>
                    <p class="text-muted text-center">Register as a student or lecturer.</p>
                    <?php if (isset($_SESSION['flash'])): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8'); ?>" role="alert">
                            <?php echo htmlspecialchars($_SESSION['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <?php unset($_SESSION['flash']); ?>
                    <?php endif; ?>
                    <form method="post" action="process_register.php">
                        <?php echo csrfField(); ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Full name</label>
                                <input id="full_name" name="full_name" type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email address</label>
                                <input id="email" name="email" type="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role</label>
                                <select id="role" name="role" class="form-select" required>
                                    <option value="student">Student</option>
                                    <option value="lecturer">Lecturer</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input id="phone" name="phone" type="tel" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" name="password" type="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm password</label>
                                <input id="confirm_password" name="confirm_password" type="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button class="btn btn-primary btn-lg" type="submit">Register</button>
                        </div>
                    </form>
                    <p class="mt-4 text-center mb-0">
                        Already have an account? <a href="login.php">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
