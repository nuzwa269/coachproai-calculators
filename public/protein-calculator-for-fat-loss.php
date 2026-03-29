<?php
/**
 * CoachProAI – Protein Calculator for Fat Loss
 * File: public/protein-calculator-for-fat-loss.php
 */

$pageTitle       = 'Protein Calculator for Fat Loss (Accurate & Free) | CoachProAI';
$pageDescription = 'Calculate how much protein you need to lose fat based on your weight, goal, and activity level. Free accurate protein calculator with smart coaching insights.';
$pageCanonical   = 'https://calculators.coachproai.com/protein-calculator-for-fat-loss.php';

require_once __DIR__ . '/../includes/header.php';

/* ------------------------------------------------------------------
   Server-side calculation
   ------------------------------------------------------------------ */

$errors  = [];
$result  = null;
$posted  = (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST');

// Base protein multipliers (g/kg)
$baseMultipliers = [
    'lose_weight'        => 2.0,
    'maintain_weight'    => 1.6,
    'body_recomposition' => 2.2,
    'build_muscle'       => 1.8,
];

// Activity adjustments (added to base)
$activityAdjustments = [
    'sedentary'  => 0.00,
    'light'      => 0.05,
    'moderate'   => 0.08,
    'heavy'      => 0.12,
    'very_heavy' => 0.15,
];

// Coach insight by goal
$goalInsights = [
    'lose_weight'        => 'This target helps preserve muscle while dieting and may improve fullness between meals.',
    'maintain_weight'    => 'This target supports daily recovery and helps maintain lean body mass at your current weight.',
    'body_recomposition' => 'This target supports recovery and muscle retention while managing body fat.',
    'build_muscle'       => 'This target supports lean muscle gain without pushing protein unnecessarily high.',
];

if ($posted) {
    // Sanitise and retrieve inputs
    $sex          = isset($_POST['sex'])          ? trim($_POST['sex'])          : '';
    $goal         = isset($_POST['goal'])         ? trim($_POST['goal'])         : '';
    $weightUnit   = isset($_POST['weight_unit'])  ? trim($_POST['weight_unit'])  : 'kg';
    $weightVal    = isset($_POST['weight'])       ? trim($_POST['weight'])       : '';
    $heightUnit   = isset($_POST['height_unit'])  ? trim($_POST['height_unit'])  : 'cm';
    $heightCm     = isset($_POST['height_cm'])    ? trim($_POST['height_cm'])    : '';
    $heightFt     = isset($_POST['height_ft'])    ? trim($_POST['height_ft'])    : '';
    $heightIn     = isset($_POST['height_in'])    ? trim($_POST['height_in'])    : '';
    $activity     = isset($_POST['activity'])     ? trim($_POST['activity'])     : '';

    // Validate sex
    if (!in_array($sex, ['female', 'male'], true)) {
        $errors['sex'] = 'Please select a sex.';
    }

    // Validate goal
    if (!array_key_exists($goal, $baseMultipliers)) {
        $errors['goal'] = 'Please select a valid goal.';
    }

    // Validate and convert weight to kg
    $weightKg = null;
    if (!is_numeric($weightVal)) {
        $errors['weight'] = 'Please enter a valid weight.';
    } else {
        $weightKg = ($weightUnit === 'lbs') ? (float)$weightVal / 2.20462 : (float)$weightVal;
        if ($weightKg < 30 || $weightKg > 300) {
            $errors['weight'] = 'Weight must be between 30 kg (66 lbs) and 300 kg (661 lbs).';
        }
    }

    // Validate and convert height to cm
    $heightCmVal = null;
    if ($heightUnit === 'cm') {
        if (!is_numeric($heightCm)) {
            $errors['height'] = 'Please enter a valid height.';
        } else {
            $heightCmVal = (float)$heightCm;
            if ($heightCmVal < 100 || $heightCmVal > 250) {
                $errors['height'] = 'Height must be between 100 cm and 250 cm.';
            }
        }
    } else {
        if (!is_numeric($heightFt) || !is_numeric($heightIn)) {
            $errors['height'] = 'Please enter a valid height in feet and inches.';
        } else {
            $heightCmVal = ((int)$heightFt * 12 + (float)$heightIn) * 2.54;
            if ($heightCmVal < 100 || $heightCmVal > 250) {
                $errors['height'] = 'Height must be between 3ft 3in and 8ft 2in.';
            }
        }
    }

    // Validate activity
    if (!array_key_exists($activity, $activityAdjustments)) {
        $errors['activity'] = 'Please select a valid activity level.';
    }

    // Calculate if no errors
    if (empty($errors) && $weightKg !== null && $heightCmVal !== null) {
        $baseMultiplier     = $baseMultipliers[$goal];
        $activityAdj        = $activityAdjustments[$activity];
        $finalMultiplier    = $baseMultiplier + $activityAdj;
        $proteinGrams       = (int) round($weightKg * $finalMultiplier);
        $meals              = max(3, min(6, (int) round($proteinGrams / 35)));
        $proteinPerMeal     = (int) round($proteinGrams / $meals);

        $weightLbs          = round($weightKg * 2.20462, 1);
        $totalInches        = $heightCmVal / 2.54;
        $feetPart           = (int) floor($totalInches / 12);
        $inchesPart         = round($totalInches - ($feetPart * 12), 1);

        $coachInsight = $goalInsights[$goal] ?? '';
        if (in_array($activity, ['heavy', 'very_heavy'], true)) {
            $coachInsight .= ' Your activity level increases protein demand slightly.';
        }

        $result = [
            'protein_grams'    => $proteinGrams,
            'protein_per_meal' => $proteinPerMeal,
            'meals'            => $meals,
            'final_multiplier' => $finalMultiplier,
            'weight_kg'        => round($weightKg, 1),
            'weight_lbs'       => $weightLbs,
            'height_cm'        => round($heightCmVal, 1),
            'height_ft'        => $feetPart,
            'height_in'        => $inchesPart,
            'coach_insight'    => $coachInsight,
            'goal'             => $goal,
        ];
    }
}

// Safe re-population helpers
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
        "name": "Protein Calculator for Fat Loss (Accurate & Free) | CoachProAI",
        "description": "Calculate how much protein you need to lose fat based on your weight, goal, and activity level. Free accurate protein calculator with smart coaching insights.",
        "url": "https://calculators.coachproai.com/protein-calculator-for-fat-loss.php",
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
                "name": "How much protein do I need to lose fat?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Most research supports 1.6–2.2 g of protein per kg of body weight when in a calorie deficit. This range helps preserve lean muscle while your body burns fat for fuel. This calculator sets your target based on your goal and activity level."
                }
            },
            {
                "@type": "Question",
                "name": "Is protein important for fat loss?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. Protein has the highest thermic effect of all macronutrients, meaning your body burns more calories digesting it. It also promotes satiety, reduces muscle breakdown during a deficit, and helps maintain metabolic rate."
                }
            },
            {
                "@type": "Question",
                "name": "Can too much protein be bad?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "For healthy individuals, protein intakes up to 3 g/kg are generally well-tolerated. Excessively high intakes simply displace other nutrients without additional benefit. This calculator keeps your target within evidence-based ranges."
                }
            },
            {
                "@type": "Question",
                "name": "Should I use kg or lbs in this calculator?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Either works. The calculator automatically converts your input to kg internally for the calculation, then displays both units in the result. Use whichever unit you are most comfortable with."
                }
            },
            {
                "@type": "Question",
                "name": "Can this calculator help with body recomposition too?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. Select 'Body recomposition' as your goal and the calculator applies a slightly higher multiplier (2.2 g/kg) to support simultaneous muscle gain and fat loss."
                }
            }
        ]
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Protein Calculator for Fat Loss",
        "applicationCategory": "HealthApplication",
        "operatingSystem": "Web",
        "description": "A free web-based protein calculator that estimates daily protein needs for fat loss and muscle preservation based on body weight, goal, and activity level.",
        "url": "https://calculators.coachproai.com/protein-calculator-for-fat-loss.php",
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
                <span class="hero-label">Free protein calculator</span>
                <h1>How Much Protein Do You Need to Lose Fat?</h1>
                <p class="hero-subtitle">
                    This tool estimates your daily protein target for fat loss and muscle retention
                    using your body weight, goal, and activity level — giving you a number you can
                    actually act on.
                </p>
                <div class="hero-badges">
                    <span class="hero-badge">
                        <svg class="hero-badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        Works with kg or lbs
                    </span>
                    <span class="hero-badge">
                        <svg class="hero-badge-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        Height in cm or ft&nbsp;+&nbsp;in
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

                            <!-- Sex toggle -->
                            <div class="form-group">
                                <label class="form-label">Sex</label>
                                <div class="sex-toggle" role="group" aria-label="Select sex">
                                    <button type="button" class="sex-btn<?php echo (!$posted || postVal('sex') === 'female') ? ' active' : ''; ?>"
                                            data-value="female" id="sex-female"
                                            aria-pressed="<?php echo (!$posted || postVal('sex') === 'female') ? 'true' : 'false'; ?>">
                                        Female
                                    </button>
                                    <button type="button" class="sex-btn<?php echo ($posted && postVal('sex') === 'male') ? ' active' : ''; ?>"
                                            data-value="male" id="sex-male"
                                            aria-pressed="<?php echo ($posted && postVal('sex') === 'male') ? 'true' : 'false'; ?>">
                                        Male
                                    </button>
                                    <input type="hidden" name="sex" id="sex-hidden"
                                           value="<?php echo $posted ? postVal('sex', 'female') : 'female'; ?>">
                                </div>
                                <?php if (isset($errors['sex'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['sex'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Goal -->
                            <div class="form-group">
                                <label for="goal" class="form-label">Goal</label>
                                <select name="goal" id="goal" class="form-select" required>
                                    <option value="" disabled<?php echo !$posted ? ' selected' : ''; ?>>Select your goal</option>
                                    <option value="lose_weight"<?php echo postSelected('goal', 'lose_weight'); ?>>Lose weight</option>
                                    <option value="maintain_weight"<?php echo postSelected('goal', 'maintain_weight'); ?>>Maintain weight</option>
                                    <option value="body_recomposition"<?php echo postSelected('goal', 'body_recomposition'); ?>>Body recomposition</option>
                                    <option value="build_muscle"<?php echo postSelected('goal', 'build_muscle'); ?>>Build muscle</option>
                                </select>
                                <?php if (isset($errors['goal'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['goal'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Weight -->
                            <div class="form-group">
                                <div class="form-label-row">
                                    <label for="weight" class="form-label">Weight</label>
                                    <div class="unit-toggle" role="group" aria-label="Weight unit">
                                        <button type="button" class="unit-btn<?php echo ($posted && postVal('weight_unit') === 'lbs') ? '' : ' active'; ?>"
                                                data-unit="kg" id="wunit-kg">kg</button>
                                        <button type="button" class="unit-btn<?php echo ($posted && postVal('weight_unit') === 'lbs') ? ' active' : ''; ?>"
                                                data-unit="lbs" id="wunit-lbs">lbs</button>
                                        <input type="hidden" name="weight_unit" id="weight-unit-hidden"
                                               value="<?php echo $posted ? postVal('weight_unit', 'kg') : 'kg'; ?>">
                                    </div>
                                </div>
                                <input type="number" name="weight" id="weight" class="form-input"
                                       placeholder="e.g. 70"
                                       value="<?php echo postVal('weight', '70'); ?>"
                                       min="1" step="0.1" required>
                                <?php if (isset($errors['weight'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['weight'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Height -->
                            <div class="form-group">
                                <div class="form-label-row">
                                    <label class="form-label">Height</label>
                                    <div class="unit-toggle" role="group" aria-label="Height unit">
                                        <button type="button" class="unit-btn<?php echo ($posted && postVal('height_unit') === 'imperial') ? '' : ' active'; ?>"
                                                data-unit="cm" id="hunit-cm">cm</button>
                                        <button type="button" class="unit-btn<?php echo ($posted && postVal('height_unit') === 'imperial') ? ' active' : ''; ?>"
                                                data-unit="imperial" id="hunit-imperial">ft+in</button>
                                        <input type="hidden" name="height_unit" id="height-unit-hidden"
                                               value="<?php echo $posted ? postVal('height_unit', 'cm') : 'cm'; ?>">
                                    </div>
                                </div>

                                <div id="height-cm-wrap">
                                    <input type="number" name="height_cm" id="height_cm" class="form-input"
                                           placeholder="e.g. 170"
                                           value="<?php echo postVal('height_cm', '170'); ?>"
                                           min="1" step="0.1">
                                </div>

                                <div class="height-imperial" id="height-imperial-wrap" style="display:none;">
                                    <div class="height-imperial-inputs">
                                        <div>
                                            <input type="number" name="height_ft" id="height_ft" class="form-input"
                                                   placeholder="ft" min="0" step="1"
                                                   value="<?php echo postVal('height_ft', '5'); ?>">
                                            <span class="height-unit-label">ft</span>
                                        </div>
                                        <div>
                                            <input type="number" name="height_in" id="height_in" class="form-input"
                                                   placeholder="in" min="0" max="11" step="0.5"
                                                   value="<?php echo postVal('height_in', '0'); ?>">
                                            <span class="height-unit-label">in</span>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($errors['height'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['height'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Activity level -->
                            <div class="form-group">
                                <label for="activity" class="form-label">Activity level</label>
                                <select name="activity" id="activity" class="form-select" required>
                                    <option value="" disabled<?php echo !$posted ? ' selected' : ''; ?>>Select activity level</option>
                                    <option value="sedentary"<?php echo postSelected('activity', 'sedentary'); ?>>Sedentary (little or no exercise)</option>
                                    <option value="light"<?php echo postSelected('activity', 'light'); ?>>Light (1–3 days/week)</option>
                                    <option value="moderate"<?php echo postSelected('activity', 'moderate'); ?>>Moderate (3–5 days/week)</option>
                                    <option value="heavy"<?php echo postSelected('activity', 'heavy'); ?>>Heavy (6–7 days/week)</option>
                                    <option value="very_heavy"<?php echo postSelected('activity', 'very_heavy'); ?>>Very heavy (twice daily or physical job)</option>
                                </select>
                                <?php if (isset($errors['activity'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['activity'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="calc-btn btn btn-primary">
                                Calculate my protein
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </button>

                        </form>
                    </div>
                </div>

                <!-- RIGHT: Result Card -->
                <div class="calc-result-col">
                    <?php if ($result): ?>
                    <div class="result-card" id="result" role="region" aria-label="Protein calculation result">

                        <div class="result-header">
                            <span class="result-label">Your daily protein target</span>
                            <div class="result-main">
                                <span class="result-number"><?php echo $result['protein_grams']; ?></span>
                                <span class="result-unit">g / day</span>
                            </div>
                        </div>

                        <div class="result-stats">
                            <div class="result-stat">
                                <span class="stat-value"><?php echo $result['weight_kg']; ?> kg</span>
                                <span class="stat-label">/ <?php echo $result['weight_lbs']; ?> lbs</span>
                                <span class="stat-title">Body weight</span>
                            </div>
                            <div class="result-stat">
                                <span class="stat-value"><?php echo $result['height_cm']; ?> cm</span>
                                <span class="stat-label"><?php echo $result['height_ft']; ?> ft <?php echo $result['height_in']; ?> in</span>
                                <span class="stat-title">Height</span>
                            </div>
                            <div class="result-stat">
                                <span class="stat-value"><?php echo $result['final_multiplier']; ?> g/kg</span>
                                <span class="stat-label">multiplier used</span>
                                <span class="stat-title">Formula</span>
                            </div>
                            <div class="result-stat">
                                <span class="stat-value"><?php echo $result['protein_per_meal']; ?> g</span>
                                <span class="stat-label">across <?php echo $result['meals']; ?> meals</span>
                                <span class="stat-title">Per meal</span>
                            </div>
                        </div>

                        <div class="result-insight">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                            <p><?php echo htmlspecialchars($result['coach_insight'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>

                        <div class="result-cta">
                            <a href="<?php echo COACHING_URL; ?>" class="btn btn-primary">
                                Unlock full nutrition plan
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                    </div>
                    <?php else: ?>
                    <div class="result-placeholder">
                        <div class="result-placeholder-icon" aria-hidden="true">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        </div>
                        <h3>Your result will appear here</h3>
                        <p>Fill in the form and click <strong>Calculate my protein</strong> to get your personalised daily target.</p>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- WHAT YOUR PROTEIN INTAKE MEANS -->
    <section class="content-section bg-white">
        <div class="container">
            <div class="content-prose-section">
                <span class="section-label">Understanding your number</span>
                <h2 class="section-title">What your protein intake means</h2>
                <div class="content-prose">
                    <p>
                        When you are in a calorie deficit, your body does not exclusively burn fat — it also risks
                        breaking down muscle tissue for energy. Eating sufficient protein sends a signal to preserve
                        lean muscle, ensuring the weight you lose comes primarily from fat stores rather than hard-earned
                        muscle. Research consistently shows that higher protein intakes (around 1.6–2.2 g per kg) during
                        a calorie deficit lead to better body composition outcomes compared with lower intakes.
                    </p>
                    <p>
                        Beyond muscle retention, protein has the highest thermic effect of any macronutrient — your body
                        burns roughly 20–30% of the calories in protein just through digestion. It also triggers satiety
                        hormones more effectively than carbohydrates or fat, which can make sticking to a deficit far
                        less of a struggle. Hitting your daily protein target is one of the most evidence-backed
                        strategies for successful, sustainable fat loss.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY USE THIS CALCULATOR -->
    <section class="content-section">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Built for results</span>
                <h2 class="section-title">Why use this protein calculator</h2>
            </div>
            <div class="features-grid">

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3>Protect muscle</h3>
                    <p>
                        Evidence-based multipliers ensure your target is high enough to preserve lean mass
                        throughout a fat-loss phase, regardless of how aggressively you are cutting.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    </div>
                    <h3>Stay fuller longer</h3>
                    <p>
                        Higher protein intakes reduce hunger hormones and increase satiety signals, making it
                        easier to stay in a calorie deficit without constant cravings or energy crashes.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    </div>
                    <h3>Recover better</h3>
                    <p>
                        Adequate protein supports muscle repair after training sessions, keeping you performing
                        well even while in a deficit — so your workouts remain productive throughout your cut.
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
                    <summary class="faq-question">How much protein do I need to lose fat?</summary>
                    <div class="faq-answer">
                        <p>Most research supports 1.6–2.2 g of protein per kg of body weight when in a calorie deficit. This range helps preserve lean muscle while your body burns fat for fuel. This calculator sets your target based on your specific goal and activity level within that evidence-based window.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Is protein important for fat loss?</summary>
                    <div class="faq-answer">
                        <p>Yes — it is one of the most impactful nutrition variables for fat loss success. Protein has the highest thermic effect of all macronutrients, meaning your body burns more calories digesting it. It also promotes satiety, reduces muscle breakdown during a deficit, and helps maintain your metabolic rate.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Can too much protein be bad?</summary>
                    <div class="faq-answer">
                        <p>For healthy individuals, protein intakes up to 3 g/kg are generally well-tolerated and safe. Excessively high intakes beyond that simply displace other important nutrients without additional benefit. This calculator keeps your target within evidence-based ranges where the benefits are clear and risks are minimal.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Should I use kg or lbs in this calculator?</summary>
                    <div class="faq-answer">
                        <p>Either works — use whichever unit you are most comfortable with. The calculator automatically converts your input to kg internally for the calculation, then displays both units in the result panel. Simply toggle between kg and lbs using the unit switcher next to the weight field.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Can this calculator help with body recomposition too?</summary>
                    <div class="faq-answer">
                        <p>Yes. Select "Body recomposition" as your goal and the calculator applies a higher protein multiplier (2.2 g/kg) to support simultaneous muscle gain and fat loss. Body recomposition typically requires the highest protein intake of any goal, combined with a modest calorie surplus or maintenance calories and progressive resistance training.</p>
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

                <a href="/calorie-deficit-calculator-to-lose-weight.php" class="related-card">
                    <div class="related-card-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3z"/><path d="M3 9h18M9 21V9"/></svg>
                    </div>
                    <div class="related-card-body">
                        <h3>Calorie Deficit Calculator</h3>
                        <p>Find the right calorie deficit to lose weight without sacrificing muscle or metabolism.</p>
                    </div>
                    <svg class="related-card-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>

                <a href="/fat-loss-timeline-calculator.php" class="related-card">
                    <div class="related-card-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div class="related-card-body">
                        <h3>Fat Loss Timeline Calculator</h3>
                        <p>See a realistic timeline for reaching your goal weight based on your current deficit.</p>
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
                    <span class="cta-label">Go beyond the number</span>
                    <h2>Ready to Build a Full Nutrition Plan?</h2>
                    <p>
                        Your protein target is one piece of the puzzle. A complete plan covers your total
                        calories, macro split, meal timing, and training programme — tailored to your body and goals.
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
        var wInput      = document.getElementById('weight');

        function setWeightUnit(unit) {
            var val = parseFloat(wInput.value);
            if (!isNaN(val)) {
                if (unit === 'lbs' && wHidden.value === 'kg') {
                    wInput.value = Math.round(CoachProAI.kgToLbs(val) * 10) / 10;
                } else if (unit === 'kg' && wHidden.value === 'lbs') {
                    wInput.value = Math.round(CoachProAI.lbsToKg(val) * 10) / 10;
                }
            }
            wHidden.value = unit;
            wKgBtn.classList.toggle('active', unit === 'kg');
            wLbsBtn.classList.toggle('active', unit === 'lbs');
        }

        if (wKgBtn && wLbsBtn) {
            wKgBtn.addEventListener('click', function () { setWeightUnit('kg'); });
            wLbsBtn.addEventListener('click', function () { setWeightUnit('lbs'); });
        }

        /* ── Height unit toggle ──────────────────────────────── */
        var hCmBtn      = document.getElementById('hunit-cm');
        var hImperialBtn = document.getElementById('hunit-imperial');
        var hHidden     = document.getElementById('height-unit-hidden');
        var hCmWrap     = document.getElementById('height-cm-wrap');
        var hImpWrap    = document.getElementById('height-imperial-wrap');
        var hCmInput    = document.getElementById('height_cm');
        var hFtInput    = document.getElementById('height_ft');
        var hInInput    = document.getElementById('height_in');

        function setHeightUnit(unit) {
            if (unit === 'cm') {
                // Convert ft+in → cm
                var ft = parseFloat(hFtInput.value) || 0;
                var inch = parseFloat(hInInput.value) || 0;
                if (ft > 0 || inch > 0) {
                    hCmInput.value = Math.round(CoachProAI.feetInchesToCm(ft, inch));
                }
                hCmWrap.style.display = '';
                hImpWrap.style.display = 'none';
                hCmInput.required = true;
                hFtInput.required = false;
            } else {
                // Convert cm → ft+in
                var cm = parseFloat(hCmInput.value) || 0;
                if (cm > 0) {
                    var fi = CoachProAI.cmToFeetInches(cm);
                    hFtInput.value = fi.feet;
                    hInInput.value = fi.inches;
                }
                hCmWrap.style.display = 'none';
                hImpWrap.style.display = '';
                hFtInput.required = true;
                hCmInput.required = false;
            }
            hHidden.value = unit;
            hCmBtn.classList.toggle('active', unit === 'cm');
            hImperialBtn.classList.toggle('active', unit === 'imperial');
        }

        if (hCmBtn && hImperialBtn) {
            hCmBtn.addEventListener('click', function () { setHeightUnit('cm'); });
            hImperialBtn.addEventListener('click', function () { setHeightUnit('imperial'); });
        }

        // Restore height UI if imperial was posted
        if (hHidden && hHidden.value === 'imperial') {
            hCmWrap.style.display = 'none';
            hImpWrap.style.display = '';
        }

        /* ── Sex toggle ──────────────────────────────────────── */
        var sexBtns   = document.querySelectorAll('.sex-btn');
        var sexHidden = document.getElementById('sex-hidden');

        sexBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                sexBtns.forEach(function (b) {
                    b.classList.remove('active');
                    b.setAttribute('aria-pressed', 'false');
                });
                btn.classList.add('active');
                btn.setAttribute('aria-pressed', 'true');
                sexHidden.value = btn.getAttribute('data-value');
            });
        });

    }());
    </script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
