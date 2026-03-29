<?php
/**
 * CoachProAI – Fat Loss Timeline Calculator
 * File: public/fat-loss-timeline-calculator.php
 */

$pageTitle       = 'Fat Loss Timeline Calculator (Accurate & Free) | CoachProAI';
$pageDescription = 'Estimate how long it may take to reach your goal weight based on your current weight, target weight, and fat loss pace. Free fat loss timeline calculator with smart coaching insights.';
$pageCanonical   = 'https://calculators.coachproai.com/fat-loss-timeline-calculator.php';

require_once __DIR__ . '/../includes/header.php';

/* ------------------------------------------------------------------
   Server-side calculation
   ------------------------------------------------------------------ */

$errors = [];
$result = null;
$posted = (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST');

// Pace rates (kg per week)
$paceRates = [
    'slow'       => 0.25,
    'moderate'   => 0.50,
    'aggressive' => 0.75,
];

// Pace display labels
$paceLabels = [
    'slow'       => 'Slow (0.25 kg/week)',
    'moderate'   => 'Moderate (0.50 kg/week)',
    'aggressive' => 'Aggressive (0.75 kg/week)',
];

// Coach insights by pace
$paceInsights = [
    'slow'       => 'A slower pace is easier to sustain and often helps preserve energy, performance, and consistency.',
    'moderate'   => 'A moderate pace is usually the best balance between visible progress and long-term adherence.',
    'aggressive' => 'This timeline is aggressive and may be harder to sustain over time. Monitor hunger, recovery, and consistency closely.',
];

// Valid activity levels
$activityOptions = [
    'sedentary'  => 'Sedentary (little or no exercise)',
    'light'      => 'Light (1–3 days/week)',
    'moderate'   => 'Moderate (3–5 days/week)',
    'heavy'      => 'Heavy (6–7 days/week)',
    'very_heavy' => 'Very heavy (twice daily or physical job)',
];

if ($posted) {
    // Retrieve and sanitise inputs
    $weightUnit     = isset($_POST['weight_unit'])     ? trim($_POST['weight_unit'])     : 'kg';
    $currentWeightV = isset($_POST['current_weight'])  ? trim($_POST['current_weight'])  : '';
    $goalWeightV    = isset($_POST['goal_weight'])      ? trim($_POST['goal_weight'])      : '';
    $pace           = isset($_POST['pace'])             ? trim($_POST['pace'])             : '';
    $activity       = isset($_POST['activity'])         ? trim($_POST['activity'])         : '';

    // Validate weight unit
    if (!in_array($weightUnit, ['kg', 'lbs'], true)) {
        $weightUnit = 'kg';
    }

    // Validate current weight
    $currentWeightKg = null;
    if (!is_numeric($currentWeightV)) {
        $errors['current_weight'] = 'Please enter a valid current weight.';
    } else {
        $currentWeightKg = ($weightUnit === 'lbs') ? lbs_to_kg((float) $currentWeightV) : (float) $currentWeightV;
        if (!validate_weight($currentWeightKg, 30, 300)) {
            $errors['current_weight'] = 'Current weight must be between 30 kg (66 lbs) and 300 kg (661 lbs).';
        }
    }

    // Validate goal weight
    $goalWeightKg = null;
    if (!is_numeric($goalWeightV)) {
        $errors['goal_weight'] = 'Please enter a valid goal weight.';
    } else {
        $goalWeightKg = ($weightUnit === 'lbs') ? lbs_to_kg((float) $goalWeightV) : (float) $goalWeightV;
        if ($goalWeightKg < 25) {
            $errors['goal_weight'] = 'Goal weight must be at least 25 kg (55 lbs).';
        }
    }

    // Cross-field validation: goal must be strictly less than current
    if ($currentWeightKg !== null && $goalWeightKg !== null && empty($errors['current_weight']) && empty($errors['goal_weight'])) {
        if ($goalWeightKg >= $currentWeightKg) {
            $errors['goal_weight'] = 'Goal weight must be lower than your current weight.';
        }
    }

    // Validate pace
    if (!array_key_exists($pace, $paceRates)) {
        $errors['pace'] = 'Please select a valid weight loss pace.';
    }

    // Activity is optional – validate only if provided
    if ($activity !== '' && !array_key_exists($activity, $activityOptions)) {
        $errors['activity'] = 'Please select a valid activity level.';
    }

    // Calculate if no errors
    if (empty($errors) && $currentWeightKg !== null && $goalWeightKg !== null) {
        $weeklyRate  = $paceRates[$pace];
        $totalLossKg = $currentWeightKg - $goalWeightKg;
        $weeksToGoal = $totalLossKg / $weeklyRate;
        $monthsToGoal = $weeksToGoal / 4.345;
        $targetDate  = date('F j, Y', strtotime('+' . ceil($weeksToGoal) . ' weeks'));

        // Display-unit conversions
        $currentWeightDisplay = ($weightUnit === 'lbs')
            ? round((float) $currentWeightV, 1) . ' lbs'
            : round($currentWeightKg, 1) . ' kg';
        $goalWeightDisplay = ($weightUnit === 'lbs')
            ? round((float) $goalWeightV, 1) . ' lbs'
            : round($goalWeightKg, 1) . ' kg';
        $totalLossDisplay = ($weightUnit === 'lbs')
            ? round(kg_to_lbs($totalLossKg), 1) . ' lbs'
            : round($totalLossKg, 1) . ' kg';
        $weeklyRateDisplay = ($weightUnit === 'lbs')
            ? round(kg_to_lbs($weeklyRate), 2) . ' lbs/week'
            : $weeklyRate . ' kg/week';

        $result = [
            'weeks'             => round($weeksToGoal, 1),
            'months'            => round($monthsToGoal, 1),
            'target_date'       => $targetDate,
            'total_loss'        => $totalLossDisplay,
            'weekly_rate'       => $weeklyRateDisplay,
            'pace'              => $pace,
            'pace_label'        => $paceLabels[$pace],
            'coach_insight'     => $paceInsights[$pace],
            'is_aggressive'     => ($pace === 'aggressive'),
            'large_goal'        => ($totalLossKg > 20),
            'current_display'   => $currentWeightDisplay,
            'goal_display'      => $goalWeightDisplay,
        ];
    }
}

// Safe re-population helpers (scoped to this file)
function postVal(string $key, string $default = ''): string {
    return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key]), ENT_QUOTES, 'UTF-8') : $default;
}
function postChecked(string $key, string $value): string {
    return (isset($_POST[$key]) && $_POST[$key] === $value) ? ' aria-pressed="true"' : '';
}
function postSelected(string $key, string $value): string {
    return (isset($_POST[$key]) && $_POST[$key] === $value) ? ' selected' : '';
}
?>

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Fat Loss Timeline Calculator (Accurate &amp; Free) | CoachProAI",
        "description": "Estimate how long it may take to reach your goal weight based on your current weight, target weight, and fat loss pace. Free fat loss timeline calculator with smart coaching insights.",
        "url": "https://calculators.coachproai.com/fat-loss-timeline-calculator.php",
        "publisher": {
            "@type": "Organization",
            "name": "CoachProAI",
            "url": "https://calculators.coachproai.com"
        }
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "How long does it take to lose weight safely?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Safe, sustainable weight loss typically ranges from 0.25 to 0.75 kg (0.5–1.5 lbs) per week, depending on your starting weight and calorie deficit. At a moderate pace of 0.5 kg/week, losing 10 kg would take roughly 20 weeks. Faster rates are possible but carry a higher risk of muscle loss, fatigue, and rebound."
                }
            },
            {
                "@type": "Question",
                "name": "What is a realistic fat loss pace?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "For most people, 0.5 kg (1 lb) per week is a realistic and well-researched fat loss pace. This requires a calorie deficit of roughly 500 kcal/day and is considered sustainable by most sports nutrition guidelines. Slower paces are often easier to maintain long-term, especially when training performance matters."
                }
            },
            {
                "@type": "Question",
                "name": "Is faster weight loss better?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Not necessarily. Faster weight loss (0.75 kg/week or more) can produce quicker results but increases the risk of muscle breakdown, hormonal disruption, low energy, and poor training performance. Most research supports moderate, consistent deficits for better long-term body composition outcomes."
                }
            },
            {
                "@type": "Question",
                "name": "Can I use lbs in this calculator?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. Toggle the unit switcher next to the weight fields to switch between kg and lbs. The calculator auto-converts your values between units, and all internal calculations are performed in kg before displaying the result in your chosen unit."
                }
            },
            {
                "@type": "Question",
                "name": "Why is my real progress slower than the estimate?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Real-world fat loss is rarely perfectly linear. Water retention, hormonal fluctuations, muscle gain, and variations in adherence all affect the scale. Your estimate is a planning baseline, not a guarantee. Expect the scale to plateau briefly and remember that consistent effort over weeks and months is what drives results."
                }
            }
        ]
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Fat Loss Timeline Calculator",
        "applicationCategory": "HealthApplication",
        "operatingSystem": "Web",
        "description": "A free web-based fat loss timeline calculator that estimates how long it will take to reach your goal weight based on your current weight, target weight, and chosen fat loss pace.",
        "url": "https://calculators.coachproai.com/fat-loss-timeline-calculator.php",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
        },
        "provider": {
            "@type": "Organization",
            "name": "CoachProAI"
        }
    }
    </script>

    <!-- HERO -->
    <section class="calc-hero" id="calculator">
        <div class="container">
            <div class="calc-hero-content">
                <span class="hero-label">Free fat loss timeline calculator</span>
                <h1>How Long Will It Take to Reach Your Goal Weight?</h1>
                <p class="hero-subtitle">
                    This calculator estimates a realistic fat loss timeline based on your current
                    weight, target weight, and chosen pace — so you can set expectations, plan
                    milestones, and stay focused on what matters.
                </p>
                <div class="hero-badges">
                    <span class="hero-badge">
                        <svg class="hero-badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        Works with kg or lbs
                    </span>
                    <span class="hero-badge">
                        <svg class="hero-badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        Shows weeks and months
                    </span>
                    <span class="hero-badge">
                        <svg class="hero-badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        Includes coaching insight
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- CALCULATOR + RESULT -->
    <section class="content-section calc-section">
        <div class="container">
            <div class="calc-layout">

                <!-- LEFT: Calculator Form -->
                <div class="calc-form-col">
                    <div class="calc-form">
                        <h2 class="calc-form-title">Enter your details</h2>

                        <?php if (!empty($errors)): ?>
                        <div class="form-error-banner" role="alert">
                            <strong>Please fix the following:</strong>
                            <ul>
                                <?php foreach ($errors as $err): ?>
                                <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="#calculator" novalidate>

                            <!-- Current weight -->
                            <div class="form-group">
                                <div class="form-label-row">
                                    <label for="current_weight" class="form-label">Current weight</label>
                                    <div class="unit-toggle" role="group" aria-label="Weight unit">
                                        <button type="button" class="unit-btn<?php echo ($posted && postVal('weight_unit') === 'lbs') ? '' : ' active'; ?>"
                                                data-unit="kg" id="wunit-kg">kg</button>
                                        <button type="button" class="unit-btn<?php echo ($posted && postVal('weight_unit') === 'lbs') ? ' active' : ''; ?>"
                                                data-unit="lbs" id="wunit-lbs">lbs</button>
                                        <input type="hidden" name="weight_unit" id="weight-unit-hidden"
                                               value="<?php echo $posted ? postVal('weight_unit', 'kg') : 'kg'; ?>">
                                    </div>
                                </div>
                                <input type="number" name="current_weight" id="current_weight" class="form-input"
                                       placeholder="e.g. 85"
                                       value="<?php echo postVal('current_weight', ''); ?>"
                                       min="1" step="0.1" required>
                                <?php if (isset($errors['current_weight'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['current_weight'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Goal weight -->
                            <div class="form-group">
                                <label for="goal_weight" class="form-label">
                                    Goal weight <span class="form-label-hint">(same unit as above)</span>
                                </label>
                                <input type="number" name="goal_weight" id="goal_weight" class="form-input"
                                       placeholder="e.g. 75"
                                       value="<?php echo postVal('goal_weight', ''); ?>"
                                       min="1" step="0.1" required>
                                <?php if (isset($errors['goal_weight'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['goal_weight'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Weight loss pace -->
                            <div class="form-group">
                                <label for="pace" class="form-label">Weight loss pace</label>
                                <select name="pace" id="pace" class="form-select" required>
                                    <option value="" disabled<?php echo !$posted ? ' selected' : ''; ?>>Select your pace</option>
                                    <option value="slow"<?php echo postSelected('pace', 'slow'); ?>>Slow (0.25 kg / 0.55 lbs per week)</option>
                                    <option value="moderate"<?php echo postSelected('pace', 'moderate'); ?>>Moderate (0.50 kg / 1.1 lbs per week)</option>
                                    <option value="aggressive"<?php echo postSelected('pace', 'aggressive'); ?>>Aggressive (0.75 kg / 1.65 lbs per week)</option>
                                </select>
                                <?php if (isset($errors['pace'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['pace'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Activity level (optional) -->
                            <div class="form-group">
                                <label for="activity" class="form-label">
                                    Activity level <span class="form-label-hint">(optional)</span>
                                </label>
                                <select name="activity" id="activity" class="form-select">
                                    <option value=""<?php echo !$posted ? ' selected' : ''; ?>>Select activity level (optional)</option>
                                    <?php foreach ($activityOptions as $key => $label): ?>
                                    <option value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"<?php echo postSelected('activity', $key); ?>>
                                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['activity'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['activity'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="calc-btn btn btn-primary">
                                Calculate my timeline
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </button>

                        </form>
                    </div>
                </div>

                <!-- RIGHT: Result Card -->
                <div class="calc-result-col">
                    <?php if ($result): ?>
                    <div class="result-card" id="result" role="region" aria-label="Fat loss timeline calculation result">

                        <div class="result-header">
                            <span class="result-label">Estimated weeks to goal</span>
                            <div class="result-main">
                                <span class="result-number"><?php echo htmlspecialchars((string) $result['weeks'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="result-unit">weeks</span>
                            </div>
                        </div>

                        <div class="result-stats">
                            <div class="result-stat">
                                <span class="stat-value"><?php echo htmlspecialchars((string) $result['months'], ENT_QUOTES, 'UTF-8'); ?> months</span>
                                <span class="stat-label">estimated duration</span>
                                <span class="stat-title">Timeline</span>
                            </div>
                            <div class="result-stat">
                                <span class="stat-value"><?php echo htmlspecialchars($result['total_loss'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="stat-label">total to lose</span>
                                <span class="stat-title">Goal Delta</span>
                            </div>
                            <div class="result-stat">
                                <span class="stat-value"><?php echo htmlspecialchars($result['weekly_rate'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="stat-label"><?php echo htmlspecialchars($result['pace'], ENT_QUOTES, 'UTF-8'); ?> pace</span>
                                <span class="stat-title">Weekly Rate</span>
                            </div>
                            <div class="result-stat">
                                <span class="stat-value"><?php echo htmlspecialchars($result['target_date'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="stat-label">estimated arrival</span>
                                <span class="stat-title">Target Date</span>
                            </div>
                        </div>

                        <?php if ($result['large_goal']): ?>
                        <div class="realism-note">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                            <p>Large fat loss goals usually happen in phases, not in one perfectly linear stretch. Focus on one phase at a time for the best results.</p>
                        </div>
                        <?php endif; ?>

                        <div class="result-insight">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                            <p><?php echo htmlspecialchars($result['coach_insight'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>

                        <div class="result-cta">
                            <a href="<?php echo COACHING_URL; ?>" class="btn btn-primary">
                                Unlock full fat loss plan
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                    </div>
                    <?php else: ?>
                    <div class="result-placeholder">
                        <div class="result-placeholder-icon" aria-hidden="true">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        </div>
                        <h3>Your timeline will appear here</h3>
                        <p>Fill in the form and click <strong>Calculate my timeline</strong> to get your personalised fat loss estimate.</p>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- WHAT THIS FAT LOSS TIMELINE MEANS -->
    <section class="content-section bg-white">
        <div class="container">
            <div class="content-prose-section">
                <span class="section-label">Understanding your timeline</span>
                <h2 class="section-title">What this fat loss timeline means</h2>
                <div class="content-prose">
                    <p>
                        The timeline this calculator produces is a planning estimate — not a guarantee.
                        It assumes a consistent weekly calorie deficit that matches your chosen pace, with
                        no interruptions. In practice, fat loss rarely follows a perfectly straight line.
                        Water retention, hormonal shifts, temporary plateaus, and variations in daily
                        activity all influence how quickly the scale moves week to week.
                    </p>
                    <p>
                        Real outcomes depend heavily on adherence, sleep quality, stress levels, training
                        intensity, and recovery. Think of your estimated date as a motivational target, not
                        a deadline. If you stay consistent with your deficit and training plan, the overall
                        trajectory will move you toward your goal — even if week-to-week progress looks
                        uneven along the way.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY USE THIS CALCULATOR -->
    <section class="content-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Built for clarity</span>
                <h2 class="section-title">Why use this fat loss timeline calculator</h2>
            </div>
            <div class="why-grid">

                <div class="why-card">
                    <div class="why-icon-circle" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    </div>
                    <h3>Set realistic expectations</h3>
                    <p>
                        Knowing how long it realistically takes to reach your goal weight prevents
                        frustration and keeps you grounded when early progress feels slow. Realistic
                        expectations are one of the strongest predictors of long-term adherence.
                    </p>
                </div>

                <div class="why-card">
                    <div class="why-icon-circle" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                    </div>
                    <h3>Plan better milestones</h3>
                    <p>
                        A timeline helps you break a large goal into smaller, more manageable phases.
                        Instead of fixating on a single end point, you can celebrate progress at each
                        milestone — which sustains motivation across longer journeys.
                    </p>
                </div>

                <div class="why-card">
                    <div class="why-icon-circle" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    </div>
                    <h3>Stay focused on consistency</h3>
                    <p>
                        Seeing your estimated target date keeps the big picture visible during the
                        inevitable tough weeks. Consistency — not perfection — is what drives fat
                        loss. Your timeline gives that consistency a direction and a destination.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="content-section bg-white faq-section" id="faq">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Common questions</span>
                <h2 class="section-title">Frequently asked questions</h2>
            </div>

            <div class="faq-list">

                <details class="faq-item">
                    <summary class="faq-question">How long does it take to lose weight safely?</summary>
                    <div class="faq-answer">
                        <p>Safe, sustainable weight loss typically ranges from 0.25 to 0.75 kg (0.5–1.5 lbs) per week depending on your starting weight and deficit size. At a moderate pace of 0.5 kg/week, losing 10 kg would take around 20 weeks. Most nutrition guidelines consider 0.5–1% of body weight per week a healthy upper limit for preserving muscle and staying healthy.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">What is a realistic fat loss pace?</summary>
                    <div class="faq-answer">
                        <p>For most people, 0.5 kg (approximately 1 lb) per week is both realistic and well-supported by research. It requires a sustained calorie deficit of around 500 kcal/day and is achievable without severe restriction. Slower paces (0.25 kg/week) are often easier to maintain over many months, especially when training performance is a priority.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Is faster weight loss better?</summary>
                    <div class="faq-answer">
                        <p>Not necessarily. Losing weight faster than 1% of body weight per week significantly increases the risk of muscle breakdown, hormonal disruption, persistent hunger, and training performance drops. Most people who cut aggressively struggle with adherence or rebound after the diet ends. A moderate, consistent pace typically produces better long-term body composition results.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Can I use lbs in this calculator?</summary>
                    <div class="faq-answer">
                        <p>Yes — toggle the unit switcher next to the weight inputs to switch between kg and lbs. When you switch units, both your current weight and goal weight values are automatically converted. All internal calculations use kg, and the results are displayed in your chosen unit.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Why is my real progress slower than the estimate?</summary>
                    <div class="faq-answer">
                        <p>Real-world fat loss is rarely perfectly linear. Water retention, hormonal fluctuations, muscle gain, inaccurate calorie tracking, and temporary plateaus all affect what the scale shows week to week. The estimate this calculator provides is a planning baseline assuming perfect adherence. Expect some deviation and focus on the overall trend over 4–6 weeks rather than day-to-day fluctuations.</p>
                    </div>
                </details>

            </div>
        </div>
    </section>

    <!-- RELATED CALCULATORS -->
    <section class="content-section related-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">More tools</span>
                <h2 class="section-title">Related calculators</h2>
            </div>
            <div class="related-grid">

                <a href="/protein-calculator-for-fat-loss.php" class="related-card">
                    <div class="related-card-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="related-card-body">
                        <h3>Protein Calculator for Fat Loss</h3>
                        <p>Find out exactly how much protein you need to preserve muscle while losing fat.</p>
                    </div>
                    <svg class="related-card-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>

                <a href="/calorie-deficit-calculator-to-lose-weight.php" class="related-card">
                    <div class="related-card-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div class="related-card-body">
                        <h3>Calorie Deficit Calculator</h3>
                        <p>Calculate your daily calorie target to lose weight at your chosen pace.</p>
                    </div>
                    <svg class="related-card-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>

                <a href="/weight-loss-plateau-calculator.php" class="related-card">
                    <div class="related-card-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div class="related-card-body">
                        <h3>Weight Loss Plateau Calculator</h3>
                        <p>Diagnose why your progress has stalled and find out what to adjust to start losing again.</p>
                    </div>
                    <svg class="related-card-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>

            </div>
        </div>
    </section>

    <!-- FINAL CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-banner">
                <div class="cta-banner-inner">
                    <span class="cta-label">Go beyond the estimate</span>
                    <h2>Ready to Build a Complete Fat Loss Plan?</h2>
                    <p>
                        Your timeline estimate is a powerful starting point. A complete plan combines
                        your target date with the right calorie target, macro split, protein goal,
                        training programme, and progress tracking — all tailored to your body and goals.
                    </p>
                    <div class="cta-banner-actions">
                        <a href="#calculator" class="btn btn-outline">Recalculate now</a>
                        <a href="<?php echo COACHING_URL; ?>" class="btn btn-accent">Start full plan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Unit toggle JavaScript -->
    <script>
    (function () {
        'use strict';

        /* ── Weight unit toggle ──────────────────────────────── */
        var wKgBtn      = document.getElementById('wunit-kg');
        var wLbsBtn     = document.getElementById('wunit-lbs');
        var wHidden     = document.getElementById('weight-unit-hidden');
        var wCurrent    = document.getElementById('current_weight');
        var wGoal       = document.getElementById('goal_weight');

        function convertWeightInput(input, fromUnit, toUnit) {
            var val = parseFloat(input.value);
            if (!isNaN(val) && val > 0) {
                if (toUnit === 'lbs' && fromUnit === 'kg') {
                    input.value = Math.round(CoachProAI.kgToLbs(val) * 10) / 10;
                } else if (toUnit === 'kg' && fromUnit === 'lbs') {
                    input.value = Math.round(CoachProAI.lbsToKg(val) * 10) / 10;
                }
            }
        }

        function setWeightUnit(unit) {
            var current = wHidden.value;
            if (current === unit) return;
            convertWeightInput(wCurrent, current, unit);
            convertWeightInput(wGoal, current, unit);
            wHidden.value = unit;
            wKgBtn.classList.toggle('active', unit === 'kg');
            wLbsBtn.classList.toggle('active', unit === 'lbs');
            wKgBtn.setAttribute('aria-pressed', unit === 'kg' ? 'true' : 'false');
            wLbsBtn.setAttribute('aria-pressed', unit === 'lbs' ? 'true' : 'false');
        }

        if (wKgBtn && wLbsBtn) {
            wKgBtn.addEventListener('click', function () { setWeightUnit('kg'); });
            wLbsBtn.addEventListener('click', function () { setWeightUnit('lbs'); });
        }

    }());
    </script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
