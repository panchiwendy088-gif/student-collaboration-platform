<?php
require_once __DIR__ . '/config/app.php';
include __DIR__ . '/includes/header.php';
?>
<main class="hero-section">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">Cloud-based learning platform</span>
                <h1 class="display-4 fw-bold mt-4">Learn, collaborate, and succeed together.</h1>
                <p class="lead text-muted mt-3">A modern cloud-ready collaboration platform for students and lecturers.</p>
                <div class="d-flex gap-3 flex-wrap mt-4">
                    <a href="auth/login.php" class="btn btn-primary btn-lg">Login</a>
                    <a href="auth/register.php" class="btn btn-outline-primary btn-lg">Register</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-3">Platform highlights</h4>
                        <ul class="list-group list-group-flush fs-6">
                            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Role-based access</li>
                            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Assignments and grading</li>
                            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Course materials and announcements</li>
                            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Discussion forums</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
