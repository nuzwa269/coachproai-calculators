/**
 * CoachProAI – Shared Calculator Utilities
 * File: public/assets/js/calculators.js
 */

var CoachProAI = (function () {
    'use strict';

    /* ------------------------------------------------------------------
       Unit Conversions
       ------------------------------------------------------------------ */

    function kgToLbs(kg) {
        return kg * 2.20462;
    }

    function lbsToKg(lbs) {
        return lbs / 2.20462;
    }

    function cmToFeetInches(cm) {
        var totalInches = cm / 2.54;
        var feet   = Math.floor(totalInches / 12);
        var inches = Math.round((totalInches - feet * 12) * 10) / 10;
        return { feet: feet, inches: inches };
    }

    function feetInchesToCm(feet, inches) {
        return (feet * 12 + inches) * 2.54;
    }

    /* ------------------------------------------------------------------
       Formatting
       ------------------------------------------------------------------ */

    function roundClean(value, precision) {
        precision = precision !== undefined ? precision : 1;
        var factor = Math.pow(10, precision);
        var rounded = Math.round(value * factor) / factor;
        return parseFloat(rounded.toFixed(precision)).toString();
    }

    function formatNumber(value, decimals) {
        decimals = decimals !== undefined ? decimals : 0;
        return Number(value).toLocaleString(undefined, {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    /* ------------------------------------------------------------------
       Validation
       ------------------------------------------------------------------ */

    function validateWeight(weight, min, max) {
        min = min !== undefined ? min : 20;
        max = max !== undefined ? max : 500;
        var n = parseFloat(weight);
        return !isNaN(n) && n >= min && n <= max;
    }

    function validateHeight(height, min, max) {
        min = min !== undefined ? min : 100;
        max = max !== undefined ? max : 250;
        var n = parseFloat(height);
        return !isNaN(n) && n >= min && n <= max;
    }

    function validateAge(age, min, max) {
        min = min !== undefined ? min : 13;
        max = max !== undefined ? max : 120;
        var n = parseInt(age, 10);
        return !isNaN(n) && n >= min && n <= max;
    }

    /* ------------------------------------------------------------------
       Activity Multiplier
       ------------------------------------------------------------------ */

    var activityMultipliers = {
        sedentary:   1.2,
        light:       1.375,
        moderate:    1.55,
        active:      1.725,
        very_active: 1.9
    };

    function getActivityMultiplier(level) {
        return activityMultipliers[level] || 1.2;
    }

    /* ------------------------------------------------------------------
       Unit Toggle Helper
       ------------------------------------------------------------------ */

    function initUnitToggle(toggleSelector, metricSelector, imperialSelector) {
        var toggles = document.querySelectorAll(toggleSelector);
        if (!toggles.length) return;

        toggles.forEach(function (toggle) {
            toggle.addEventListener('change', function () {
                var isMetric = this.value === 'metric';
                var metricFields   = document.querySelectorAll(metricSelector);
                var imperialFields = document.querySelectorAll(imperialSelector);

                metricFields.forEach(function (el) {
                    el.style.display = isMetric ? '' : 'none';
                });
                imperialFields.forEach(function (el) {
                    el.style.display = isMetric ? 'none' : '';
                });
            });
        });
    }

    /* ------------------------------------------------------------------
       Public API
       ------------------------------------------------------------------ */

    return {
        kgToLbs:               kgToLbs,
        lbsToKg:               lbsToKg,
        cmToFeetInches:        cmToFeetInches,
        feetInchesToCm:        feetInchesToCm,
        roundClean:            roundClean,
        formatNumber:          formatNumber,
        validateWeight:        validateWeight,
        validateHeight:        validateHeight,
        validateAge:           validateAge,
        getActivityMultiplier: getActivityMultiplier,
        initUnitToggle:        initUnitToggle
    };

}());
