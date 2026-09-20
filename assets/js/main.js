/* ============================================================
   AURA — Premium Course Landing JavaScript
   Compatible with custom.css + themes.css
   ============================================================ */
(function () {
  "use strict";

  const BASE_URL = document.querySelector('meta[name="base-url"]')?.content
    || (window.location.origin + window.location.pathname.replace(/\/[^/]*$/, ''));

  /* ----------------------------------------------------------
     Theme Toggle
     ---------------------------------------------------------- */
  const themeToggle = document.getElementById("themeToggle");
  const themeIcon = document.getElementById("themeIcon");
  const themeLabel = document.getElementById("themeLabel");

  function setTheme(mode) {
    if (mode === "dark") {
      document.body.classList.add("dark-mode");
      if (themeIcon) themeIcon.className = "fas fa-sun";
      if (themeLabel) themeLabel.textContent = "فاتح";
      localStorage.setItem("theme", "dark");
    } else {
      document.body.classList.remove("dark-mode");
      if (themeIcon) themeIcon.className = "fas fa-moon";
      if (themeLabel) themeLabel.textContent = "داكن";
      localStorage.setItem("theme", "light");
    }
  }

  const savedTheme = localStorage.getItem("theme") || "light";
  setTheme(savedTheme);

  if (themeToggle) {
    themeToggle.addEventListener("click", function () {
      const isDark = document.body.classList.contains("dark-mode");
      setTheme(isDark ? "light" : "dark");
    });
  }

  /* ----------------------------------------------------------
     Navbar scroll effect
     ---------------------------------------------------------- */
  const navbar = document.getElementById("mainNavbar");
  if (navbar) {
    window.addEventListener("scroll", function () {
      navbar.classList.toggle("scrolled", window.scrollY > 20);
    }, { passive: true });
  }

  /* ----------------------------------------------------------
     Smooth scroll for anchor links
     ---------------------------------------------------------- */
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function(e) {
      const href = this.getAttribute("href");
      if (href === "#") return;

      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({
          behavior: "smooth",
          block: "start"
        });
      }
    });
  });

  /* ----------------------------------------------------------
     Mobile menu toggle
     ---------------------------------------------------------- */
  const navbarToggler = document.querySelector('.navbar-toggler');
  const headerNav = document.querySelector('.header-nav');

  if (navbarToggler && headerNav) {
    navbarToggler.addEventListener('click', function() {
      headerNav.classList.toggle('show');
      const isOpen = headerNav.classList.contains('show');
      this.setAttribute('aria-expanded', isOpen);
    });
  }

  /* ----------------------------------------------------------
     Reveal on scroll (Intersection Observer)
     ---------------------------------------------------------- */
  const revealElements = document.querySelectorAll('.reveal');
  if (revealElements.length > 0 && 'IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -40px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));
  } else {
    // Fallback for older browsers
    revealElements.forEach(el => el.classList.add('visible'));
  }

  /* ----------------------------------------------------------
     CSRF helper
     ---------------------------------------------------------- */
  function getCsrf() {
    const el = document.querySelector('input[name="csrf_token"]');
    return el ? el.value : "";
  }

  /* ----------------------------------------------------------
     Remove from cart
     ---------------------------------------------------------- */
  document.querySelectorAll('.btn-remove').forEach(btn => {
    btn.addEventListener('click', function() {
      const courseId = this.dataset.courseId;
      this.disabled = true;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

      const formData = new FormData();
      formData.append('action', 'remove');
      formData.append('course_id', courseId);
      formData.append('csrf_token', getCsrf());

      fetch(BASE_URL + "/cart-ajax.php", {
        method: "POST",
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          location.reload();
        } else {
          this.disabled = false;
          this.innerHTML = '<i class="fas fa-trash-alt"></i>';
          alert(res.message || "حدث خطأ");
        }
      })
      .catch(() => {
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-trash-alt"></i>';
        alert("حدث خطأ في الشبكة");
      });
    });
  });

  /* ----------------------------------------------------------
     Checkout form handling
     ---------------------------------------------------------- */
  const checkoutForm = document.getElementById("checkoutForm");
  if (checkoutForm) {
    const paymentModalEl = document.getElementById("paymentModal");
    let paymentModal = null;
    if (paymentModalEl && typeof bootstrap !== "undefined" && bootstrap.Modal) {
      paymentModal = new bootstrap.Modal(paymentModalEl);
    }

    checkoutForm.addEventListener("submit", function (e) {
      e.preventDefault();

      if (!checkoutForm.checkValidity()) {
        checkoutForm.classList.add("was-validated");
        return;
      }

      if (paymentModal) {
        paymentModal.show();
      } else {
        alert("عذراً، حدث خطأ في تحميل نظام الدفع. يرجى تحديث الصفحة.");
      }
    });

    const confirmBtn = document.getElementById("confirmPaymentBtn");
    if (confirmBtn) {
      confirmBtn.addEventListener("click", function () {
        const txnId = document.getElementById("paymentTxnId");
        const msgBox = document.getElementById("paymentMsg");
        if (msgBox) msgBox.textContent = "";

        if (!txnId.value.trim()) {
          txnId.classList.add("is-invalid");
          return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جارٍ المعالجة...';

        const formData = new FormData(checkoutForm);
        formData.append("payment_txn_id", txnId.value.trim());
        formData.append("csrf_token", getCsrf());

        fetch(BASE_URL + "/process-payment.php", { method: "POST", body: formData })
          .then((r) => r.json())
          .then((res) => {
            if (res.success) {
              window.open(res.whatsapp_admin_link, "_blank");
              window.location.href = res.redirect;
            } else {
              btn.disabled = false;
              btn.innerHTML = '<i class="fas fa-check-circle"></i> تأكيد الدفع';
              if (msgBox) msgBox.textContent = res.message || "حدث خطأ";
            }
          })
          .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> تأكيد الدفع';
            if (msgBox) msgBox.textContent = "حدث خطأ في الشبكة";
          });
      });
    }

    const txnInput = document.getElementById("paymentTxnId");
    if (txnInput) {
      txnInput.addEventListener("input", function () {
        this.classList.remove("is-invalid");
      });
    }
  }

  /* ----------------------------------------------------------
     Add to cart
     ---------------------------------------------------------- */
  const addForm = document.getElementById("addToCartForm");
  if (addForm) {
    addForm.addEventListener("submit", function(e) {
      e.preventDefault();
      const btn = this.querySelector('button[type="submit"]');
      const originalText = btn.innerHTML;
      const msgBox = document.getElementById("addToCartMsg");

      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جارٍ الإضافة...';
      btn.disabled = true;
      if (msgBox) msgBox.innerHTML = "";

      const formData = new FormData(this);
      fetch(BASE_URL + "/cart-ajax.php", {
        method: "POST",
        body: formData
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          if (msgBox) {
            msgBox.innerHTML = '<div class="alert alert-success py-2"><i class="fas fa-check-circle"></i> تمت الإضافة للسلة!</div>';
          }
          setTimeout(() => {
            window.location.href = BASE_URL + "/cart.php";
          }, 800);
        } else {
          btn.innerHTML = originalText;
          btn.disabled = false;
          if (msgBox) {
            msgBox.innerHTML = '<div class="alert alert-danger py-2">' + (res.message || "حدث خطأ") + '</div>';
          }
        }
      })
      .catch(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        if (msgBox) {
          msgBox.innerHTML = '<div class="alert alert-danger py-2">حدث خطأ في الشبكة. يرجى المحاولة مرة أخرى.</div>';
        }
      });
    });
  }

  /* ----------------------------------------------------------
     Parallax effect for hero shapes (subtle)
     ---------------------------------------------------------- */
  const heroShapes = document.querySelector('.hero-shapes');
  if (heroShapes && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    let ticking = false;
    window.addEventListener('scroll', () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          const scrollY = window.scrollY;
          const shapes = heroShapes.querySelectorAll('.shape');
          shapes.forEach((shape, i) => {
            const speed = 0.05 + (i * 0.02);
            shape.style.transform = `translateY(${scrollY * speed}px)`;
          });
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }

})();
