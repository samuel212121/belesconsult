<?php
require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$post = $slug ? get_post_by_slug($pdo, $slug) : null;

if (!$post) {
    http_response_code(404);
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Post Not Found</title></head><body style='font-family:sans-serif;text-align:center;padding:100px 20px;'><h1>Post not found</h1><p><a href='blog.php'>← Back to Insights</a></p></body></html>";
    exit;
}

$categories = get_categories_with_counts($pdo);
$recentPosts = get_recent_posts($pdo, 3, $post['id']);
$allTags = get_all_tags($pdo);
$readTime = estimate_read_time($post['content']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($post['title']) ?> | Beles Consulting P.L.C</title>
    <meta name="description" content="<?= e($post['excerpt']) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/blog.css">
    <link rel="stylesheet" href="assets/css/post.css">
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

<!-- ══ POST HERO ══ -->
<section class="post-hero" style="background-image: linear-gradient(to top, rgba(7,13,25,0.97) 0%, rgba(7,13,25,0.55) 55%, rgba(7,13,25,0.3) 100%), url('<?= e($post['featured_image']) ?>');">
    <div class="post-hero-content">
        <div class="breadcrumb reveal-element">
            <a href="index.html">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="blog.php">Blog</a>
            <?php if ($post['category_name']): ?>
            <i class="fa-solid fa-chevron-right"></i>
            <span><?= e($post['category_name']) ?></span>
            <?php endif; ?>
        </div>
        <?php if ($post['category_name']): ?>
        <div class="post-cat-badge reveal-element"><?= e($post['category_name']) ?></div>
        <?php endif; ?>
        <h1 class="reveal-element"><?= e($post['title']) ?></h1>
        <div class="post-meta-row reveal-element">
            <div class="post-meta-item"><i class="fa-regular fa-calendar"></i> <?= format_post_date($post['published_at']) ?></div>
            <div class="post-meta-item"><i class="fa-regular fa-user"></i> <?= e($post['author_name']) ?></div>
            <div class="post-meta-item"><i class="fa-regular fa-clock"></i> <?= e($readTime) ?></div>
        </div>
    </div>
</section>

<!-- ══ MAIN LAYOUT ══ -->
<section class="post-layout-section">
    <div class="post-layout-inner">

        <!-- ── Article ── -->
        <article class="post-article reveal-element">
            <?= $post['content'] ?>

            <div class="post-footer-row">
                <?php if (!empty($post['tags'])): ?>
                <div class="post-tags">
                    <?php foreach ($post['tags'] as $tag): ?>
                        <a href="blog.php?tag=<?= e($tag['slug']) ?>" class="post-tag">#<?= e($tag['name']) ?></a>
                    <?php endforeach; ?>
                </div>
                <?php else: ?><div></div><?php endif; ?>
                <div class="post-share">
                    <span>Share</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . '/post.php?slug=' . $post['slug']) ?>" target="_blank" class="share-btn"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(SITE_URL . '/post.php?slug=' . $post['slug']) ?>" target="_blank" class="share-btn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' ' . SITE_URL . '/post.php?slug=' . $post['slug']) ?>" target="_blank" class="share-btn"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="author-box">
                <div class="author-avatar"><?= e(strtoupper(substr($post['author_name'], 0, 1))) ?></div>
                <div>
                    <h4>Written By</h4>
                    <strong><?= e($post['author_name']) ?></strong>
                    <p>Our in-house engineers and project coordinators share updates and technical perspectives from active project sites across Ethiopia.</p>
                </div>
            </div>
        </article>

        <!-- ── Sidebar ── -->
        <aside class="post-sidebar reveal-element">
            <div class="sidebar-box">
                <div class="sidebar-box-title">Search</div>
                <form class="sidebar-search" action="blog.php" method="get">
                    <input type="text" name="search" placeholder="Enter your keyword">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <div class="sidebar-box">
                <div class="sidebar-box-title">Categories</div>
                <div class="sidebar-cat-list">
                    <?php foreach ($categories as $cat): ?>
                        <a href="blog.php?category=<?= e($cat['slug']) ?>" class="sidebar-cat-item">
                            <?= e($cat['name']) ?> <span class="sidebar-cat-count"><?= (int)$cat['post_count'] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($recentPosts)): ?>
            <div class="sidebar-box">
                <div class="sidebar-box-title">Recent Posts</div>
                <?php foreach ($recentPosts as $rp): ?>
                    <a href="post.php?slug=<?= e($rp['slug']) ?>" class="sidebar-recent-item">
                        <div class="sidebar-recent-img"><img src="<?= e($rp['featured_image']) ?>" alt="<?= e($rp['title']) ?>"></div>
                        <div>
                            <div class="sidebar-recent-title"><?= e($rp['title']) ?></div>
                            <span class="sidebar-recent-date"><?= format_post_date($rp['published_at']) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($allTags)): ?>
            <div class="sidebar-box">
                <div class="sidebar-box-title">Tags</div>
                <div class="sidebar-tags-cloud">
                    <?php foreach ($allTags as $tag): ?>
                        <a href="blog.php?tag=<?= e($tag['slug']) ?>" class="sidebar-tag-chip"><?= e($tag['name']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="sidebar-box">
                <div class="sidebar-box-title">Follow Us</div>
                <div class="sidebar-social-row">
                    <a href="#" class="sidebar-social-btn"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="sidebar-social-btn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="#" class="sidebar-social-btn"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://wa.me/251911281227" target="_blank" class="sidebar-social-btn"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="sidebar-cta-box">
                <i class="fa-solid fa-drafting-compass"></i>
                <h4>Have a Project in Mind?</h4>
                <p>Talk to our engineering team about feasibility, design, or supervision for your next infrastructure project.</p>
                <a href="contact.html" class="sidebar-cta-btn">Get In Touch</a>
            </div>
        </aside>

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
