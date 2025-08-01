/** @type {import('tailwindcss').Config} */
import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,vue,jsx,ts,tsx}',
    ],

    theme: {
        extend: {
            // الألوان مطابقة للصورة الداكنة
            colors: {

                'card-bg': '#ffffff',

                // Sidebar Colors
                'sidebar-bg': '#364150',
                'sidebar-hover': '#2f3b48',
                'sidebar-active': '#2c3542',
                'sidebar-submenu': 'rgba(0,0,0,0.2)',
                'header-border': '#eef1f5',
                'sidebar-active-text': '#60a5fa',

                // Page Colors from dashboard-final-ux.css
                'page-bg': '#f8fafc',          // var(--page-bg)
                'border-color': '#eef2f7',     // var(--border-color)
                'text-primary': '#1e293b',     // var(--text-primary)
                'text-secondary': '#64748b',   // var(--text-secondary)
                'accent-primary': '#4f46e5',   // var(--accent-primary)
                'accent-primary-light': '#eef2ff', // var(--accent-primary-light)
                'success': '#16a34a',          // var(--success)
                'success-light': '#f0fdf4',    // var(--success-light)
                'danger': '#dc2626',           // var(--danger)
                'danger-light': '#fef2f2',     // var(--danger-light)
                'warning': '#f59e0b',          // var(--warning)
                'warning-light': '#fffbeb',    // var(--warning-light)
                'purple': '#7c3aed',           // var(--purple)
                'purple-light': '#f5f3ff',     // var(--purple-light)
                'pagination-hover-bg': '#e2e8f0', // لون الخلفية عند التحويم
                'pagination-active-bg': '#3498db', // لون الخلفية للزر النشط
                'pagination-active-border': '#3498db', // لون الإطار للزر النشط

                'chat-primary': '#3a82f6',
                'chat-sent-bubble': '#dcf8c6',
                'chat-received-bubble': '#ffffff',
                'chat-bg': '#eae6df',
                'chat-header-bg': '#f0f2f5',
            },
            fontFamily: {
                // تأكد من استيراد خط Cairo في layout الرئيسي
                sans: ['Cairo', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                // -- أضفنا نصف القطر من ملف CSS --
                'xl': '12px', // var(--radius-md)
            },
            boxShadow: {
                // -- أضفنا الظل من ملف CSS --
                'custom': '0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05)',
                'custom-hover': '0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.07)',
            },
            // ... باقي الإعدادات القديمة مثل spacing و width
            spacing: {
                'sidebar-open': '280px',
                'sidebar-closed': '60px',
                'header-height': '50px',
                'sticky-top': 'calc(50px + 1.5rem)'
            },
            width: {
                'sidebar-open': '280px',
                'sidebar-closed': '60px',
            },
            height: {
                'header': '50px',
            },
            transitionProperty: {
                'width': 'width',
                'margin': 'margin',
                'all': 'all',
            },
            maxHeight: {
                '500': '500px',
            },
            borderWidth: {
                '1': '1px',
            },
            zIndex: {
                '1040': '1040',
                '1050': '1050',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'), // مفيد للـ Modals
    ],
};