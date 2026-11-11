<?php

define('APP_NAME', 'Cloud Student Collaboration Platform');
define('APP_URL', 'http://localhost');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

function url(string $path): string
{
    $path = ltrim($path, '/');
    return APP_URL . '/' . $path;
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function requireLogin(array $allowedRoles = []): void
{
    if (!isset($_SESSION['user'])) {
        redirect(url('index.php'));
    }

    if ($allowedRoles !== [] && !in_array($_SESSION['user']['role'], $allowedRoles, true)) {
        redirect(url('index.php'));
    }
}

function sanitizeText(string $value): string
{
    return trim(strip_tags($value));
}

function formatDate(?string $date, string $format = 'M j, Y'): string
{
    if (empty($date)) {
        return '—';
    }

    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : '—';
}

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function verifyCsrf(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function validateUploadedFile(array $file, array $allowedExtensions, int $maxBytes = 5242880): array
{
    if (!isset($file['name']) || $file['name'] === '') {
        return ['valid' => false, 'message' => 'No file was uploaded.'];
    }

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['valid' => false, 'message' => 'The uploaded file is invalid.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions, true)) {
        return ['valid' => false, 'message' => 'Unsupported file extension.'];
    }

    if ($file['size'] > $maxBytes) {
        return ['valid' => false, 'message' => 'The file exceeds the allowed size.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'message' => 'The upload failed. Please try again.'];
    }

    return ['valid' => true, 'extension' => $ext];
}

function safeInt($value, int $default = 0): int
{
    return is_numeric($value) ? (int) $value : $default;
}

function ensureDirectory(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
    }
}
