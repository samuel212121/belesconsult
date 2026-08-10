<nav class="admin-nav">
    <div class="admin-nav-inner">
        <a href="dashboard.php" class="admin-logo">Beles <em>Admin</em></a>
        <div class="admin-nav-right">
            <span class="admin-user"><i class="fa-solid fa-user-circle"></i> <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
            <a href="../blog.php" target="_blank" class="admin-nav-link"><i class="fa-solid fa-eye"></i> View Site</a>
            <a href="logout.php" class="admin-nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</nav>
