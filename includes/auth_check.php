<?php
require_once __DIR__ . '/../config/app.php';

if (!isset($_SESSION['user'])) {
    redirect(url('index.php'));
}
