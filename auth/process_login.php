<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(url('auth/login.php'));
}

if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token. Please try again.'];
    redirect(url('auth/login.php'));
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Email and password are required.'];
    redirect(url('auth/login.php'));
}

$conn = getDatabaseConnection();
if ($conn === null) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Database connection failed. Please contact the administrator.'];
    redirect(url('auth/login.php'));
}

$stmt = $conn->prepare('SELECT id, full_name, email, password_hash, role, status FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user['password_hash'])) {
    if ($user['status'] !== 'active') {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Your account is currently inactive.'];
        redirect(url('auth/login.php'));
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'You have been logged in successfully.'];
    $destination = $user['role'] === 'admin' ? 'admin/dashboard.php' : ($user['role'] === 'lecturer' ? 'lecturer/dashboard.php' : 'student/dashboard.php');
    redirect(url($destination));
}

$_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid email or password.'];
redirect(url('auth/login.php'));
