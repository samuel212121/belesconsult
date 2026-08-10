<?php

require_once __DIR__ . '/../config.php';

function make_slug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

/** Ensure a slug is unique in the posts table (appends -2, -3, etc. if needed) */
function unique_post_slug(PDO $pdo, $baseSlug, $excludeId = null) {
    $slug = $baseSlug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM posts WHERE slug = :slug" . ($excludeId ? " AND id != :id" : "");
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        if ($excludeId) $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        if (!$stmt->fetch()) break;
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
    return $slug;
}

function get_published_posts(PDO $pdo, $page = 1, $perPage = 6, $categorySlug = null) {
    $offset = max(0, ($page - 1) * $perPage);

    $where = "p.status = 'published'";
    $params = [];
    if ($categorySlug) {
        $where .= " AND c.slug = :cat";
        $params[':cat'] = $categorySlug;
    }

    $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE $where
            ORDER BY p.published_at DESC
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/** Count total published posts (for pagination), optionally filtered by category */
function count_published_posts(PDO $pdo, $categorySlug = null) {
    $where = "p.status = 'published'";
    $params = [];
    if ($categorySlug) {
        $where .= " AND c.slug = :cat";
        $params[':cat'] = $categorySlug;
    }
    $sql = "SELECT COUNT(*) AS total FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE $where";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->execute();
    return (int)$stmt->fetch()['total'];
}

/** Get a single published post by slug, including its tags */
function get_post_by_slug(PDO $pdo, $slug) {
    $stmt = $pdo->prepare(
        "SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM posts p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.slug = :slug AND p.status = 'published'
         LIMIT 1"
    );
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch();
    if (!$post) return null;

    $tagStmt = $pdo->prepare(
        "SELECT t.name, t.slug FROM tags t
         JOIN post_tags pt ON pt.tag_id = t.id
         WHERE pt.post_id = :id"
    );
    $tagStmt->execute([':id' => $post['id']]);
    $post['tags'] = $tagStmt->fetchAll();

    return $post;
}

/** Get all categories with a live count of published posts in each */
function get_categories_with_counts(PDO $pdo) {
    $sql = "SELECT c.id, c.name, c.slug, COUNT(p.id) AS post_count
            FROM categories c
            LEFT JOIN posts p ON p.category_id = c.id AND p.status = 'published'
            GROUP BY c.id, c.name, c.slug
            ORDER BY c.name ASC";
    return $pdo->query($sql)->fetchAll();
}

/** Get all tags */
function get_all_tags(PDO $pdo) {
    return $pdo->query("SELECT id, name, slug FROM tags ORDER BY name ASC")->fetchAll();
}

/** Get N most recent published posts, optionally excluding one post id */
function get_recent_posts(PDO $pdo, $limit = 3, $excludeId = null) {
    $where = "status = 'published'";
    $params = [];
    if ($excludeId) {
        $where .= " AND id != :exclude";
        $params[':exclude'] = $excludeId;
    }
    $sql = "SELECT id, title, slug, featured_image, published_at
            FROM posts WHERE $where
            ORDER BY published_at DESC LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/** Get the single featured post (is_featured = 1), or null */
function get_featured_post(PDO $pdo) {
    $stmt = $pdo->query(
        "SELECT p.*, c.name AS category_name, c.slug AS category_slug
         FROM posts p LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.status = 'published' AND p.is_featured = 1
         ORDER BY p.published_at DESC LIMIT 1"
    );
    return $stmt->fetch() ?: null;
}

/** Format a MySQL datetime into "June 14, 2026" style */
function format_post_date($datetime) {
    if (!$datetime) return '';
    return date('F j, Y', strtotime($datetime));
}

/** Rough reading time estimate based on word count */
function estimate_read_time($html) {
    $text = strip_tags($html);
    $words = str_word_count($text);
    $minutes = max(1, round($words / 200));
    return $minutes . ' min read';
}

/** Safely escape output for HTML */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
