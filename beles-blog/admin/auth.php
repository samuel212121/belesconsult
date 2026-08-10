<?php
/**
 * Include this at the very top of any admin page that requires login.
 */
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
