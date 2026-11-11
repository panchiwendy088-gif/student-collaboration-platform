<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(url('auth/register.php'));
}

if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token. Please try again.'];
    redirect(url('auth/register.php'));
}

$fullName = sanitizeText($_POST['full_name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$role = in_array($_POST['role'] ?? '', ['student', 'lecturer'], true) ? $_POST['role'] : 'student';
$phone = sanitizeText($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($fullName === '' || $email === '' || $password === '' || $confirmPassword === '') {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Please complete all required fields.'];
    redirect(url('auth/register.php'));
}

if ($password !== $confirmPassword) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Passwords do not match.'];
    redirect(url('auth/register.php'));
}

if (strlen($password) < 8) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Password must be at least 8 characters long.'];
    redirect(url('auth/register.php'));
}

$conn = getDatabaseConnection();
if ($conn === null) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Database connection failed. Please contact the administrator.'];
    redirect(url('auth/register.php'));
}

$checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$checkStmt->bind_param('s', $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
if ($checkResult->num_rows > 0) {
    $checkStmt->close();
    $_SESSION['flash'] = ['type' => 'warning', 'message' => 'An account with that email already exists.'];
    redirect(url('auth/register.php'));
}
$checkStmt->close();

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$insertStmt = $conn->prepare('INSERT INTO users (full_name, email, password_hash, role, phone, status, created_at) VALUES (?, ?, ?, ?, ?, "active", NOW())');
$insertStmt->bind_param('sssss', $fullName, $email, $passwordHash, $role, $phone);

if ($insertStmt->execute()) {
    $insertStmt->close();
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Your account was created successfully. You can log in now.'];
    redirect(url('auth/login.php'));
}

$insertStmt->close();
$_SESSION['flash'] = ['type' => 'danger', 'message' => 'Registration failed. Please try again.'];
redirect(url('auth/register.php'));
