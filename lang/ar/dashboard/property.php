<?php

return [
    'list' => [
        // Page & Buttons
        'page_title' => 'عقاراتي',
        'page_title_admin' => 'كل العقارات',

        'add_new_button' => 'إضافة عقار جديد',
        'search_button' => 'بحث',
        'refresh_button' => 'تحديث',
        'advanced_search_button' => 'بحث متقدم',
        'advanced_search_title' => 'بحث متقدم',
        'reset_button' => 'إعادة تعيين',
        'apply_button' => 'تطبيق',

        // Filters
        'filter_status' => 'الحالة',
        'select_status' => 'كل الحالات',

        'filter_owner' => 'المالك',
        'select_owner' => 'كل المالكين',

        'filter_city' => 'المدينة',
        'select_city' => 'كل المدن',

        'filter_category' => 'الفئة',
        'select_category' => 'كل الفئات',

        'filter_type' => 'النوع',
        'select_type' => 'كل الأنواع',

        'filter_floor' => 'الطابق',
        'select_floor' => 'كل الطوابق',

        'filter_orientation' => 'الإتجاه',
        'select_orientation' => 'كل الإتجاهات',

        'filter_contract_type' => 'العقد',
        'select_contract_type' => 'كل أنواع العقود',

        'price_range' => 'نطاق السعر',
        'min_price' => 'أدنى سعر',
        'max_price' => 'أعلى سعر',

        'min_price_label' => 'أقل سعر ($)',
        'max_price_label' => 'أعلى سعر ($)',
        'filter_amenities' => 'المميزات',

        // Table Headers
        'th_image' => 'الصور',
        'th_title' => 'العنوان',
        'th_city' => 'المدينة',
        'th_type' => 'النوع',
        'th_category' => 'الفئة',
        'th_status' => 'الحالة الحالية',
        'th_views' => 'عدد المشاهدات',
        'th_price' => 'السعر',
        'th_date' => 'تاريخ الإضافة',
        'th_actions' => 'إجراءات',

        // Table Content
        'no_properties_found' => 'لم يتم العثور على عقارات تطابق معايير البحث.',
        'error_loading_properties' => 'خطأ في تحميل العقارات.',
        'pagination_info' => 'عرض :from إلى :to من أصل :total نتائج',
        'price_on_request' => 'السعر عند الطلب',
        'status_pending' => 'قيد المراجعة',
        'status_active' => 'مقبول',
        'status_rejected' => 'مرفوض',
        'status_unknown' => 'غير معروف',

        // Tooltips
        'edit_tooltip' => 'تعديل',
        'delete_tooltip' => 'حذف',

        // SweetAlerts & Confirmations
        'confirm_button' => 'نعم، نفذ الإجراء',
        'cancel_button' => 'إلغاء',
        'error_title' => 'خطأ!',

        'confirm_status_change_title' => 'تأكيد تغيير الحالة',
        'confirm_status_change_text' => 'هل أنت متأكد من تغيير حالة العقار من <b>:oldStatus</b> إلى <b>:newStatus</b>؟',
        'update_success_title' => 'تم التحديث!',
        'update_success_text' => 'تم تغيير حالة العقار بنجاح.',
        'update_error_text' => 'فشل تحديث الحالة: :error',

        'confirm_delete_title' => 'هل أنت متأكد؟',
        'confirm_delete_text' => 'لن تتمكن من التراجع عن هذا الإجراء!',
        'confirm_delete_button' => 'نعم، احذفه!',
        'delete_success_title' => 'تم الحذف!',
        'delete_success_text' => 'تم حذف العقار بنجاح.',
        'delete_error_text' => 'فشل حذف العقار: :error',


        'th_owner' => 'صاحب العقار',
        'phone_number' => 'رقم الهاتف',
        'member_since' => 'عضو منذ',
        'owner_details_title' => 'تفاصيل صاحب العقار',
        'blacklist_owner_button' => 'إضافة إلى القائمة السوداء',
        'confirm_blacklist_title' => 'تأكيد الحظر',
        'confirm_blacklist_text' => 'هل أنت متأكد من أنك تريد إضافة <strong>:name</strong> إلى القائمة السوداء؟',
        'confirm_blacklist_button' => 'نعم، قم بالحظر',
        'blacklist_success_title' => 'تم بنجاح',
        'blacklist_success_text' => 'تمت إضافة <strong>:name</strong> إلى القائمة السوداء.',

        'user_is_blacklisted' => 'هذا المستخدم في القائمة السوداء',
        'blacklisted_status' => 'هذا المستخدم محظور حاليًا.',
        'reactivate_owner_button' => 'إعادة التفعيل',
        'confirm_reactivate_title' => 'تأكيد إعادة التفعيل',
        'confirm_reactivate_text' => 'هل أنت متأكد من أنك تريد إعادة تفعيل حساب <strong>:name</strong>؟',
        'confirm_reactivate_button' => 'نعم، قم بإعادة التفعيل',
        'reactivate_success_title' => 'تم بنجاح',
        'reactivate_success_text' => 'تمت إعادة تفعيل حساب <strong>:name</strong>.',

        'owner_is_me' => 'لي',


        // blacklist
        'remove_from_blacklist' => 'إزالة من القائمة السوداء',
        'confirm_remove_blacklist_title' => 'تأكيد الإزالة',
        'confirm_remove_blacklist_text' => 'هل أنت متأكد من رغبتك في إزالة هذا العقار من القائمة السوداء؟',
        'remove_blacklist_success_title' => 'تمت الإزالة بنجاح!',
        'remove_blacklist_success_text' => 'تمت إزالة العقار من القائمة السوداء.',
        'no_properties_blacklisted' => 'لا توجد عقارات في القائمة السوداء',
        'th_blacklist_reason' => 'سبب الإدراج في القائمة السوداء',
        'blacklist_page_title' => 'إدارة العقارات المدرجة في القائمة السوداء',


        // favorite
        'favorites_page_title' => 'العقارات المفضلة',
        'remove_from_favorites' => 'إزالة من المفضلة',
        'confirm_remove_favorite_title' => 'تأكيد الإزالة',
        'confirm_remove_favorite_text' => 'هل أنت متأكد من رغبتك في إزالة هذا العقار من المفضلة؟',
        'remove_favorite_success_title' => 'تمت الإزالة بنجاح!',
        'remove_favorite_success_text' => 'تمت إزالة العقار من المفضلة.',
        'no_favorites_found' => 'لا توجد عقارات في المفضلة',
        'view_details' => 'عرض التفاصيل',


    ],
    'create' => [
        // Page & Section Titles
        'page_title' => 'إضافة عقار جديد',
        'section_basic_info' => 'معلومات أساسية',
        'section_categorization' => 'الفئة والتفاصيل',
        'section_publish' => 'نشر',
        'section_details' => 'تفاصيل العقار',
        'gallery_add_new' => 'إضافة صور',
        'section_custom_attributes' => 'بيانات إضافية',
        'section_location' => 'الموقع',
        'section_amenities' => 'المميزات والتجهيزات',
        'section_gallery' => 'معرض الصور',

        // Form Labels & Placeholders
        'label_description' => 'ادخل وصف مفصل حول العقار (:lang)',
        'label_location' => 'ادخل اسم مفصل حول العقار (:lang)',
        'label_area' => 'المساحة (متر مربع)',
        'label_year_built' => 'سنة البناء / سنة',
        'label_status' => 'نشر من أجل',
        'label_available_from' => 'يكون العقار متاحاً في',
        'label_price' => 'السعر',
        'label_price_on_request' => 'السعر عند الطلب',
        'label_category' => 'الفئة',
        'label_floor' => 'الطابق',
        'label_rooms' => 'عدد الغرف',
        'label_orientation' => 'الإتجاه',
        'label_location_desc' => 'ادخل وصف مفصل حول موقع العقار (:lang)',
        'label_city' => 'المدينة',
        'label_latitude' => 'خط العرض',
        'label_longitude' => 'خط الطول',
        'label_map' => 'حدد الموقع على الخريطة (انقر للتحديد أو اسحب العلامة)',
        'label_video_url' => 'رابط الفيديو (يوتيوب)',
        'label_amenities' => 'اختر الميزات:',
        'label_upload_button' => 'اختر الصور أو قم بالسحب والإفلات',
        'gallery_info' => 'الحد الأقصى 10 صور، 2 ميجابايت لكل صورة',
        'upload_note' => 'سيتم الرفع الفعلي بعد حفظ تفاصيل العقار.',

        // Select Placeholders
        'select_status_placeholder' => 'اختر النوع...',
        'select_category_placeholder' => 'اختر الفئة...',
        'select_floor_placeholder' => 'اختر الطابق...',
        'select_orientation_placeholder' => 'اختر الإتجاه...',
        'select_city_placeholder' => 'اختر المدينة...',
        'select_attribute_placeholder' => 'اختر :name...',

        // Buttons & Actions
        'button_save' => 'حفظ العقار',
        'button_cancel' => 'إلغاء',
        'button_saving' => 'جارٍ الحفظ...',
        'button_uploading_images' => 'جارٍ تحميل الصور...',
        'button_remove_image' => 'إزالة الصورة',

        // JS Messages & Placeholders
        'js_loading_attributes' => 'جارٍ تحميل البيانات...',
        'js_no_attributes' => 'لا توجد بيانات إضافية لهذه الفئة.',
        'js_error_loading_attributes' => 'فشل تحميل البيانات الإضافية.',
        'js_no_images_selected' => 'لم يتم تحديد صور للرفع.',
        'js_uploading_status' => 'جارٍ رفع :count صورة...',
        'js_uploading_progress' => 'جارٍ الرفع... :percent%',
        'js_upload_complete' => 'اكتمل الرفع!',
        'js_upload_error_parsing' => 'خطأ في الرفع (تحليل استجابة الخادم).',
        'js_upload_error_network' => 'خطأ في الرفع (مشكلة شبكة أو خادم).',
        'js_create_success_default' => 'تم إنشاء العقار بنجاح.',
        'js_images_processed' => 'وتمت معالجة الصور.',
        'js_create_success_img_error' => 'تم حفظ العقار، ولكن هناك مشكلة في الصور',
        'js_create_success_img_error_desc' => 'تم إنشاء العقار بنجاح. ولكن حدث خطأ أثناء تحميل الصور: :error. يمكنك محاولة تحميل الصور لاحقاً من صفحة تعديل العقار.',
        'js_form_error_default' => 'فشل في إنشاء العقار. الرجاء المحاولة مرة أخرى.',

        // SweetAlerts
        'swal_limit_exceeded_title' => 'تم تجاوز الحد',
        'swal_limit_exceeded_text' => 'يمكنك اختيار :max صور كحد أقصى. لقد اخترت بالفعل :current صورة.',
        'swal_file_too_large_title' => 'حجم الملف (الملفات) كبير',
        'swal_file_too_large_text' => 'حجم الصورة (الصور) ":files" يتجاوز الحد المسموح به (2 ميجابايت) ولم تتم إضافتها.',
        'swal_invalid_type_title' => 'نوع الملف غير صالح',
        'swal_invalid_type_text' => 'الملف ":name" ليس من أنواع الصور المدعومة.',
        'swal_success_title' => 'تم بنجاح!',
        'swal_error_title' => 'خطأ!',

        'swal_map_location_title' => 'حدد موقع العقار',
        'swal_map_location_text' => 'يرجى النقر على الخريطة أو سحب الدبوس لتحديد الموقع الدقيق لعقارك قبل الحفظ.',

        'swal_cancel_title' => 'هل أنت متأكد؟',
        'swal_cancel_text' => 'سيتم تجاهل أي تغييرات لم يتم حفظها عند مغادرة هذه الصفحة.',
        'swal_cancel_confirm_button' => 'نعم، مغادرة',
        'swal_cancel_abort_button' => 'البقاء ومتابعة التعديل',
        'swal_map_location_title' => 'Location Not Set',
        'swal_map_location_text' => 'Please click or drag the pin on the map to set the exact location.',
    ],
    'edit' => [
        // Page & Section Titles
        'page_title' => 'تعديل العقار: :name',
        'section_basic_info' => 'معلومات أساسية',
        'section_categorization' => 'الفئة والتفاصيل',
        'section_custom_attributes' => 'بيانات إضافية',
        'section_location' => 'الموقع',
        'section_amenities' => 'المميزات والتجهيزات',
        'section_gallery' => 'معرض الصور',
        'gallery_existing_images' => 'الصور الحالية',
        'gallery_add_new' => 'إضافة صور جديدة',

        'section_publish' => 'نشر',
        'section_details' => 'تفاصيل العقار',

        // Form Labels & Placeholders
        'label_description' => 'ادخل وصف مفصل حول العقار (:lang)',
        'label_location' => 'ادخل اسم مفصل حول العقار (:lang)',
        'label_area' => 'المساحة (متر مربع)',
        'label_year_built' => 'عمر البناء / سنة',
        'label_status' => 'نشر من أجل',
        'label_available_from' => 'يكون العقار متاحاً في',
        'label_price' => 'السعر',
        'label_price_on_request' => 'السعر عند الطلب',
        'label_category' => 'الفئة',
        'label_floor' => 'الطابق',
        'label_rooms' => 'عدد الغرف',
        'label_orientation' => 'الإتجاه',
        'label_location_desc' => 'ادخل وصف مفصل حول موقع العقار (:lang)',
        'label_city' => 'المدينة',
        'label_latitude' => 'خط العرض',
        'label_longitude' => 'خط الطول',
        'label_map' => 'حدد الموقع على الخريطة (انقر للتحديد أو اسحب العلامة)',
        'label_video_url' => 'رابط الفيديو (يوتيوب)',
        'label_amenities' => 'اختر الميزات:',
        'label_upload_button' => 'اختر الصور أو قم بالسحب والإفلات',

        // Select Placeholders
        'select_status_placeholder' => 'اختر النوع...',
        'select_category_placeholder' => 'اختر الفئة...',
        'select_floor_placeholder' => 'اختر الطابق...',
        'select_orientation_placeholder' => 'اختر الإتجاه...',
        'select_city_placeholder' => 'اختر المدينة...',
        'select_attribute_placeholder' => 'اختر :name...',

        // Buttons & Actions
        'button_update' => 'تحديث العقار',
        'button_cancel' => 'إلغاء',
        'button_updating' => 'جارٍ التحديث...',
        'button_uploading_images' => 'جارٍ تحميل الصور الجديدة...',
        'button_remove_image' => 'حذف هذه الصورة',
        'button_undo_delete' => 'إلغاء الحذف',

        // JS Messages & Placeholders
        'js_loading_attributes' => 'جارٍ تحميل البيانات...',
        'js_no_attributes' => 'لا توجد بيانات إضافية لهذه الفئة.',
        'js_error_loading_attributes' => 'فشل تحميل البيانات الإضافية.',
        'js_no_new_images' => 'لم يتم تحديد صور جديدة للرفع.',
        'js_uploading_status' => 'جارٍ رفع :count صورة جديدة...',
        'js_uploading_progress' => 'جارٍ رفع الصور الجديدة... :percent%',
        'js_upload_success_message' => 'تم رفع الصور الجديدة بنجاح!',
        'js_upload_error_network' => 'حدث خطأ في الشبكة أثناء رفع الصور.',
        'js_upload_error_invalid_response' => 'استجابة غير صالحة من الخادم أثناء رفع الصور.',
        'js_update_success_default' => 'تم تحديث العقار بنجاح.',
        'js_form_error_default' => 'فشل في تحديث العقار.',

        // SweetAlerts
        'swal_limit_exceeded_title' => 'تم تجاوز الحد',
        'swal_limit_exceeded_text' => 'الحد الأقصى للصور هو :max صور.',
        'swal_file_too_large_title' => 'حجم الملف كبير',
        'swal_file_too_large_text' => 'حجم الصورة ":name" يتجاوز الحد المسموح به (2 ميجابايت).',
        'swal_invalid_type_title' => 'نوع الملف غير صالح',
        'swal_invalid_type_text' => 'الملف ":name" ليس من أنواع الصور المدعومة.',
        'swal_success_title' => 'تم بنجاح!',
        'swal_error_title' => 'خطأ!',

        'swal_cancel_title' => 'هل أنت متأكد؟',
        'swal_cancel_text' => 'سيتم تجاهل أي تغييرات لم يتم حفظها عند مغادرة هذه الصفحة.',
        'swal_cancel_confirm_button' => 'نعم، مغادرة',
        'swal_cancel_abort_button' => 'البقاء ومتابعة التعديل',
    ]
];
