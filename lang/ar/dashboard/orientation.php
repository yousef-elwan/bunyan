<?php
return [
    //======================================
    // قسم 'list' لصفحة قائمة الاتجاهات
    //======================================
    'list' => [
        // Page & Buttons
        'orientations' => 'الاتجاهات',
        'add' => 'إضافة اتجاه جديد',
        'refresh_button' => 'تحديث',

        // Table Headers
        'name' => 'الاسم',
        'addon' => 'تاريخ الإضافة',
        'actions' => 'إجراءات',

        // Table Content
        'no_data_found' => 'لم يتم العثور على اتجاهات.',
        'error_loading' => 'خطأ في تحميل الاتجاهات.',

        // Tooltips
        'edit_tooltip' => 'تعديل',
        'delete_tooltip' => 'حذف',

        // SweetAlerts & Confirmations for Delete
        'confirm_delete_title' => 'هل أنت متأكد؟',
        'confirm_delete_text' => 'لن تتمكن من التراجع عن هذا الإجراء!',
        'confirm_delete_button' => 'نعم, احذفه!',
        'cancel_button' => 'إلغاء',

        'delete_success_title' => 'تم الحذف!',
        'delete_success_text' => 'تم حذف الاتجاه بنجاح.',
        'error_title' => 'خطأ!', // عنوان خطأ عام
        'delete_error_text' => 'فشل حذف الاتجاه: :error',
    ],
    //======================================
    // قسم 'create' لصفحة إضافة اتجاه جديد
    //======================================
    'create' => [
        // Page & Buttons
        'add' => 'إضافة اتجاه جديد',
        'primaryinfo' => 'معلومات أساسية',
        'entername' => 'ادخل الاسم',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'saving' => 'جارٍ الحفظ...',

        // SweetAlerts & JS Messages for Create
        'create_success_title' => 'تم بنجاح!',
        'create_success_text' => 'تم إنشاء الاتجاه بنجاح.',
        'error_title' => 'خطأ!',
        'form_error_text' => 'فشل في حفظ الاتجاه. الرجاء المحاولة مرة أخرى.',
    ],
    //======================================
    // قسم 'edit' لصفحة تعديل الاتجاه
    //======================================
    'edit' => [
        // Page & Buttons
        'edit' => 'تعديل الاتجاه',
        'primaryinfo' => 'معلومات أساسية',
        'entername' => 'ادخل الاسم',
        'save' => 'حفظ التغييرات',
        'cancel' => 'إلغاء',
        'saving' => 'جارٍ الحفظ...',

        // SweetAlerts & JS Messages for Update
        'update_success_title' => 'تم بنجاح!',
        'update_success_text' => 'تم تحديث الاتجاه بنجاح.',
        'error_title' => 'خطأ!',
        'form_error_text' => 'فشل في حفظ الاتجاه. الرجاء المحاولة مرة أخرى.',
    ],
];
