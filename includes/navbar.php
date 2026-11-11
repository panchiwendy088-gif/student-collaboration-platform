<?php
$user = currentUser();
$role = $user['role'] ?? 'guest';
$homeLink = $role === 'admin' ? 'admin/dashboard.php' : ($role === 'lecturer' ? 'lecturer/dashboard.php' : ($role === 'student' ? 'student/dashboard.php' : 'index.php'));
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo htmlspecialchars(url($homeLink), ENT_QUOTES, 'UTF-8'); ?>">
            <i class="fa-solid fa-graduation-cap me-2"></i><?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <?php if ($user): ?>
                    <?php if ($role === 'student'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('student/dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('student/courses.php'), ENT_QUOTES, 'UTF-8'); ?>">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('student/materials.php'), ENT_QUOTES, 'UTF-8'); ?>">Materials</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('student/assignments.php'), ENT_QUOTES, 'UTF-8'); ?>">Assignments</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('collaboration/discussions.php'), ENT_QUOTES, 'UTF-8'); ?>">Discussions</a></li>
                    <?php elseif ($role === 'lecturer'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('lecturer/dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('lecturer/courses.php'), ENT_QUOTES, 'UTF-8'); ?>">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('lecturer/materials.php'), ENT_QUOTES, 'UTF-8'); ?>">Materials</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('lecturer/assignments.php'), ENT_QUOTES, 'UTF-8'); ?>">Assignments</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('collaboration/discussions.php'), ENT_QUOTES, 'UTF-8'); ?>">Discussions</a></li>
                    <?php elseif ($role === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('admin/dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('admin/manage_students.php'), ENT_QUOTES, 'UTF-8'); ?>">Students</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('admin/manage_lecturers.php'), ENT_QUOTES, 'UTF-8'); ?>">Lecturers</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('admin/manage_courses.php'), ENT_QUOTES, 'UTF-8'); ?>">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('admin/reports.php'), ENT_QUOTES, 'UTF-8'); ?>">Reports</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if ($user): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user-circle me-2"></i><?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo htmlspecialchars(url('profile/edit_profile.php'), ENT_QUOTES, 'UTF-8'); ?>">Edit profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo htmlspecialchars(url('profile/change_password.php'), ENT_QUOTES, 'UTF-8'); ?>">Change password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo htmlspecialchars(url('auth/logout.php'), ENT_QUOTES, 'UTF-8'); ?>">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('auth/login.php'), ENT_QUOTES, 'UTF-8'); ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars(url('auth/register.php'), ENT_QUOTES, 'UTF-8'); ?>">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
