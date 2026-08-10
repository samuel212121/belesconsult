<?php


define('DB_HOST', 'localhost');           
define('DB_NAME', 'beles_blog');          
define('DB_USER', 'root');    
define('DB_PASS', '');    
// ----------------------------------------------------------------

// Site settings
define('SITE_URL', 'https://belesconsult.com');   // change to your real domain
define('SITE_NAME', 'Beles Consulting P.L.C');
define('POSTS_PER_PAGE', 6);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
  
    die('Database connection failed. Please check config.php settings.');
}
