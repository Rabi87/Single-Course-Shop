    </div><!-- /.admin-content -->
</div><!-- /.admin-main -->

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
    // Sidebar toggle for mobile
    document.getElementById('sidebarToggle')?.addEventListener('click', function(){
        document.getElementById('adminSidebar').classList.toggle('open');
    });

    // Admin theme toggle (syncs with main theme system)
    (function() {
        const themeBtn = document.getElementById('adminThemeToggle');
        if (!themeBtn) return;

        function updateIcon() {
            const isDark = document.body.classList.contains('dark-mode');
            themeBtn.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        }

        themeBtn.addEventListener('click', function() {
            const mainToggle = document.getElementById('themeToggle');
            if (mainToggle) {
                mainToggle.click();
            } else {
                const isDark = document.body.classList.contains('dark-mode');
                if (isDark) {
                    document.body.classList.remove('dark-mode');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.body.classList.add('dark-mode');
                    localStorage.setItem('theme', 'dark');
                }
            }
            setTimeout(updateIcon, 50);
        });

        // Initial icon
        updateIcon();

        // Listen for changes from other tabs/toggles
        window.addEventListener('storage', function(e) {
            if (e.key === 'theme') updateIcon();
        });
    })();

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('adminSidebar');
        const toggle = document.getElementById('sidebarToggle');
        if (window.innerWidth <= 991 && sidebar?.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });
</script>
</body>
</html>
<?php $pdo = null; ?>
