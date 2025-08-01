// passwordManager.js

/**
 * مدير حقول كلمة المرور - للتحكم في إظهار/إخفاء كلمة المرور في جميع أنحاء المشروع
 */
export class PasswordInputManager {
    constructor() {
        this.passwordInputs = new Map();
        this.setupEventListeners();
    }

    /**
     * إعداد المستمعين للأحداث
     */
    setupEventListeners() {
        // البحث عن جميع أزرار التبديل وإضافة المستمعين
        document.querySelectorAll('.password-toggle-btn').forEach(btn => {
            const inputId = btn.getAttribute('data-input-id');
            if (inputId) {
                this.registerInput(inputId, btn);
            }
        });

        // البحث عن جميع حقول الإدخال التي لم يتم تسجيلها بعد
        document.querySelectorAll('input[type="password"]').forEach(input => {
            if (!this.passwordInputs.has(input.id)) {
                const toggleBtn = document.querySelector(`.password-toggle-btn[data-input-id="${input.id}"]`);
                if (toggleBtn) {
                    this.registerInput(input.id, toggleBtn);
                }
            }
        });
    }

    /**
     * تسجيل حقل إدخال جديد في المدير
     * @param {string} inputId - معرّف حقل الإدخال
     * @param {HTMLElement} toggleBtn - زر التبديل المرتبط
     */
    registerInput(inputId, toggleBtn) {
        const input = document.getElementById(inputId);
        if (!input) return;

        // تجنب التسجيل المزدوج
        if (this.passwordInputs.has(inputId)) return;

        // الحصول على أيقونة التبديل
        let icon = toggleBtn.querySelector('i');
        if (!icon) {
            icon = document.createElement('i');
            icon.className = 'fas fa-eye';
            toggleBtn.appendChild(icon);
        }

        // حفظ حالة الحقل
        this.passwordInputs.set(inputId, {
            input,
            toggleBtn,
            icon,
            visible: false
        });

        // إضافة مستمع الأحداث
        toggleBtn.addEventListener('click', () => this.togglePasswordVisibility(inputId));

        // التهيئة الأولية
        this.updateIcon(inputId);
    }

    /**
     * تبديل حالة إظهار/إخفاء كلمة المرور
     * @param {string} inputId - معرّف حقل الإدخال
     */
    togglePasswordVisibility(inputId) {
        const data = this.passwordInputs.get(inputId);
        if (!data) return;

        data.visible = !data.visible;
        data.input.type = data.visible ? 'text' : 'password';
        this.updateIcon(inputId);
    }

    /**
     * تحديث الأيقونة بناءً على حالة الحقل
     * @param {string} inputId - معرّف حقل الإدخال
     */
    updateIcon(inputId) {
        const data = this.passwordInputs.get(inputId);
        if (!data) return;

        if (data.visible) {
            data.icon.classList.replace('fa-eye', 'fa-eye-slash');
            data.icon.classList.replace('fa-eye-slash', 'fa-eye-slash');
            data.toggleBtn.setAttribute('aria-label', 'Hide password');
        } else {
            data.icon.classList.replace('fa-eye-slash', 'fa-eye');
            data.icon.classList.replace('fa-eye', 'fa-eye');
            data.toggleBtn.setAttribute('aria-label', 'Show password');
        }
    }

    /**
     * تهيئة المدير عند تحميل الصفحة
     */
    static init() {
        if (!window.passwordManager) {
            window.passwordManager = new PasswordInputManager();
        }
        return window.passwordManager;
    }
}

// تهيئة المدير عند تحميل الصفحة
// if (document.readyState === 'loading') {
//     document.addEventListener('DOMContentLoaded', PasswordInputManager.init);
// } else {
//     PasswordInputManager.init();
// }

/*
<div class="input-group">
    <i class="fas fa-lock icon"></i>
    <input type="password" name="password" class="password-input" 
            id="loginPasswordInput" placeholder="Password" required>
    <button type="button" class="password-toggle-btn" 
            data-input-id="loginPasswordInput" aria-label="Show password">
        <i class="fas fa-eye"></i>
    </button>
</div>
 */