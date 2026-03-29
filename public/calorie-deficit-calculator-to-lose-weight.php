<?php
/**
 * CoachProAI – Calorie Deficit Calculator to Lose Weight
 * File: public/calorie-deficit-calculator-to-lose-weight.php
 */

$pageTitle       = 'Calorie Deficit Calculator to Lose Weight (Accurate & Free) | CoachProAI';
$pageDescription = 'Calculate your calorie deficit for weight loss using your age, height, weight, and activity level. Free accurate calorie deficit calculator with smart coaching insights.';
$pageCanonical   = 'https://calculators.coachproai.com/calorie-deficit-calculator-to-lose-weight.php';

require_once __DIR__ . '/../includes/header.php';

/* ------------------------------------------------------------------
   Server-side calculation
   ------------------------------------------------------------------ */

$errors = [];
$result = null;
$posted = (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST');

// Activity multipliers
$activityMultipliers = [
    'sedentary'  => 1.2,
    'light'      => 1.375,
    'moderate'   => 1.55,
    'heavy'      => 1.725,
    'very_heavy' => 1.9,
];

// Deficit values (kcal/day) by pace
$paceDeficits = [
    'slow'       => 300,
    'moderate'   => 500,
    'aggressive' => 750,
];

// Coach insights by pace
$paceInsights = [
    'slow'       => 'A slower deficit is easier to sustain and may help preserve energy and training performance.',
    'moderate'   => 'A moderate deficit is usually the best balance between steady fat loss and long-term adherence.',
    'aggressive' => 'This deficit is aggressive and may be harder to sustain. Monitor hunger, recovery, and training quality closely.',
];

// Pace display labels
$paceLabels = [
    'slow'       => 'Slow',
    'moderate'   => 'Moderate',
    'aggressive' => 'Aggressive',
];

if ($posted) {
    // Retrieve and sanitise inputs
    $sex        = isset($_POST['sex'])          ? trim($_POST['sex'])         : '';
    $ageVal     = isset($_POST['age'])          ? trim($_POST['age'])         : '';
    $weightUnit = isset($_POST['weight_unit'])  ? trim($_POST['weight_unit']) : 'kg';
    $weightVal  = isset($_POST['weight'])       ? trim($_POST['weight'])      : '';
    $heightUnit = isset($_POST['height_unit'])  ? trim($_POST['height_unit']) : 'cm';
    $heightCm   = isset($_POST['height_cm'])    ? trim($_POST['height_cm'])   : '';
    $heightFt   = isset($_POST['height_ft'])    ? trim($_POST['height_ft'])   : '';
    $heightIn   = isset($_POST['height_in'])    ? trim($_POST['height_in'])   : '';
    $activity   = isset($_POST['activity'])     ? trim($_POST['activity'])    : '';
    $pace       = isset($_POST['pace'])         ? trim($_POST['pace'])        : '';

    // Validate sex
    if (!in_array($sex, ['female', 'male'], true)) {
        $errors['sex'] = 'Please select a sex.';
    }

    // Validate age
    $age = null;
    if (!is_numeric($ageVal)) {
        $errors['age'] = 'Please enter a valid age.';
    } else {
        $age = (int) $ageVal;
        if (!validate_age($age, 15, 100)) {
            $errors['age'] = 'Age must be between 15 and 100.';
        }
    }

    // Validate and convert weight to kg
    $weightKg = null;
    if (!is_numeric($weightVal)) {
        $errors['weight'] = 'Please enter a valid weight.';
    } else {
        $weightKg = ($weightUnit === 'lbs') ? lbs_to_kg((float) $weightVal) : (float) $weightVal;
        if (!validate_weight($weightKg, 30, 300)) {
            $errors['weight'] = 'Weight must be between 30 kg (66 lbs) and 300 kg (661 lbs).';
        }
    }

    // Validate and convert height to cm
    $heightCmVal = null;
    if ($heightUnit === 'cm') {
        if (!is_numeric($heightCm)) {
            $errors['height'] = 'Please enter a valid height.';
        } else {
            $heightCmVal = (float) $heightCm;
            if (!validate_height($heightCmVal, 100, 250)) {
                $errors['height'] = 'Height must be between 100 cm and 250 cm.';
            }
        }
    } else {
        if (!is_numeric($heightFt) || !is_numeric($heightIn)) {
            $errors['height'] = 'Please enter a valid height in feet and inches.';
        } else {
            $heightCmVal = feet_inches_to_cm((int) $heightFt, (float) $heightIn);
            if (!validate_height($heightCmVal, 100, 250)) {
                $errors['height'] = 'Height must be between 3 ft 3 in and 8 ft 2 in.';
            }
        }
    }

    // Validate activity level
    if (!array_key_exists($activity, $activityMultipliers)) {
        $errors['activity'] = 'Please select a valid activity level.';
    }

    // Validate pace
    if (!array_key_exists($pace, $paceDeficits)) {
        $errors['pace'] = 'Please select a valid weight loss pace.';
    }

    // Calculate if no errors
    if (empty($errors) && $weightKg !== null && $heightCmVal !== null && $age !== null) {
        // BMR via Mifflin-St Jeor
        $bmr = (10 * $weightKg) + (6.25 * $heightCmVal) - (5 * $age) + ($sex === 'male' ? 5 : -161);

        // TDEE
        $tdee = $bmr * $activityMultipliers[$activity];

        // Daily deficit and calorie target
        $deficit      = $paceDeficits[$pace];
        $dailyTarget  = max(1200, (int) round($tdee - $deficit));

        // Weekly estimated loss
        $weeklyLossKg = round_clean(($deficit * 7) / 7700, 2);

        // Display values
        $weightLbs   = round(kg_to_lbs($weightKg), 1);
        $heightFtIn  = cm_to_feet_inches($heightCmVal);

        $result = [
            'daily_target'  => $dailyTarget,
            'tdee'          => (int) round($tdee),
            'bmr'           => (int) round($bmr),
            'deficit'       => $deficit,
            'weekly_loss'   => $weeklyLossKg,
            'pace'          => $pace,
            'pace_label'    => $paceLabels[$pace],
            'coach_insight' => $paceInsights[$pace],
            'is_aggressive' => ($pace === 'aggressive'),
            'weight_kg'     => round($weightKg, 1),
            'weight_lbs'    => $weightLbs,
            'height_cm'     => round($heightCmVal, 1),
            'height_ft'     => $heightFtIn['feet'],
            'height_in'     => $heightFtIn['inches'],
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
        "name": "Calorie Deficit Calculator to Lose Weight (Accurate &amp; Free) | CoachProAI",
        "description": "Calculate your calorie deficit for weight loss using your age, height, weight, and activity level. Free accurate calorie deficit calculator with smart coaching insights.",
        "url": "https://calculators.coachproai.com/calorie-deficit-calculator-to-lose-weight.php",
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
                "name": "What is a calorie deficit?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "A calorie deficit occurs when you consume fewer calories than your body burns in a day. Your body then draws on stored fat for the extra energy it needs, which leads to fat loss over time. The size of the deficit determines how quickly you lose weight."
                }
            },
            {
                "@type": "Question",
                "name": "How big should my calorie deficit be?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "A moderate deficit of 300–500 kcal/day is recommended for most people. This typically produces 0.25–0.5 kg of fat loss per week — a rate that is sustainable and preserves lean muscle mass. Larger deficits can work but carry higher risk of muscle loss, fatigue, and poor adherence."
                }
            },
            {
                "@type": "Question",
                "name": "Is a bigger calorie deficit better?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Not necessarily. While a larger deficit does produce faster initial weight loss, it also increases the risk of muscle breakdown, micronutrient deficiencies, low energy, and poor training performance. Most research supports moderate deficits for better long-term body composition outcomes."
                }
            },
            {
                "@type": "Question",
                "name": "Can I use kg or lbs in this calculator?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Yes. Use whichever unit you prefer — the calculator auto-converts your input to kg internally. You can also enter your height in cm or in feet and inches. Simply toggle between units using the switcher next to each field."
                }
            },
            {
                "@type": "Question",
                "name": "Does activity level change my calorie deficit?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Your activity level affects your TDEE (Total Daily Energy Expenditure), which is the starting point for calculating your calorie target. A more active person burns more calories each day, so their maintenance calories — and therefore their deficit target — will be higher than a sedentary person of the same size."
                }
            }
        ]
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Calorie Deficit Calculator to Lose Weight",
        "applicationCategory": "HealthApplication",
        "operatingSystem": "Web",
        "description": "A free web-based calorie deficit calculator that estimates your daily calorie target for weight loss using the Mifflin-St Jeor formula, your activity level, and your chosen pace.",
        "url": "https://calculators.coachproai.com/calorie-deficit-calculator-to-lose-weight.php",
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
                <span class="hero-label">Free calorie deficit calculator</span>
                <h1>Calorie Deficit Calculator to Lose Weight</h1>
                <p class="hero-subtitle">
                    This calculator estimates how many calories you should eat each day to lose
                    weight at your chosen pace — using your age, sex, body weight, height, and
                    activity level to work out your personal TDEE and deficit target.
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
                        Shows calories, TDEE, and pace
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

                            <!-- Age -->
                            <div class="form-group">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" name="age" id="age" class="form-input"
                                       placeholder="e.g. 30"
                                       value="<?php echo postVal('age', ''); ?>"
                                       min="15" max="100" step="1" required>
                                <?php if (isset($errors['age'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['age'], ENT_QUOTES, 'UTF-8'); ?></p>
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
                                       placeholder="e.g. 75"
                                       value="<?php echo postVal('weight', ''); ?>"
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
                                           value="<?php echo postVal('height_cm', ''); ?>"
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

                            <!-- Weight loss pace -->
                            <div class="form-group">
                                <label for="pace" class="form-label">Weight loss pace</label>
                                <select name="pace" id="pace" class="form-select" required>
                                    <option value="" disabled<?php echo !$posted ? ' selected' : ''; ?>>Select your pace</option>
                                    <option value="slow"<?php echo postSelected('pace', 'slow'); ?>>Slow (−300 kcal/day, ~0.27 kg/week)</option>
                                    <option value="moderate"<?php echo postSelected('pace', 'moderate'); ?>>Moderate (−500 kcal/day, ~0.45 kg/week)</option>
                                    <option value="aggressive"<?php echo postSelected('pace', 'aggressive'); ?>>Aggressive (−750 kcal/day, ~0.68 kg/week)</option>
                                </select>
                                <?php if (isset($errors['pace'])): ?>
                                <p class="form-error"><?php echo htmlspecialchars($errors['pace'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="calc-btn btn btn-primary">
                                Calculate my deficit
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </button>

                        </form>
                    </div>
                </div>

                <!-- RIGHT: Result Card -->
                <div class="calc-result-col">
                    <?php if ($result): ?>
                    <div class="result-card" id="result" role="region" aria-label="Calorie deficit calculation result">

                        <div class="result-header">
                            <span class="result-label">Your daily calorie target</span>
                            <div class="result-main">
                                <span class="result-number"><?php echo format_number((float) $result['daily_target']); ?></span>
                                <span class="result-unit">kcal / day</span>
                            </div>
                        </div>

                        <div class="result-stats">
                            <div class="result-stat">
                                <span class="stat-value"><?php echo format_number((float) $result['tdee']); ?> kcal</span>
                                <span class="stat-label">maintenance calories</span>
                                <span class="stat-title">TDEE</span>
                            </div>
                            <div class="result-stat">
                                <span class="stat-value"><?php echo format_number((float) $result['bmr']); ?> kcal</span>
                                <span class="stat-label">at complete rest</span>
                                <span class="stat-title">BMR</span>
                            </div>
                            <div class="result-stat">
                                <span class="stat-value">−<?php echo format_number((float) $result['deficit']); ?> kcal</span>
                                <span class="stat-label"><?php echo htmlspecialchars($result['pace_label'], ENT_QUOTES, 'UTF-8'); ?> pace</span>
                                <span class="stat-title">Daily Deficit</span>
                            </div>
                            <div class="result-stat">
                                <span class="stat-value">~<?php echo htmlspecialchars($result['weekly_loss'], ENT_QUOTES, 'UTF-8'); ?> kg</span>
                                <span class="stat-label">estimated loss</span>
                                <span class="stat-title">Per Week</span>
                            </div>
                        </div>

                        <?php if ($result['is_aggressive']): ?>
                        <div class="result-warning">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <p>Aggressive deficits can be harder to sustain and may increase fatigue or hunger.</p>
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
                        <h3>Your result will appear here</h3>
                        <p>Fill in the form and click <strong>Calculate my deficit</strong> to get your personalised calorie target.</p>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- WHAT THIS CALORIE DEFICIT MEANS -->
    <section class="content-section bg-white">
        <div class="container">
            <div class="content-prose-section">
                <span class="section-label">Understanding your number</span>
                <h2 class="section-title">What this calorie deficit means</h2>
                <div class="content-prose">
                    <p>
                        A calorie deficit simply means you are consuming fewer calories than your body burns
                        each day. When you do this consistently, your body draws on stored body fat for the
                        energy it needs — leading to gradual, sustainable fat loss. This calculator works out
                        your Total Daily Energy Expenditure (TDEE) using the Mifflin-St Jeor formula, then
                        subtracts your chosen deficit to give you a practical daily calorie target.
                    </p>
                    <p>
                        The pace you select determines the size of the deficit and therefore the speed of your
                        progress. A moderate deficit of 500 kcal/day typically produces around 0.4–0.5 kg of
                        fat loss per week — a rate that most research supports as sustainable and protective
                        of lean muscle. If your calculated target is very close to the 1,200 kcal minimum
                        floor, consider choosing a slower pace or increasing your activity level to preserve
                        your metabolic rate and training performance.
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
                <h2 class="section-title">Why use this calorie deficit calculator</h2>
            </div>
            <div class="features-grid">

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3>Clear calorie target</h3>
                    <p>
                        Instead of guessing or using a generic number, you get a calorie target based on your
                        actual body stats and activity level — so your deficit is right-sized for you, not for
                        an average person.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    </div>
                    <h3>Better pace control</h3>
                    <p>
                        Choose the pace that fits your life — slow for easier adherence, moderate for steady
                        progress, or aggressive when time is a factor. Each option shows you the real weekly
                        loss you can expect so there are no surprises.
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    </div>
                    <h3>More sustainable fat loss</h3>
                    <p>
                        Knowing your TDEE and maintenance calories helps you make informed food choices rather
                        than cutting blindly. Sustainable fat loss starts with understanding your numbers —
                        and this calculator gives you all of them in one place.
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
                    <summary class="faq-question">What is a calorie deficit?</summary>
                    <div class="faq-answer">
                        <p>A calorie deficit occurs when you eat fewer calories than your body burns in a day. Your body then uses stored body fat to make up the shortfall, which leads to fat loss over time. The larger the deficit, the faster the loss — but also the harder it is to sustain and the greater the risk of muscle breakdown.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">How big should my calorie deficit be?</summary>
                    <div class="faq-answer">
                        <p>A moderate deficit of 300–500 kcal/day is recommended for most people. This typically produces 0.25–0.5 kg of fat loss per week — a rate that is sustainable without significantly impacting energy, training performance, or muscle mass. Larger deficits can work short-term but are harder to maintain and carry greater risk of muscle loss.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Is a bigger calorie deficit better?</summary>
                    <div class="faq-answer">
                        <p>Not necessarily. While a larger deficit accelerates initial weight loss, it also increases the risk of losing muscle alongside fat, drops in training performance, micronutrient deficiencies, and eventually metabolic adaptation where your body reduces its energy output. Most research supports moderate deficits for better long-term body composition results.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Can I use kg or lbs in this calculator?</summary>
                    <div class="faq-answer">
                        <p>Yes — use whichever unit you prefer. The calculator auto-converts your weight to kg internally for the calculation and displays results in both units. Your height can also be entered in cm or as feet and inches. Simply toggle between units using the switcher next to each input field.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">Does activity level change my calorie deficit?</summary>
                    <div class="faq-answer">
                        <p>Your activity level affects your TDEE — the number of calories you burn each day. A more active person has a higher TDEE, which means their maintenance calories and their deficit-adjusted target are both higher. This is why two people with the same weight can have very different calorie targets depending on how active they are.</p>
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
                    <h2>Ready to Build a Full Fat Loss Plan?</h2>
                    <p>
                        Your daily calorie target is a powerful starting point. A complete plan
                        combines your calorie target with the right macro split, protein goal,
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
        var wKgBtn  = document.getElementById('wunit-kg');
        var wLbsBtn = document.getElementById('wunit-lbs');
        var wHidden = document.getElementById('weight-unit-hidden');
        var wInput  = document.getElementById('weight');

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
        var hCmBtn       = document.getElementById('hunit-cm');
        var hImperialBtn = document.getElementById('hunit-imperial');
        var hHidden      = document.getElementById('height-unit-hidden');
        var hCmWrap      = document.getElementById('height-cm-wrap');
        var hImpWrap     = document.getElementById('height-imperial-wrap');
        var hCmInput     = document.getElementById('height_cm');
        var hFtInput     = document.getElementById('height_ft');
        var hInInput     = document.getElementById('height_in');

        function setHeightUnit(unit) {
            if (unit === 'cm') {
                var ft   = parseFloat(hFtInput.value) || 0;
                var inch = parseFloat(hInInput.value) || 0;
                if (ft > 0 || inch > 0) {
                    hCmInput.value = Math.round(CoachProAI.feetInchesToCm(ft, inch));
                }
                hCmWrap.style.display = '';
                hImpWrap.style.display = 'none';
                hCmInput.required = true;
                hFtInput.required = false;
            } else {
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
