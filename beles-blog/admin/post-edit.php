<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$postId = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int)$_GET['id'] : null;
$post = null;
$selectedTagIds = [];

if ($postId) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
    $stmt->execute([':id' => $postId]);
    $post = $stmt->fetch();
    if (!$post) { header('Location: dashboard.php'); exit; }

    $tagStmt = $pdo->prepare("SELECT tag_id FROM post_tags WHERE post_id = :id");
    $tagStmt->execute([':id' => $postId]);
    $selectedTagIds = array_column($tagStmt->fetchAll(), 'tag_id');
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
$allTags = get_all_tags($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $featuredImage = trim($_POST['featured_image'] ?? '');
    $categoryId = $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
    $authorName = trim($_POST['author_name'] ?? 'Beles Engineering Team');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $status = $_POST['status'] === 'published' ? 'published' : 'draft';
    $submittedTagIds = array_map('intval', $_POST['tags'] ?? []);

    if ($title === '' || $content === '') {
        $error = 'Title and content are required.';
    } else {
        $baseSlug = make_slug($title);
        $slug = unique_post_slug($pdo, $baseSlug, $postId);
        $publishedAt = ($status === 'published')
            ? (($post['published_at'] ?? null) ?: date('Y-m-d H:i:s'))
            : null;

        // If turning on featured, unset any other featured post first
        if ($isFeatured) {
            $pdo->exec("UPDATE posts SET is_featured = 0");
        }

        if ($postId) {
            $stmt = $pdo->prepare(
                "UPDATE posts SET title=:title, slug=:slug, excerpt=:excerpt, content=:content,
                 featured_image=:img, category_id=:cat, author_name=:author,
                 is_featured=:feat, status=:status, published_at=:pub
                 WHERE id=:id"
            );
            $stmt->execute([
                ':title' => $title, ':slug' => $slug, ':excerpt' => $excerpt, ':content' => $content,
                ':img' => $featuredImage, ':cat' => $categoryId, ':author' => $authorName,
                ':feat' => $isFeatured, ':status' => $status, ':pub' => $publishedAt, ':id' => $postId
            ]);
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO posts (title, slug, excerpt, content, featured_image, category_id, author_name, is_featured, status, published_at)
                 VALUES (:title, :slug, :excerpt, :content, :img, :cat, :author, :feat, :status, :pub)"
            );
            $stmt->execute([
                ':title' => $title, ':slug' => $slug, ':excerpt' => $excerpt, ':content' => $content,
                ':img' => $featuredImage, ':cat' => $categoryId, ':author' => $authorName,
                ':feat' => $isFeatured, ':status' => $status, ':pub' => $publishedAt
            ]);
            $postId = (int)$pdo->lastInsertId();
        }

        // Sync tags
        $pdo->prepare("DELETE FROM post_tags WHERE post_id = :id")->execute([':id' => $postId]);
        if (!empty($submittedTagIds)) {
            $tagStmt = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (:pid, :tid)");
            foreach ($submittedTagIds as $tid) {
                $tagStmt->execute([':pid' => $postId, ':tid' => $tid]);
            }
        }

        header('Location: dashboard.php?saved=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $postId ? 'Edit Post' : 'New Post' ?> | Beles Admin</title>
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
            <h1><?= $postId ? 'Edit Post' : 'New Post' ?></h1>
        </div>
        <a href="dashboard.php" class="btn-ghost"><i class="fa-solid fa-arrow-left"></i> Back to Posts</a>
    </div>

    <?php if ($error): ?>
        <div class="flash-msg error"><i class="fa-solid fa-triangle-exclamation"></i> <?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="post-form">
        <div class="form-grid">
            <div class="form-main">
                <div class="field">
                    <label>Title</label>
                    <input type="text" name="title" required value="<?= e($post['title'] ?? '') ?>" placeholder="e.g. Inside Package I: Six Bridges in SNNP Region">
                </div>
                <div class="field">
                    <label>Excerpt <span>(short summary shown on the blog grid)</span></label>
                    <textarea name="excerpt" rows="2" maxlength="400" placeholder="A one or two sentence summary..."><?= e($post['excerpt'] ?? '') ?></textarea>
                </div>
                <div class="field">
                    <label>Content <span>(HTML allowed — use &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt; etc.)</span></label>
                    <textarea name="content" rows="18" required class="content-textarea" placeholder="<p>Start writing your post...</p>"><?= e($post['content'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-side">
                <div class="side-box">
                    <label>Status</label>
                    <select name="status">
                        <option value="draft" <?= (($post['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= (($post['status'] ?? '') === 'published') ? 'selected' : '' ?>>Published</option>
                    </select>

                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" <?= !empty($post['is_featured']) ? 'checked' : '' ?>>
                        Show as Featured Story
                    </label>

                    <button type="submit" class="btn-primary full-width"><i class="fa-solid fa-floppy-disk"></i> Save Post</button>
                </div>

                <div class="side-box">
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">— None —</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (($post['category_id'] ?? null) == $cat['id']) ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Author Name</label>
                    <input type="text" name="author_name" value="<?= e($post['author_name'] ?? 'Beles Engineering Team') ?>">
                </div>

                <div class="side-box">
                    <label>Featured Image URL</label>
                    <input type="text" name="featured_image" value="<?= e($post['featured_image'] ?? '') ?>" placeholder="https://...">
                    <p class="hint">Paste an image URL, or upload to <code>assets/uploads/</code> via FTP and link it here.</p>
                </div>

                <div class="side-box">
                    <label>Tags</label>
                    <div class="tag-checkbox-list">
                        <?php foreach ($allTags as $tag): ?>
                            <label class="tag-checkbox">
                                <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>" <?= in_array($tag['id'], $selectedTagIds) ? 'checked' : '' ?>>
                                <?= e($tag['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

</body>
</html>
