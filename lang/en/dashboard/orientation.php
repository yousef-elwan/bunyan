<?php
return [
    //======================================
    // 'list' section for the orientations list page
    //======================================
    'list' => [
        // Page & Buttons
        'page_title' => 'Orientations',
        'add_new_button' => 'Add New Orientation',
        'refresh_button' => 'Refresh',

        // Table Headers
        'name' => 'Name',
        'addon' => 'Date Added',
        'actions' => 'Actions',

        // Table Content
        'no_data_found' => 'No orientations found.',
        'error_loading' => 'Error loading orientations.',

        // Tooltips
        'edit_tooltip' => 'Edit',
        'delete_tooltip' => 'Delete',

        'pagination_info' => 'Showing :from to :to of :total results',

        // SweetAlerts & Confirmations for Delete
        'confirm_delete_title' => 'Are you sure?',
        'confirm_delete_text' => "You won't be able to revert this!",
        'confirm_delete_button' => 'Yes, delete it!',
        'cancel_button' => 'Cancel',

        'delete_success_title' => 'Deleted!',
        'delete_success_text' => 'The orientation has been deleted.',
        'error_title' => 'Error!', // General error title
        'delete_error_text' => 'Failed to delete the orientation: :error',
    ],
    //======================================
    // 'create' section for the add new orientation page
    //======================================
    'create' => [
        // Page & Buttons
        'page_title' => 'Add New Orientation',
        'primaryinfo' => 'Primary Information',
        'entername' => 'Enter Name',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'saving' => 'Saving...',

        // SweetAlerts & JS Messages for Create
        'create_success_title' => 'Success!',
        'create_success_text' => 'The orientation has been created successfully.',
        'error_title' => 'Error!',
        'form_error_text' => 'Failed to save the orientation. Please try again.',
    ],
    //======================================
    // 'edit' section for the edit orientation page
    //======================================
    'edit' => [
        // Page & Buttons
        'page_title' => 'Edit Orientation',
        'primaryinfo' => 'Primary Information',
        'entername' => 'Enter Name',
        'save' => 'Save Changes',
        'cancel' => 'Cancel',
        'saving' => 'Saving...',

        // SweetAlerts & JS Messages for Update
        'update_success_title' => 'Success!',
        'update_success_text' => 'The orientation has been updated successfully.',
        'error_title' => 'Error!',
        'form_error_text' => 'Failed to save the orientation. Please try again.',
    ],
];
