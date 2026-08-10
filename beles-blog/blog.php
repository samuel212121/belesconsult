<?php
require_once __DIR__ . '/includes/functions.php';

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$categorySlug = isset($_GET['category']) ? trim($_GET['category']) : null;

$totalPosts = count_published_posts($pdo, $categorySlug);
$totalPages = max(1, (int)ceil($totalPosts / POSTS_PER_PAGE));
$page = min($page, $totalPages);

$posts = get_published_posts($pdo, $page, POSTS_PER_PAGE, $categorySlug);
$featuredPost = ($page === 1 && !$categorySlug) ? get_featured_post($pdo) : null;
$categories = get_categories_with_counts($pdo);

// If we're showing the featured post separately, drop it from the regular list to avoid duplication
if ($featuredPost) {
    $posts = array_filter($posts, fn($p) => $p['id'] != $featuredPost['id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog &amp; News | Beles Consulting P.L.C</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/blog.css">
</head>
<body>

<!-- ══ SLIM NAV ══ -->
<nav class="slim-nav">
    <div class="slim-nav-inner">
        <a href="../index.html" class="slim-nav-logo">
            <img src="../logos/beles.png" alt="Beles Consulting" onerror="this.onerror=null;this.src='https://placehold.co/120x34/164780/ffffff?text=Beles'">
            <span>Beles <em>Consult</em></span>
        </a>
        <div class="slim-nav-links">
            <a href="../index.html">Home</a>
            <a href="../about.html">About</a>
            <a href="../services.html">Services</a>
            <a href="../projects.html">Projects</a>
            <a href="../contact.html">Contact</a>
            <a href="beles_blog/blog.php" class="active">Blog</a>

        </div>
        <div class="slim-nav-right">
            <a href="https://wa.me/251911281227" class="nav-cta-pill" target="_blank"><i class="fa-brands fa-whatsapp"></i> <span>Get a Quote</span></a>
            <button class="nav-burger" aria-label="Menu"><span></span><span></span><span></span></button>
        </div>
    </div>
</nav>

<!-- ══ HERO ══ -->
<section class="page-hero">
    <div class="hero-watermark"></div>
    <div class="hero-content">
        <div class="breadcrumb reveal-element">
            <a href="index.html">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Blog &amp; News</span>
        </div>
        <div class="hero-label reveal-element delay-1"><i class="fa-solid fa-newspaper"></i> From the Field</div>
        <h1 class="reveal-element delay-1">Engineering Insights <span>&amp; Project News</span></h1>
        <p class="reveal-element delay-2">Updates from our project sites, technical notes from our engineers, and perspectives on infrastructure development across Ethiopia.</p>
    </div>
</section>

<!-- ══ BLOG GRID ══ -->
<section class="blog-section">
    <div class="blog-inner">
        <div class="blog-filter-row reveal-element">
            <a href="blog.php" class="blog-filter-pill <?= !$categorySlug ? 'active' : '' ?>">All Posts</a>
            <?php foreach ($categories as $cat): ?>
                <a href="blog.php?category=<?= e($cat['slug']) ?>" class="blog-filter-pill <?= $categorySlug === $cat['slug'] ? 'active' : '' ?>">
                    <?= e($cat['name']) ?> <span class="pill-count">(<?= (int)$cat['post_count'] ?>)</span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!$totalPosts && !$featuredPost): ?>
            <div class="empty-state reveal-element">
                <i class="fa-regular fa-folder-open"></i>
                <p>No posts published yet in this category. Check back soon.</p>
            </div>
        <?php else: ?>

        <div class="blog-grid">

            <?php if ($featuredPost): ?>
            <a href="post.php?slug=<?= e($featuredPost['slug']) ?>" class="blog-card featured reveal-element" style="text-decoration:none; color:inherit;">
                <div class="blog-card-img">
                    <img src="<?= e($featuredPost['featured_image']) ?>" alt="<?= e($featuredPost['title']) ?>">
                    <?php if ($featuredPost['category_name']): ?>
                        <div class="blog-card-cat-badge"><?= e($featuredPost['category_name']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="blog-card-body">
                    <div class="featured-tag"><i class="fa-solid fa-star"></i> Featured Story</div>
                    <div class="blog-card-meta">
                        <span><i class="fa-regular fa-calendar"></i> <?= format_post_date($featuredPost['published_at']) ?></span>
                        <span><i class="fa-regular fa-user"></i> <?= e($featuredPost['author_name']) ?></span>
                    </div>
                    <h3><?= e($featuredPost['title']) ?></h3>
                    <p><?= e($featuredPost['excerpt']) ?></p>
                    <span class="blog-card-link">Read Full Story <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endif; ?>

            <?php foreach ($posts as $i => $post): ?>
            <a href="post.php?slug=<?= e($post['slug']) ?>" class="blog-card reveal-element <?= 'delay-' . (($i % 3) + 1) ?>" style="text-decoration:none; color:inherit;">
                <div class="blog-card-img">
                    <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>">
                    <?php if ($post['category_name']): ?>
                        <div class="blog-card-cat-badge"><?= e($post['category_name']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="blog-card-body">
                    <div class="blog-card-meta">
                        <span><i class="fa-regular fa-calendar"></i> <?= format_post_date($post['published_at']) ?></span>
                    </div>
                    <h3><?= e($post['title']) ?></h3>
                    <p><?= e($post['excerpt']) ?></p>
                    <span class="blog-card-link">Read More <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>

        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination-row reveal-element">
            <?php
            $catParam = $categorySlug ? '&category=' . urlencode($categorySlug) : '';
            for ($p = 1; $p <= $totalPages; $p++):
                if ($p > 1 && $p < $totalPages && abs($p - $page) > 2) {
                    if ($p == 2 || $p == $totalPages - 1) echo '<span class="page-btn dots">…</span>';
                    continue;
                }
            ?>
                <a href="blog.php?page=<?= $p . $catParam ?>" class="page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="blog.php?page=<?= ($page + 1) . $catParam ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<!-- ══ NEWSLETTER ══ -->
<section class="newsletter-band">
    <div class="newsletter-inner reveal-element">
        <h2>Subscribe for updates on our latest projects and engineering insights.</h2>
        <form class="newsletter-form">
            <input type="email" placeholder="Enter your email">
            <button type="submit">Subscribe <i class="fa-solid fa-arrow-right"></i></button>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/blog.js"></script>
</body>
</html>
