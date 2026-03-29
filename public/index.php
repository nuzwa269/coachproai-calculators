<?php
/**
 * CoachProAI – Calculators Hub Page
 * File: public/index.php
 * URL:  https://calculators.coachproai.com/
 */

$pageTitle       = 'Free Fitness Calculators | CoachProAI';
$pageDescription = 'Use free fitness calculators for fat loss, muscle gain, calorie deficit, and more. Smart tools powered by CoachProAI.';
$pageCanonical   = 'https://calculators.coachproai.com/';

require_once __DIR__ . '/../includes/header.php';
?>

        <!-- ===================================================
             HERO SECTION
        ==================================================== -->
        <section class="hero" aria-labelledby="hero-heading">
            <div class="container">
                <div class="hero-content">

                    <span class="hero-label">CoachProAI Tools</span>

                    <h1 id="hero-heading">
                        Free Fitness<br>
                        <span class="highlight">Calculators</span>
                    </h1>

                    <p class="hero-subtitle">
                        Evidence-based tools to help you lose fat, build lean muscle,
                        and plan your nutrition with confidence — powered by real coaching logic.
                    </p>

                    <div class="hero-actions">
                        <a href="#calculators" class="btn btn-primary">Explore Calculators</a>
                        <a href="https://coachproai.com/start" class="btn btn-outline">Start Coaching</a>
                    </div>

                    <!-- Value Badges -->
                    <div class="hero-badges" aria-label="Key features">
                        <div class="hero-badge">
                            <!-- Fire icon -->
                            <svg class="hero-badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2c0 0-4 4-4 8a4 4 0 0 0 8 0c0-1-.5-2-1-3 0 0-1 2-2 2s-1-1-1-3c0-2 1-4 1-4z"/>
                                <path d="M9 18c-1.1.5-2 1.4-2 2.5A2.5 2.5 0 0 0 9.5 23h5a2.5 2.5 0 0 0 2.5-2.5c0-1.1-.9-2-2-2.5"/>
                            </svg>
                            Fat Loss Tools
                        </div>
                        <div class="hero-badge">
                            <!-- Chart icon -->
                            <svg class="hero-badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                            Smart Nutrition Calculators
                        </div>
                        <div class="hero-badge">
                            <!-- Shield / coaching icon -->
                            <svg class="hero-badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 2l8 4v6c0 5-3.5 9.7-8 11-4.5-1.3-8-6-8-11V6l8-4z"/>
                            </svg>
                            Built for Real Coaching
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ===================================================
             CALCULATORS GRID
        ==================================================== -->
        <section class="calculators-section" id="calculators" aria-labelledby="calculators-heading">
            <div class="container">

                <header class="section-header">
                    <span class="section-label">Free Tools</span>
                    <h2 class="section-title" id="calculators-heading">Choose Your Calculator</h2>
                    <p class="section-subtitle">
                        Each tool is built around proven fitness principles to give you
                        accurate, actionable numbers — not generic estimates.
                    </p>
                </header>

                <div class="calc-grid">

                    <!-- Card 1: Protein Calculator -->
                    <article class="calc-card">
                        <div class="calc-card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <h3>Protein Calculator</h3>
                        <p>
                            Find out exactly how much protein you need daily to preserve muscle,
                            accelerate fat loss, and hit your body composition goals.
                        </p>
                        <a href="/protein-calculator-for-fat-loss.php" class="btn btn-ghost btn-sm">
                            Use Calculator →
                        </a>
                    </article>

                    <!-- Card 2: Calorie Deficit Calculator -->
                    <article class="calc-card">
                        <div class="calc-card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3h18v18H3z"/>
                                <path d="M3 9h18M9 21V9"/>
                            </svg>
                        </div>
                        <h3>Calorie Deficit Calculator</h3>
                        <p>
                            Calculate the right calorie deficit to lose weight steadily without
                            losing muscle or crashing your metabolism.
                        </p>
                        <a href="/calorie-deficit-calculator-to-lose-weight.php" class="btn btn-ghost btn-sm">
                            Use Calculator →
                        </a>
                    </article>

                    <!-- Card 3: Fat Loss Timeline Calculator -->
                    <article class="calc-card">
                        <div class="calc-card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <h3>Fat Loss Timeline Calculator</h3>
                        <p>
                            Set a realistic target and see exactly how long it will take
                            to reach your goal weight with a sustainable deficit.
                        </p>
                        <a href="/fat-loss-timeline-calculator.php" class="btn btn-ghost btn-sm">
                            Use Calculator →
                        </a>
                    </article>

                    <!-- Card 4: Weight Loss Plateau Calculator -->
                    <article class="calc-card">
                        <div class="calc-card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                        </div>
                        <h3>Weight Loss Plateau Calculator</h3>
                        <p>
                            Diagnose exactly why the scale has stalled and get a data-driven
                            plan to break through your current plateau.
                        </p>
                        <a href="/weight-loss-plateau-calculator.php" class="btn btn-ghost btn-sm">
                            Use Calculator →
                        </a>
                    </article>

                    <!-- Card 5: Lean Bulk Calculator -->
                    <article class="calc-card">
                        <div class="calc-card-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 8h1a4 4 0 0 1 0 8h-1"/>
                                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                                <line x1="6" y1="1" x2="6" y2="4"/>
                                <line x1="10" y1="1" x2="10" y2="4"/>
                                <line x1="14" y1="1" x2="14" y2="4"/>
                            </svg>
                        </div>
                        <h3>Lean Bulk Calculator</h3>
                        <p>
                            Build clean muscle with a precision surplus — calculated to maximise
                            muscle gain while keeping fat accumulation minimal.
                        </p>
                        <a href="/lean-bulk-calculator-clean-muscle-gain.php" class="btn btn-ghost btn-sm">
                            Use Calculator →
                        </a>
                    </article>

                </div>
            </div>
        </section>

        <!-- ===================================================
             WHY COACHPROAI SECTION
        ==================================================== -->
        <section class="why-section" id="why-coachproai" aria-labelledby="why-heading">
            <div class="container">

                <span class="section-label">Why CoachProAI</span>
                <h2 class="section-title" id="why-heading">Calculators Built for Real Results</h2>
                <p class="section-subtitle">
                    Most online calculators give generic outputs. Ours apply the same
                    coaching logic used by professional trainers to get you numbers that
                    actually work in the real world.
                </p>

                <div class="why-grid">

                    <!-- Feature 1 -->
                    <div class="why-card">
                        <div class="why-icon-circle" aria-hidden="true">
                            <!-- Flame / fat loss icon -->
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2c0 0-4 4-4 8a4 4 0 0 0 8 0c0-1-.5-2-1-3 0 0-1 2-2 2s-1-1-1-3c0-2 1-4 1-4z"/>
                                <path d="M9 18c-1.1.5-2 1.4-2 2.5A2.5 2.5 0 0 0 9.5 23h5a2.5 2.5 0 0 0 2.5-2.5c0-1.1-.9-2-2-2.5"/>
                            </svg>
                        </div>
                        <h3>Optimised for Fat Loss &amp; Muscle</h3>
                        <p>
                            Every formula is calibrated to support your body composition goals —
                            whether you're cutting, maintaining, or building lean mass — without
                            sacrificing the muscle you've worked hard to build.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="why-card">
                        <div class="why-icon-circle" aria-hidden="true">
                            <!-- Logic / brain icon -->
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 3H5a2 2 0 0 0-2 2v4"/>
                                <path d="M9 3h6"/>
                                <path d="M15 3h4a2 2 0 0 1 2 2v4"/>
                                <path d="M3 9v6"/>
                                <path d="M21 9v6"/>
                                <path d="M3 15v2a2 2 0 0 0 2 2h4"/>
                                <path d="M21 15v2a2 2 0 0 1-2 2h-4"/>
                                <path d="M9 21h6"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </div>
                        <h3>Practical Coaching Logic</h3>
                        <p>
                            These aren't textbook equations copied from a fitness article. Each
                            calculator applies the same practical methodology coaches use with
                            paying clients — adapted for self-service use.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="why-card">
                        <div class="why-icon-circle" aria-hidden="true">
                            <!-- People / real world icon -->
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <h3>Designed for Real-World Users</h3>
                        <p>
                            Simple inputs, clear outputs, zero jargon. Whether you're just starting
                            your fitness journey or you're an experienced athlete fine-tuning your
                            plan, these tools are built to be immediately useful.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- ===================================================
             CTA BANNER SECTION
        ==================================================== -->
        <section class="cta-section" aria-labelledby="cta-heading">
            <div class="container">
                <div class="cta-banner">
                    <div class="cta-banner-inner">
                        <span class="cta-label">Go Further</span>
                        <h2 id="cta-heading">Ready to Move Beyond the Calculators?</h2>
                        <p>
                            Numbers are a starting point. A full CoachProAI coaching plan turns
                            those numbers into a personalised roadmap — nutrition, training, and
                            accountability — tailored specifically to you.
                        </p>
                        <div class="cta-banner-actions">
                            <a href="https://coachproai.com/start" class="btn btn-accent">Start Full Plan</a>
                            <a href="#calculators" class="btn btn-outline">Browse Calculators</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
