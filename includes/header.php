<?php
/**
 * CoachProAI – Shared Header
 * File: includes/header.php
 *
 * Variables expected before include:
 *   $pageTitle       – <title> text (required)
 *   $pageDescription – meta description (required)
 *   $pageCanonical   – canonical URL (optional)
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$pageTitle       = $pageTitle       ?? SITE_TAGLINE . ' | ' . SITE_NAME;
$pageDescription = $pageDescription ?? 'Free fitness calculators powered by CoachProAI.';
$pageCanonical   = $pageCanonical   ?? SITE_URL . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <title><?php echo sanitize($pageTitle); ?></title>
    <meta name="description" content="<?php echo sanitize($pageDescription); ?>">
    <link rel="canonical" href="<?php echo sanitize($pageCanonical); ?>">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?php echo sanitize($pageCanonical); ?>">
    <meta property="og:title"       content="<?php echo sanitize($pageTitle); ?>">
    <meta property="og:description" content="<?php echo sanitize($pageDescription); ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Brand Stylesheet -->
    <link rel="stylesheet" href="<?php echo CSS_PATH; ?>/brand.css">

    <!-- Shared Calculator JS -->
    <script src="<?php echo JS_PATH; ?>/calculators.js" defer></script>
</head>
<body>

    <!-- HEADER / NAVBAR -->
    <header class="site-header" role="banner">
        <div class="container">
            <div class="header-inner">

                <!-- Brand -->
                <a href="/" class="brand-logo" aria-label="<?php echo SITE_NAME; ?> Home">
                    CoachPro<span class="accent">AI</span>
                </a>

                <!-- Primary Navigation -->
                <nav class="site-nav" id="site-nav" aria-label="Main navigation">
                    <a href="/">Home</a>
                    <a href="/#calculators">Calculators</a>
                    <a href="/#why-coachproai">About</a>
                </nav>

                <!-- CTA + Mobile Toggle -->
                <div class="header-cta" style="display:flex;align-items:center;gap:0.75rem;">
                    <a href="<?php echo COACHING_URL; ?>" class="btn btn-primary btn-sm">Start Coaching</a>
                    <button class="nav-toggle" id="nav-toggle" aria-controls="site-nav" aria-expanded="false" aria-label="Toggle navigation">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <main id="main-content" role="main">
