/*
 * ===============================================
 *   مكون مؤشر قوة كلمة المرور (Password Strength Checker)
 *   - مصمم ليكون قابلاً لإعادة الاستخدام في أي مكان بالصفحة.
 *   - يعتمد على كلاسات CSS وهيكل HTML محدد.
 * ===============================================
 */

/**
 * Class لإدارة مؤشر قوة كلمة المرور لمكون واحد.
 * يبحث عن العناصر المطلوبة داخل عنصر أب واحد (wrapperElement).
 */
class PasswordStrengthChecker {
    /**
     * @param {HTMLElement} wrapperElement - العنصر الأب الذي يحيط بحقل الإدخال والمؤشر.
     */
    constructor(wrapperElement) {
        this.wrapper = wrapperElement;
        this.input = this.wrapper.querySelector('.password-strength-input');
        this.meter = this.wrapper.querySelector('.password-strength-meter');

        // إذا كانت العناصر الأساسية غير موجودة، أوقف التنفيذ لهذا المكون
        if (!this.input || !this.meter) {
            console.warn('Password strength component is missing required elements (e.g., .password-strength-input or .password-strength-meter).', this.wrapper);
            return;
        }

        this.bar = this.meter.querySelector('.strength-bar');
        this.text = this.meter.querySelector('.strength-text');

        // يمكنك تعديل هذه النصوص لتتوافق مع نظام الترجمة لديك إذا أردت
        this.strengthLabels = {
            weak: 'ضعيفة',
            medium: 'متوسطة',
            strong: 'قوية',
        };

        this.initEventListeners();
    }

    /**
     * يقوم بتهيئة المستمع لحدث الإدخال في حقل كلمة المرور.
     */
    initEventListeners() {
        this.input.addEventListener('input', () => {
            const password = this.input.value;

            if (password) {
                // إظهار المؤشر بمجرد بدء الكتابة
                this.meter.style.display = 'block';
                const score = this.#checkStrength(password);
                this.#updateUI(score);
            } else {
                // إخفاء المؤشر إذا كان الحقل فارغًا
                this.meter.style.display = 'none';
            }
        });

        // إخفاء المؤشر عند تحميل الصفحة مبدئيًا
        this.meter.style.display = 'none';
    }

    /**
     * يقوم بتقييم قوة كلمة المرور بناءً على مجموعة من القواعد.
     * @param {string} password - كلمة المرور المطلوب تقييمها.
     * @returns {number} - درجة القوة (من 0 إلى 5).
     * @private
     */
    #checkStrength(password) {
        let score = 0;
        // القاعدة 1: الطول (8 أحرف على الأقل)
        if (password.length >= 8) score++;
        // القاعدة 2: وجود حرف كبير (Uppercase)
        if (/[A-Z]/.test(password)) score++;
        // القاعدة 3: وجود حرف صغير (Lowercase)
        if (/[a-z]/.test(password)) score++;
        // القاعدة 4: وجود رقم
        if (/[0-9]/.test(password)) score++;
        // القاعدة 5: وجود رمز (تأكد من مطابقة هذه الرموز لقواعد الـ backend)
        if (/[@$!%*?&]/.test(password)) score++;

        return score;
    }

    /**
     * يقوم بتحديث واجهة المستخدم (الشريط والنص) لتعكس قوة كلمة المرور.
     * @param {number} score - درجة قوة كلمة المرور.
     * @private
     */
    #updateUI(score) {
        // إعادة تعيين التنسيقات قبل تطبيق الجديدة
        this.bar.className = 'strength-bar';
        this.text.textContent = '';

        let barClass = '';
        let textLabel = '';
        let textColor = '#6c757d'; // اللون الافتراضي للنص

        // تحديد الفئة واللون بناءً على النتيجة
        if (score > 0 && score <= 2) {
            barClass = 'weak';
            textLabel = this.strengthLabels.weak;
            textColor = '#dc3545';
        } else if (score >= 3 && score <= 4) {
            barClass = 'medium';
            textLabel = this.strengthLabels.medium;
            textColor = '#ffc107';
        } else if (score >= 5) {
            barClass = 'strong';
            textLabel = this.strengthLabels.strong;
            textColor = '#28a745';
        }

        if (barClass) {
            this.bar.classList.add(barClass);
        }

        this.text.textContent = textLabel;
        this.text.style.color = textColor;
    }
}

// --- مشغل الكود ---
// انتظر حتى يتم تحميل محتوى الصفحة بالكامل
document.addEventListener('DOMContentLoaded', () => {
    // 1. ابحث عن كل حاويات كلمة المرور في الصفحة التي تستخدم الكلاس المحدد
    const passwordWrappers = document.querySelectorAll('.password-input-wrapper');

    // 2. قم بإنشاء نسخة جديدة من الكلاس لكل حاوية تجدها
    passwordWrappers.forEach(wrapper => {
        new PasswordStrengthChecker(wrapper);
    });

    // يمكنك وضع بقية أكواد التهيئة الخاصة بك هنا
    // مثلاً: initAuthPages();
});
