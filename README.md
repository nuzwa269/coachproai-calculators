# CoachProAI – Fitness Calculators

Free, evidence-based fitness calculators powered by real coaching logic.

## Project Structure

```
coachproai-calculators/
├── public/                  ← Web root (serve from here)
│   ├── index.php            ← Hub page (all calculators)
│   ├── protein-calculator-for-fat-loss.php
│   ├── calorie-deficit-calculator-to-lose-weight.php
│   ├── assets/
│   │   ├── css/
│   │   │   └── brand.css    ← Centralized brand stylesheet
│   │   ├── js/
│   │   │   └── calculators.js ← Shared calculator utilities
│   │   └── images/
│   └── .htaccess
├── includes/
│   ├── config.php           ← Site-wide configuration
│   ├── helpers.php          ← Shared PHP helper functions
│   ├── header.php           ← Shared HTML header / navbar
│   └── footer.php           ← Shared HTML footer
├── README.md
└── .gitignore
```

## Tech Stack

- **PHP** – server-side logic and templating
- **HTML5** – semantic markup
- **CSS3** – custom properties, responsive design (no frameworks)
- **Vanilla JavaScript** – client-side calculator utilities (no frameworks)

## Getting Started

1. Point your web server's document root to the `public/` directory.
2. Ensure PHP 7.4+ is available.
3. Access the site at your configured domain.

## Adding a New Calculator

1. Create a new `.php` file in `public/`.
2. Set `$pageTitle`, `$pageDescription`, and `$pageCanonical`.
3. Include the shared header and footer:
   ```php
   require_once __DIR__ . '/../includes/header.php';
   // ... your calculator HTML ...
   require_once __DIR__ . '/../includes/footer.php';
   ```
4. Add a card for it on the hub page (`public/index.php`).

## License

© CoachProAI. All rights reserved.
