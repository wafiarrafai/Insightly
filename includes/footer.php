<?php
// Mencegah error jika basePath belum didefinisikan
$basePath = $basePath ?? '';
?>
<footer class="footer">
    <div class="container footer-wrapper">
        <div>
            <a href="<?= $basePath ?>index.php" class="footer-logo">
                <img
                    src="<?= $basePath ?>assets/images/insightly-logo.png"
                    alt="Insightly Logo"
                    class="footer-logo-image">
                <span>insightly</span>
            </a>
            <p>
                Sistem manajemen feedback pelanggan.
            </p>
        </div>
        <span>
            &copy; 2026 Insightly. All rights reserved.
        </span>
    </div>
</footer>
</body>
</html>