<?php

return
    [
        'list' => [
            // Page & Buttons
            'page_title' => 'My Properties',
            'page_title_admin' => 'All Properties',
            'add_new_button' => 'Add New Property',
            'search_button' => 'Search',
            'refresh_button' => 'Refresh',
            'advanced_search_button' => 'Advanced Search',
            'advanced_search_title' => 'Advanced Search',
            'reset_button' => 'Reset',
            'apply_button' => 'Apply',

            // Filters
            'filter_status' => 'Status',
            'select_status' => 'All Status',

            'filter_owner' => 'Owner',
            'select_owner' => 'All Owners',

            'filter_city' => 'City',
            'select_city' => 'All Cities',

            'filter_category' => 'Category',
            'select_category' => 'All Category',

            'filter_type' => 'Type',
            'select_type' => 'All Types',

            'filter_floor' => 'Floor',
            'select_floor' => 'All Floors',

            'filter_orientation' => 'Orientation',
            'select_orientation' => 'All Orientations',

            'filter_contract_type' => 'Contract',
            'select_contract_type' => 'All Contract Types',

            'price_range' => 'Price Range',
            'min_price' => 'Min Price',
            'max_price' => 'Max Price',

            'min_price_label' => 'Minimum Price ($)',
            'max_price_label' => 'Maximum Price ($)',
            'filter_amenities' => 'Amenities',

            // Table Headers
            'th_image' => 'Images',
            'th_title' => 'Title',
            'th_city' => 'City',
            'th_type' => 'Type',
            'th_category' => 'Category',
            'th_status' => 'Current Status',
            'th_views' => 'Number of Views',
            'th_price' => 'Price',
            'th_date' => 'Date Added',
            'th_actions' => 'Actions',

            // Table Content
            'no_properties_found' => 'No properties matching the search criteria were found.',
            'error_loading_properties' => 'Error loading properties.',
            'pagination_info' => 'Showing :from to :to of :total results',
            'price_on_request' => 'Price on Request',
            'status_pending' => 'Pending Review',
            'status_active' => 'Approved',
            'status_rejected' => 'Rejected',
            'status_unknown' => 'Unknown',

            // Tooltips
            'edit_tooltip' => 'Edit',
            'delete_tooltip' => 'Delete',

            // SweetAlerts & Confirmations
            'confirm_button' => 'Yes, execute the action',
            'cancel_button' => 'Cancel',
            'error_title' => 'Error!',

            'confirm_status_change_title' => 'Confirm Status Change',
            'confirm_status_change_text' => 'Are you sure you want to change the property status from :oldStatus to :newStatus?',
            'update_success_title' => 'Updated!',
            'update_success_text' => 'Property status changed successfully.',
            'update_error_text' => 'Failed to update status: :error',

            'confirm_delete_title' => 'Are you sure?',
            'confirm_delete_text' => 'You will not be able to recover this property!',
            'confirm_delete_button' => 'Yes, delete it!',
            'delete_success_title' => 'Deleted!',
            'delete_success_text' => 'Property deleted successfully.',
            'delete_error_text' => 'Failed to delete property: :error',


            'th_owner' => 'Property Owner',
            'phone_number' => 'Phone Number',
            'member_since' => 'Member Since',
            'owner_details_title' => 'Property Owner Details',
            'blacklist_owner_button' => 'Add to Blacklist',
            'confirm_blacklist_title' => 'Confirm Blacklist',
            'confirm_blacklist_text' => 'Are you sure you want to add <strong>:name</strong> to the blacklist?',
            'confirm_blacklist_button' => 'Yes, blacklist',
            'blacklist_success_title' => 'Success',
            'blacklist_success_text' => '<strong>:name</strong> has been added to the blacklist.',

            'user_is_blacklisted' => 'هذا المستخدم في القائمة السوداء',
            'blacklisted_status' => 'هذا المستخدم محظور حاليًا.',
            'reactivate_owner_button' => 'إعادة التفعيل',
            'confirm_reactivate_title' => 'تأكيد إعادة التفعيل',
            'confirm_reactivate_text' => 'هل أنت متأكد من أنك تريد إعادة تفعيل حساب <strong>:name</strong>؟',
            'confirm_reactivate_button' => 'نعم، قم بإعادة التفعيل',
            'reactivate_success_title' => 'تم بنجاح',
            'reactivate_success_text' => 'تمت إعادة تفعيل حساب <strong>:name</strong>.',

            'owner_is_me' => 'me',

            // blacklist
            'remove_from_blacklist' => 'Remove from Blacklist',
            'confirm_remove_blacklist_title' => 'Confirm Removal',
            'confirm_remove_blacklist_text' => 'Are you sure you want to remove this property from the blacklist?',
            'remove_blacklist_success_title' => 'Successfully Removed!',
            'remove_blacklist_success_text' => 'The property has been removed from the blacklist.',
            'no_properties_blacklisted' => 'No properties in blacklist',
            'th_blacklist_reason' => 'Blacklist Reason',
            'blacklist_page_title' => 'Blacklisted Properties Management',


            // favorite
            'favorites_page_title' => 'Favorite Properties',
            'remove_from_favorites' => 'Remove from Favorites',
            'confirm_remove_favorite_title' => 'Confirm Removal',
            'confirm_remove_favorite_text' => 'Are you sure you want to remove this property from your favorites?',
            'remove_favorite_success_title' => 'Successfully Removed!',
            'remove_favorite_success_text' => 'The property has been removed from your favorites.',
            'no_favorites_found' => 'No favorite properties found',
            'view_details' => 'View Details',
        ],
        'create' => [
            // Page & Section Titles
            'page_title' => 'Add New Property',
            'section_basic_info' => 'Basic Information',
            'section_publish' => 'Publish',
            'section_details' => 'Property Details',
            'section_categorization' => 'Categorization & Details',
            'gallery_add_new' => 'Add Images',
            'section_custom_attributes' => 'Additional Details',
            'section_location' => 'Location',
            'section_amenities' => 'Amenities & Features',
            'section_gallery' => 'Image Gallery',

            // Form Labels & Placeholders
            'label_description' => 'Enter a detailed description of the property (:lang)',
            'label_location' => 'Enter a detailed location of the property (:lang)',
            'label_area' => 'Area (sq m/ft)',
            'label_year_built' => 'Year Built',
            'label_status' => 'Listed For',
            'label_available_from' => 'Available From',
            'label_price' => 'Price',
            'label_price_on_request' => 'Price on Request',
            'label_category' => 'Category',
            'label_floor' => 'Floor',
            'label_rooms' => 'Number of Rooms',
            'label_orientation' => 'Orientation',
            'label_location_desc' => 'Enter a detailed description of the property location (:lang)',
            'label_city' => 'City',
            'label_latitude' => 'Latitude',
            'label_longitude' => 'Longitude',
            'label_map' => 'Set location on map (Click to set or drag marker)',
            'label_video_url' => 'Video Link (YouTube)',
            'label_amenities' => 'Select amenities:',
            'label_upload_button' => 'Choose images or Drag & Drop',
            'gallery_info' => 'Max 10 images, 2MB each',
            'upload_note' => 'The actual upload will happen after property details are saved.',

            // Select Placeholders
            'select_status_placeholder' => 'Select Status...',
            'select_category_placeholder' => 'Select Category...',
            'select_floor_placeholder' => 'Select Floor...',
            'select_orientation_placeholder' => 'Select Orientation...',
            'select_city_placeholder' => 'Select City...',
            'select_attribute_placeholder' => 'Select :name...',
            'select_year_placeholder' =>  'Select Year...',

            // Buttons & Actions
            'button_save' => 'Save Property',
            'button_cancel' => 'Cancel',
            'button_saving' => 'Saving...',
            'button_uploading_images' => 'Uploading images...',
            'button_remove_image' => 'Remove image',

            // JS Messages & Placeholders
            'js_loading_attributes' => 'Loading attributes...',
            'js_no_attributes' => 'No custom attributes for this category.',
            'js_error_loading_attributes' => 'Could not load attributes.',
            'js_no_images_selected' => 'No images selected for upload.',
            'js_uploading_status' => 'Uploading :count image(s)...',
            'js_uploading_progress' => 'Uploading... :percent%',
            'js_upload_complete' => 'Upload complete!',
            'js_upload_error_parsing' => 'Upload error (parsing server response).',
            'js_upload_error_network' => 'Upload error (network or server issue).',
            'js_create_success_default' => 'Property created successfully.',
            'js_images_processed' => 'and images were processed.',
            'js_create_success_img_error' => 'Property was saved, but there was an issue with images',
            'js_create_success_img_error_desc' => 'Property created successfully. However, an error occurred during image upload: :error. You can try uploading images later from the edit page.',
            'js_form_error_default' => 'Failed to create the property. Please try again.',

            // SweetAlerts
            'swal_limit_exceeded_title' => 'Limit Exceeded',
            'swal_limit_exceeded_text' => 'You can select a maximum of :max images. You have already selected :current.',
            'swal_file_too_large_title' => 'File(s) Too Large',
            'swal_file_too_large_text' => 'Image(s) ":files" exceed the 2MB limit and were not added.',
            'swal_invalid_type_title' => 'Invalid File Type',
            'swal_invalid_type_text' => 'File ":name" is not a supported image type.',
            'swal_success_title' => 'Success!',
            'swal_error_title' => 'Error!',

            'swal_map_location_title' => 'Set Property Location',
            'swal_map_location_text' => 'Please click on the map or drag the pin to set the exact location of your property before saving.',

            'swal_cancel_title' => 'Are you sure?',
            'swal_cancel_text' => 'Any unsaved changes will be lost if you leave this page.',
            'swal_cancel_confirm_button' => 'Yes, leave page',
            'swal_cancel_abort_button' => 'Stay and continue editing',
            'swal_map_location_title' => 'Location Not Set',
            'swal_map_location_text' => 'Please click or drag the pin on the map to set the exact location.',
        ],
        'edit' => [
            // Page & Section Titles
            'page_title' => 'Edit Property: :name',
            'section_basic_info' => 'Basic Information',
            'section_categorization' => 'Categorization & Details',
            'section_custom_attributes' => 'Additional Details',
            'section_location' => 'Location',
            'section_amenities' => 'Amenities & Features',
            'section_gallery' => 'Image Gallery',
            'gallery_existing_images' => 'Current Images',
            'gallery_add_new' => 'Add New Images',

            'section_publish' => 'Publish',
            'section_details' => 'Property Details',

            // Form Labels & Placeholders
            'label_description' => 'Enter a detailed description of the property (:lang)',
            'label_location' => 'Enter a detailed location of the property (:lang)',
            'label_area' => 'Area (sq m/ft)',
            'label_year_built' => 'Year Built',
            'label_status' => 'Listed For',
            'label_available_from' => 'Available From',
            'label_price' => 'Price',
            'label_price_on_request' => 'Price on Request',
            'label_category' => 'Category',
            'label_floor' => 'Floor',
            'label_rooms' => 'Number of Rooms',
            'label_orientation' => 'Orientation',
            'label_location_desc' => 'Enter a detailed description of the property location (:lang)',
            'label_city' => 'City',
            'label_latitude' => 'Latitude',
            'label_longitude' => 'Longitude',
            'label_map' => 'Set location on map (Click to set or drag marker)',
            'label_video_url' => 'Video Link (YouTube)',
            'label_amenities' => 'Select amenities:',
            'label_upload_button' => 'Choose images or Drag & Drop',

            // Select Placeholders
            'select_status_placeholder' => 'Select Status...',
            'select_category_placeholder' => 'Select Category...',
            'select_floor_placeholder' => 'Select Floor...',
            'select_year_placeholder' => 'Select Year...',
            'select_orientation_placeholder' => 'Select Orientation...',
            'select_city_placeholder' => 'Select City...',
            'select_attribute_placeholder' => 'Select :name...',

            // Buttons & Actions
            'button_update' => 'Update Property',
            'button_cancel' => 'Cancel',
            'button_updating' => 'Updating...',
            'button_uploading_images' => 'Uploading new images...',
            'button_remove_image' => 'Remove this image',
            'button_undo_delete' => 'Undo Delete',

            // JS Messages & Placeholders
            'js_loading_attributes' => 'Loading attributes...',
            'js_no_attributes' => 'No custom attributes for this category.',
            'js_error_loading_attributes' => 'Could not load attributes.',
            'js_no_new_images' => 'No new images selected for upload.',
            'js_uploading_status' => 'Uploading :count new image(s)...',
            'js_uploading_progress' => 'Uploading new images... :percent%',
            'js_upload_success_message' => 'New images uploaded successfully!',
            'js_upload_error_network' => 'A network error occurred while uploading new images.',
            'js_upload_error_invalid_response' => 'Invalid JSON response from server during image upload.',
            'js_update_success_default' => 'Property updated successfully.',
            'js_form_error_default' => 'Failed to update the property.',

            // SweetAlerts
            'swal_limit_exceeded_title' => 'Limit Exceeded',
            'swal_limit_exceeded_text' => 'You can have a maximum of :max images in total.',
            'swal_file_too_large_title' => 'File Too Large',
            'swal_file_too_large_text' => 'Image ":name" exceeds the 2MB limit.',
            'swal_invalid_type_title' => 'Invalid File Type',
            'swal_invalid_type_text' => 'File ":name" is not a supported image type.',
            'swal_success_title' => 'Success!',
            'swal_error_title' => 'Error!',

            'swal_cancel_title' => 'Are you sure?',
            'swal_cancel_text' => 'Any unsaved changes will be lost if you leave this page.',
            'swal_cancel_confirm_button' => 'Yes, leave page',
            'swal_cancel_abort_button' => 'Stay and continue editing',
        ]
    ];
