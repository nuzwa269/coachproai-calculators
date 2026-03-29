<?php
/**
 * CoachProAI – Shared Footer
 * File: includes/footer.php
 */
?>

    </main>

    <!-- FOOTER -->
    <footer class="site-footer" role="contentinfo">
        <div class="container">
            <div class="footer-inner">

                <span class="footer-brand">
                    CoachPro<span class="accent">AI</span>
                </span>

                <p class="footer-copy">
                    &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
                </p>

                <nav class="footer-links" aria-label="Footer navigation">
                    <a href="/">Home</a>
                    <a href="/#calculators">Calculators</a>
                    <a href="<?php echo PRIVACY_URL; ?>">Privacy Policy</a>
                    <a href="<?php echo CONTACT_URL; ?>">Contact</a>
                </nav>

            </div>
        </div>
    </footer>

    <!-- Mobile nav toggle script -->
    <script>
    (function () {
        var toggle = document.getElementById('nav-toggle');
        var nav    = document.getElementById('site-nav');
        if (!toggle || !nav) return;

        toggle.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }());
    </script>

</body>
</html>
