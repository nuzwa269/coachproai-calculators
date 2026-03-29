<?php
/**
 * CoachProAI – Shared Helper Functions
 * File: includes/helpers.php
 */

require_once __DIR__ . '/config.php';

/* ------------------------------------------------------------------
   1. Output Sanitization
   ------------------------------------------------------------------ */

/**
 * Safely escape a string for HTML output.
 *
 * @param  string $value  Raw user input
 * @return string         HTML-safe string
 */
function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/* ------------------------------------------------------------------
   2. Unit Conversions
   ------------------------------------------------------------------ */

/**
 * Convert kilograms to pounds.
 *
 * @param  float $kg  Weight in kilograms
 * @return float      Weight in pounds
 */
function kg_to_lbs(float $kg): float
{
    return $kg * 2.20462;
}

/**
 * Convert pounds to kilograms.
 *
 * @param  float $lbs  Weight in pounds
 * @return float       Weight in kilograms
 */
function lbs_to_kg(float $lbs): float
{
    return $lbs / 2.20462;
}

/**
 * Convert centimetres to feet and inches.
 *
 * @param  float $cm  Height in centimetres
 * @return array      ['feet' => int, 'inches' => float]
 */
function cm_to_feet_inches(float $cm): array
{
    $totalInches = $cm / 2.54;
    $feet   = (int) floor($totalInches / 12);
    $inches = round($totalInches - ($feet * 12), 1);

    return ['feet' => $feet, 'inches' => $inches];
}

/**
 * Convert feet + inches to centimetres.
 *
 * @param  int   $feet    Feet component
 * @param  float $inches  Inches component
 * @return float          Height in centimetres
 */
function feet_inches_to_cm(int $feet, float $inches): float
{
    return (($feet * 12) + $inches) * 2.54;
}

/* ------------------------------------------------------------------
   3. Validation Helpers
   ------------------------------------------------------------------ */

/**
 * Validate a weight value (kg or lbs).
 *
 * @param  mixed $weight  The value to check
 * @param  float $min     Minimum acceptable weight (default 20)
 * @param  float $max     Maximum acceptable weight (default 500)
 * @return bool
 */
function validate_weight($weight, float $min = 20.0, float $max = 500.0): bool
{
    return is_numeric($weight) && $weight >= $min && $weight <= $max;
}

/**
 * Validate a height value in centimetres.
 *
 * @param  mixed $height  The value to check
 * @param  float $min     Minimum acceptable height in cm (default 100)
 * @param  float $max     Maximum acceptable height in cm (default 250)
 * @return bool
 */
function validate_height($height, float $min = 100.0, float $max = 250.0): bool
{
    return is_numeric($height) && $height >= $min && $height <= $max;
}

/**
 * Validate an age value.
 *
 * @param  mixed $age  The value to check
 * @param  int   $min  Minimum acceptable age (default 13)
 * @param  int   $max  Maximum acceptable age (default 120)
 * @return bool
 */
function validate_age($age, int $min = 13, int $max = 120): bool
{
    return is_numeric($age) && $age >= $min && $age <= $max;
}

/* ------------------------------------------------------------------
   4. Formatting Helpers
   ------------------------------------------------------------------ */

/**
 * Round a number and strip unnecessary trailing zeros.
 *
 * @param  float $value      The number to round
 * @param  int   $precision  Decimal places (default 1)
 * @return string
 */
function round_clean(float $value, int $precision = 1): string
{
    return rtrim(rtrim(number_format($value, $precision), '0'), '.');
}

/**
 * Format a number with a thousands separator.
 *
 * @param  float  $value        The number to format
 * @param  int    $decimals     Decimal places (default 0)
 * @param  string $decPoint     Decimal separator (default '.')
 * @param  string $thousandsSep Thousands separator (default ',')
 * @return string
 */
function format_number(float $value, int $decimals = 0, string $decPoint = '.', string $thousandsSep = ','): string
{
    return number_format($value, $decimals, $decPoint, $thousandsSep);
}

/* ------------------------------------------------------------------
   5. Activity Multiplier
   ------------------------------------------------------------------ */

/**
 * Return the TDEE activity multiplier for a given activity level.
 *
 * Levels: sedentary, light, moderate, active, very_active
 *
 * @param  string $level  Activity level key
 * @return float          Multiplier (defaults to 1.2 for unknown levels)
 */
function get_activity_multiplier(string $level): float
{
    $multipliers = [
        'sedentary'   => 1.2,
        'light'       => 1.375,
        'moderate'    => 1.55,
        'active'      => 1.725,
        'very_active' => 1.9,
    ];

    return $multipliers[strtolower(trim($level))] ?? 1.2;
}
