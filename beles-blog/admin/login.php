<?php
require_once __DIR__ . '/../config.php';

// If already logged in, go straight to dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Beles Consulting</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --navy-brand:#164780; --maroon-brand:#7F0A1C; --blue-accent:#1587D2; --bg-dark:#070d19; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--bg-dark); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .login-box { background:#fff; width:100%; max-width:380px; padding:44px 36px; border-top:4px solid var(--maroon-brand); }
        .login-logo { text-align:center; margin-bottom:30px; }
        .login-logo span { font-size:1.1rem; font-weight:800; color:var(--navy-brand); }
        .login-logo span em { color:var(--blue-accent); font-style:normal; }
        .login-box h1 { font-size:1.2rem; font-weight:800; color:var(--navy-brand); margin-bottom:6px; text-align:center; }
        .login-box p.sub { font-size:0.82rem; color:#64748b; text-align:center; margin-bottom:28px; }
        .field { margin-bottom:18px; }
        .field label { display:block; font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:var(--navy-brand); margin-bottom:7px; }
        .field input { width:100%; padding:12px 14px; border:1.5px solid #e2e8f0; font-family:inherit; font-size:0.9rem; outline:none; transition:border-color 0.2s; }
        .field input:focus { border-color:var(--blue-accent); }
        .login-btn { width:100%; background:var(--maroon-brand); color:#fff; border:none; padding:13px; font-family:inherit; font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; cursor:pointer; transition:background 0.2s; margin-top:6px; }
        .login-btn:hover { background:#9e0d23; }
        .error-msg { background:rgba(127,10,28,0.08); border:1px solid rgba(127,10,28,0.25); color:var(--maroon-brand); font-size:0.82rem; padding:12px 14px; margin-bottom:20px; }
        .back-link { display:block; text-align:center; margin-top:22px; font-size:0.78rem; color:#64748b; text-decoration:none; }
        .back-link:hover { color:var(--navy-brand); }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo"><span>Beles <em>Consult</em></span></div>
        <h1>Admin Login</h1>
        <p class="sub">Sign in to manage blog posts</p>
        <?php if ($error): ?>
            <div class="error-msg"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Sign In</button>
        </form>
        <a href="../blog.php" class="back-link">← Back to Site</a>
    </div>
</body>
</html>
