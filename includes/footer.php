</main>

<footer class="footer-store">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <span class="brand-icon"><i class="fas fa-graduation-cap"></i></span>
                    <?= e(APP_NAME) ?>
                </div>
                <p class="footer-tagline">
                    منصة تعليمية متخصصة لبيع الدورات التدريبية الرقمية بأمان وسهولة، مع دعم فني مباشر عبر واتساب.
                </p>
            </div>
            <div class="footer-col">
                <h6>روابط سريعة</h6>
                <ul class="footer-links">
                    <li><a href="<?= BASE_URL ?>/index.php"><i class="fas fa-chevron-left fa-xs"></i> الدورة التدريبية</a></li>
                    <li><a href="<?= BASE_URL ?>/cart.php"><i class="fas fa-chevron-left fa-xs"></i> سلة التسوق</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/login.php"><i class="fas fa-chevron-left fa-xs"></i> لوحة التحكم</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h6>الدعم</h6>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-chevron-left fa-xs"></i> الأسئلة الشائعة</a></li>
                    <li><a href="#"><i class="fas fa-chevron-left fa-xs"></i> سياسة الاسترداد</a></li>
                    <li><a href="#"><i class="fas fa-chevron-left fa-xs"></i> تواصل معنا</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span class="copy">&copy; <?= date('Y') ?> <?= e(APP_NAME) ?> — جميع الحقوق محفوظة</span>
            <span class="footer-trust">
                <i class="fas fa-lock"></i> دفع آمن عبر QR + إشعار واتساب فوري
            </span>
        </div>
    </div>
</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
<?php $pdo = null; ?>
