<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle delete action
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['delete']]);
    header('Location: dashboard.php?deleted=1');
    exit;
}

$posts = $pdo->query(
    "SELECT p.*, c.name AS category_name
     FROM posts p LEFT JOIN categories c ON p.category_id = c.id
     ORDER BY p.created_at DESC"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Beles Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include __DIR__ . '/partials/admin-nav.php'; ?>

<main class="admin-main">
    <div class="admin-header-row">
        <div>
            <div class="admin-eyebrow">Content Management</div>
            <h1>All Posts</h1>
        </div>
        <a href="post-edit.php" class="btn-primary"><i class="fa-solid fa-plus"></i> New Post</a>
    </div>

    <?php if (isset($_GET['saved'])): ?>
        <div class="flash-msg success"><i class="fa-solid fa-circle-check"></i> Post saved successfully.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="flash-msg success"><i class="fa-solid fa-circle-check"></i> Post deleted.</div>
    <?php endif; ?>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                    <tr><td colspan="5" class="empty-row">No posts yet. Click "New Post" to create your first one.</td></tr>
                <?php endif; ?>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td>
                        <strong><?= e($post['title']) ?></strong>
                        <?php if ($post['is_featured']): ?><span class="badge-featured"><i class="fa-solid fa-star"></i> Featured</span><?php endif; ?>
                    </td>
                    <td><?= e($post['category_name'] ?? '—') ?></td>
                    <td><span class="status-pill status-<?= $post['status'] ?>"><?= ucfirst($post['status']) ?></span></td>
                    <td><?= $post['published_at'] ? format_post_date($post['published_at']) : '—' ?></td>
                    <td class="actions-cell">
                        <a href="post-edit.php?id=<?= (int)$post['id'] ?>" class="action-btn" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <?php if ($post['status'] === 'published'): ?>
                        <a href="../post.php?slug=<?= e($post['slug']) ?>" target="_blank" class="action-btn" title="View"><i class="fa-solid fa-eye"></i></a>
                        <?php endif; ?>
                        <a href="dashboard.php?delete=<?= (int)$post['id'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Delete this post? This cannot be undone.');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
