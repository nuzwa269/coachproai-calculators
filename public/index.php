<?php
/**
 * CoachProAI – Calculators Subdomain Homepage
 * Served from: public/index.php
 * The full homepage is defined in the root index.php.
 * If your web server points document root to /public,
 * copy or symlink the root index.php and assets/ here,
 * or adjust your server configuration accordingly.
 */

// Include the root-level homepage
$rootIndex = dirname(__DIR__) . '/index.php';
if (file_exists($rootIndex)) {
    require $rootIndex;
} else {
    // Fallback: redirect to homepage
    header('Location: /');
    exit;
}
