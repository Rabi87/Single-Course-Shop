<?php
$pageTitle = 'الدورة التدريبية';
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC LIMIT 1");
$course = $stmt->fetch();

if (!$course) {
    echo '<div class="alert alert-danger">لا توجد دورة متاحة حالياً.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-star"></i>
                دورة تدريبية مميزة
            </div>
            <h1 class="hero-title"><?= e($course['name']) ?></h1>
            <p class="hero-description"><?= nl2br(e($course['description'])) ?></p>
            <div class="hero-meta">
                <div class="meta-item">
                    <i class="fas fa-users"></i>
                    <span>+120 طالب</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-star"></i>
                    <span>4.9 تقييم</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span>+20 ساعة</span>
                </div>
            </div>
            <div class="hero-actions">
                <a href="#pricing" class="btn btn-primary">
                    <span>سجل الآن</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
                <a href="#curriculum" class="btn btn-outline">
                    <span>استعرض المنهج</span>
                </a>
            </div>
        </div>
    </div>
    <div class="hero-scroll" onclick="document.getElementById('curriculum').scrollIntoView({behavior:'smooth'})">
        <span>اكتشف المزيد</span>
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

<!-- Course Info Section -->
<section class="course-info" id="curriculum">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-tag">ماذا ستتعلم</span>
            <h2 class="section-title">أهداف الدورة</h2>
        </div>
        <div class="objectives-grid">
            <div class="objective-card reveal reveal-delay-1">
                <div class="objective-icon">
                    <i class="fas fa-code"></i>
                </div>
                <h3>تطوير تطبيقات ويب كاملة</h3>
                <p>تعلم بناء تطبيقات ويب احترافية من الصفر حتى الإطلاق</p>
            </div>
            <div class="objective-card reveal reveal-delay-2">
                <div class="objective-icon">
                    <i class="fas fa-database"></i>
                </div>
                <h3>إدارة قواعد البيانات</h3>
                <p>إتقان SQL و MySQL لإنشاء وإدارة قواعد البيانات</p>
            </div>
            <div class="objective-card reveal reveal-delay-3">
                <div class="objective-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>أمان وحماية</h3>
                <p>تطبيق أفضل practices للأمان في تطبيقات الويب</p>
            </div>
            <!--   <div class="objective-card reveal reveal-delay-4">
                <div class="objective-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3>نشر واستضافة</h3>
                <p>تعلم كيفية نشر تطبيقاتك على الخوادم</p>
            </div>
        </div>
    </div> -->
</section>

<!-- Features Section -->
            <section class="features-section">
                <div class="container">
                    <div class="section-header reveal">
                        <span class="section-tag">لماذا هذه الدورة</span>
                        <h2 class="section-title">مميزات تجعلنا الخيار الأفضل</h2>
                    </div>
                    <div class="features-grid">
                        <div class="feature-card reveal reveal-delay-1">
                            <div class="feature-icon">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <h3>محتوى فيديو عالي الجودة</h3>
                            <p>فيديوهات عالية الدقة مع شرح مفصل</p>
                        </div>
                        <div class="feature-card reveal reveal-delay-2">
                            <div class="feature-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h3>شهادة إتمام معتمدة</h3>
                            <p>شهادة يمكنك إضافتها لسيرتك الذاتية</p>
                        </div>
                        <div class="feature-card reveal reveal-delay-3">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h3>دعم فني مباشر</h3>
                            <p>فريق دعم متاح على مدار الساعة</p>
                        </div>
                        <div class="feature-card reveal reveal-delay-1">
                            <div class="feature-icon">
                                <i class="fas fa-infinity"></i>
                            </div>
                            <h3>وصول مدى الحياة</h3>
                            <p>وصول كامل للمحتوى مع التحديثات</p>
                        </div>
                        <div class="feature-card reveal reveal-delay-2">
                            <div class="feature-icon">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <h3>مشاريع عملية</h3>
                            <p>مشاريع حقيقية تضيفها لمعرض أعمالك</p>
                        </div>
                        <div class="feature-card reveal reveal-delay-3">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>مجتمع تفاعلي</h3>
                            <p>انضم لمجتمع من المطورين</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing Section -->
            <section class="pricing-section" id="pricing">
                <div class="container">
                    <div class="section-header reveal">
                        <span class="section-tag">الاستثمار في مستقبلك</span>
                        <h2 class="section-title">احصل على الدورة الآن</h2>
                    </div>
                    <div class="pricing-card reveal reveal-delay-1">
                        <div class="pricing-header">
                            <span class="pricing-badge">عرض محدود</span>
                            <h2 class="pricing-title">استثمر في مستقبلك</h2>
                            <p class="pricing-subtitle">احصل على الدورة الآن بسعر خاص</p>
                        </div>
                        <div class="pricing-body">
                            <div class="price-display">
                                <span class="price-amount"><?= money($course['price']) ?></span>
                                <span class="price-period">دفعة واحدة — وصول مدى الحياة</span>
                            </div>
                            <ul class="pricing-features">
                                <li><i class="fas fa-check"></i> وصول كامل للمحتوى مدى الحياة</li>
                                <li><i class="fas fa-check"></i> شهادة إتمام معتمدة</li>
                                <li><i class="fas fa-check"></i> دعم فني مباشر</li>
                                <li><i class="fas fa-check"></i> تحديثات مجانية</li>
                                <li><i class="fas fa-check"></i> مشاريع عملية قابلة للتنزيل</li>
                            </ul>
                            <form id="addToCartForm" method="post">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="course_id" id="courseId" value="<?= (int)$course['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-add-cart">
                                    <i class="fas fa-cart-plus"></i>
                                    <span>أضف إلى السلة</span>
                                </button>
                            </form>
                            <div id="addToCartMsg" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </section>

            <?php require_once __DIR__ . '/includes/footer.php'; ?>