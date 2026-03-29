<?php
/**
 * CoachProAI – Site Configuration
 * File: includes/config.php
 */

// Prevent direct access
if (!defined('COACHPROAI')) {
    define('COACHPROAI', true);
}

// Site settings
define('SITE_NAME', 'CoachProAI');
define('SITE_URL', 'https://calculators.coachproai.com');
define('SITE_TAGLINE', 'Free Fitness Calculators');

// Paths (relative to public/)
define('ASSETS_PATH', '/assets');
define('CSS_PATH', ASSETS_PATH . '/css');
define('JS_PATH', ASSETS_PATH . '/js');
define('IMAGES_PATH', ASSETS_PATH . '/images');

// External links
define('COACHING_URL', 'https://coachproai.com/start');
define('PRIVACY_URL', 'https://coachproai.com/privacy');
define('CONTACT_URL', 'https://coachproai.com/contact');
