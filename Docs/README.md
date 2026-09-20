# 🎓 متجر دورة تدريبية واحدة (Single Course Store)

مشروع PHP + MySQL + Bootstrap لبيع دورة تدريبية واحدة، مع سلة شراء،
وإتمام دفع عبر رمز QR مع إشعار واتساب تلقائي، ولوحة تحكم للأدمن.

## المتطلبات
- PHP 8+
- MySQL 8
- Bootstrap 5 (CDN)
- JavaScript (Vanilla + AJAX)

## الإعداد (Setup)

### 1) استيراد قاعدة البيانات
يتم إنشاء قاعدة بيانات `course_store_db` عبر `sql/schema.sql`.

خيار A (داخل Docker CLI):
```bash
docker exec -i php-mysql-docker-mysql-1 mysql -u root -proot_secure_password_123 < sql/schema.sql
```

خيار B (عبر phpMyAdmin):
افتح `http://localhost:8081` → import → اختر `sql/schema.sql`.

### 2) إنشاء حساب الأدمن
افتح في المتصفح:
```
http://localhost:8080/courses/setup.php
```
أنشئ حساب الأدمن (أو حدّث كلمة المرور). ثم **احذف ملف `setup.php`**.

بيانات الدخول الافتراضية الناتجة: `admin` / (كلمة المرور التي أدخلتها).

### 3) الإعدادات العامة
عدّل ملف `includes/config.php`:
- `ADMIN_WHATSAPP_NUMBER` : رقم واتساب المدير (بصيغة دولية بدون +).
- `QR_IMAGE_PATH` : مسار صورة رمز QR الخاص بالدفع.
- `CURRENCY` : رمز العملة.
- `BASE_URL` : رابط الموقع.

### 4) الصور
- صورة الدورة: `assets/images/course.svg`
- رمز QR للدفع: ضع صورتك في `assets/qr/qr-code.svg`
  (أو يتم توليد رمز افتراضي تلقائياً إن لم يوجد الملف).

## 🖥️ الصفحات (Storefront)
| الصفحة | الوصف |
|--------|-------|
| `index.php` | عرض الدورة + إضافة للسلة |
| `cart.php` | مراجعة السلة وتعديل الكميات |
| `checkout.php` | نموذج بيانات العميل |
| `order-success.php` | رسالة نجاح الطلب |

## 🔐 لوحة التحكم (`admin/`)
| الصفحة | الوصف |
|--------|-------|
| `login.php` | تسجيل دخول آمن (bcrypt + CSRF) |
| `index.php` | لوحة القيادة (إحصائيات) |
| `edit-course.php` | تعديل الدورة ورفع صورة |
| `orders.php` | إدارة الطلبات والحالات |

## 🛡️ الأمان المطبق
- PDO + Prepared Statements (حماية SQL Injection)
- `htmlspecialchars` عند كل مخرجات (حماية XSS)
- CSRF token في جميع النماذج
- `password_hash()` bcrypt (cost=12)
- جلسات آمنة (HttpOnly, SameSite=Strict, regenerate بعد الدخول)
- رفع صور آمن (أنواع مسموحة، حد 2MB، إعادة تسمية عشوائية)

## 🔄 مسار الدفع
سلة → بيانات العميل → مودال QR → إدخال رقم العملية → إشعار واتساب للمدير → صفحة النجاح.

