<?php

return [
    //======================================================================
    // General Terms
    //======================================================================
    'general' => [
        'dashboard' => 'Dashboard',
        'view_all' => 'View All',
        'no_data' => 'No data to display',
        'by' => 'By',
        'on' => 'On',
        'last_repo' => 'Last Report',
    ],

    //======================================================================
    // Stat Cards
    //======================================================================
    'stats' => [
        'total_properties' => 'Total Properties',
        'monthly_sales' => 'Monthly Sales',
        'pending_reports' => 'Open Reports',
        'unread_messages' => 'Unread Messages',
        'today_appointments' => 'Today\'s Appointments',
        'total_users' => 'Total Users',

        'trend' => [
            'property_available' => 'Property Available',
            'successful_deal' => 'Successful Deal',
            'action_required' => 'Action Required',
            'reply_required' => 'Reply Required',
            'important' => 'Important Appointment',
            'user' => 'User',
        ],
    ],

    //======================================================================
    // Latest Properties Card
    //======================================================================
    'properties' => [
        'latest_title' => 'Latest Properties',
        'table_header' => [
            'property' => 'Property',
            'owner' => 'Owner',
            'price' => 'Price',
            'date' => 'Date',
            'status' => 'Status',
        ],
        'no_recent' => 'No recent properties to display.',
    ],

    //======================================================================
    // Latest Reports Card
    //======================================================================
    'reports' => [
        'latest_title' => 'Latest Reports',
        'table_header' => [
            'subject' => 'Subject',
            'status' => 'Status',
        ],
        'on_property' => 'On Property:',
        'no_recent' => 'No recent reports.',
    ],

    //======================================================================
    // Upcoming Appointments Card
    //======================================================================
    'appointments' => [
        'upcoming_title' => 'Upcoming Appointments',
        'with_client' => 'With:',
        'no_upcoming' => 'No upcoming appointments.',
    ],

    //======================================================================
    // Chart/Graph Cards in Sidebar
    //======================================================================
    'charts' => [
        'property_types' => 'Property Types',
        'report_statuses' => 'Report Statuses',
    ],

    //======================================================================
    // Statuses (Used across the app)
    //======================================================================
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'resolved' => 'Resolved',
        'rejected' => 'Rejected',
        'in_progress' => 'In Progress',
    ],

    //======================================================================
    // Modals (Dialogs)
    //======================================================================
    'modals' => [
        'report_details' => [
            'title' => 'Report Details',
            'related_property_title' => 'Property Related to the Report',
            'open_property_page_btn' => 'Open Property Page',
            'report_subject_label' => 'Report Subject',
            'report_description_label' => 'Report Description',
            'property_description_label' => 'Property Description',
            'action_label' => 'Take Action',
            'save_btn' => 'Save Changes',
        ],
        'appointment_details' => [
            'title' => 'Appointment Details',
            'title_label' => 'Title',
            'client_label' => 'Client',
            'datetime_label' => 'Date & Time',
            'notes_label' => 'Notes',
            'no_notes' => 'No notes provided.',
            'edit_btn' => 'Edit',
        ],
        'general' => [
            'cancel_btn' => 'Cancel',
        ],
    ],
];
