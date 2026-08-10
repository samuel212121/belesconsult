<?php
/**
 * ONE-TIME SETUP SCRIPT — creates your first admin account.
 *
 * HOW TO USE:
 * 1. Upload this file to your server inside the admin/ folder.
 * 2. Visit it in your browser once: https://yourdomain.com/admin/setup.php
 * 3. Fill in the form to create your admin account.
 * 4. DELETE THIS FILE from your server immediately afterward.
 *    (Leaving it live would let anyone create new admin accounts.)
 */

require_once __DIR__ . '/../config.php';

// Safety check: refuse to run if an admin already exists, unless explicitly confirmed
$existingCount = $pdo->query("SELECT COUNT(*) AS c FROM admins")->fetch()['c'];

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $message = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $message = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO admins (name, email, password_hash) VALUES (:name, :email, :hash)");
            $stmt->execute([':name' => $name, ':email' => $email, ':hash' => $hash]);
            $success = true;
            $message = 'Admin account created! You can now log in at admin/login.php. Please delete this setup.php file now.';
        } catch (PDOException $e) {
            $message = 'Could not create account — that email may already be in use.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Setup</title>
<style>
    body { font-family: Arial, sans-serif; background:#070d19; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; padding:20px; }
    .box { background:#fff; max-width:420px; width:100%; padding:36px; border-top:4px solid #7F0A1C; }
    h1 { font-size:1.2rem; color:#164780; margin-bottom:6px; }
    p.sub { color:#64748b; font-size:0.85rem; margin-bottom:20px; }
    label { display:block; font-size:0.78rem; font-weight:bold; color:#164780; margin:14px 0 6px 0; }
    input { width:100%; padding:10px; border:1px solid #e2e8f0; box-sizing:border-box; font-size:0.9rem; }
    button { margin-top:20px; width:100%; background:#7F0A1C; color:#fff; border:none; padding:12px; font-weight:bold; cursor:pointer; }
    .msg { padding:12px; margin-top:16px; font-size:0.85rem; }
    .msg.ok { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
    .msg.err { background:#fef2f2; color:#7F0A1C; border:1px solid #fecaca; }
    .warn { background:#fffbeb; color:#92620a; border:1px solid #fde68a; padding:12px; font-size:0.8rem; margin-bottom:16px; }
</style>
</head>
<body>
<div class="box">
    <h1>Create Admin Account</h1>
    <p class="sub">One-time setup for the Beles Consulting blog admin panel.</p>

    <?php if ($existingCount > 0 && !$success): ?>
        <div class="warn"><strong>Note:</strong> <?= (int)$existingCount ?> admin account(s) already exist. You can still create another one below, but consider deleting this file once done.</div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="msg <?= $success ? 'ok' : 'err' ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="post">
        <label>Full Name</label>
        <input type="text" name="name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required minlength="8">
        <label>Confirm Password</label>
        <input type="password" name="confirm" required minlength="8">
        <button type="submit">Create Admin Account</button>
    </form>
    <?php else: ?>
        <p style="margin-top:16px;"><a href="login.php">Go to Login →</a></p>
    <?php endif; ?>
</div>
</body>
</html>
