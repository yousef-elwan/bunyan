@extends('dashboard.layouts.default')

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('dashboard/style/notifications.css') }}">
@endpush

@section('content')
    {{-- <div class="page-header"> --}}
    {{-- <h1>Notifications</h1> --}}
    <div class="bg-card-bg shadow-sm rounded-lg mb-6 p-4">
        {{-- Breadcrumbs --}}
        <nav class="mb-2" aria-label="Breadcrumb">
            <ol class="flex items-center flex-nowrap overflow-hidden text-sm text-text-secondary">
                @isset($breadcrumbs)
                    @foreach ($breadcrumbs as $breadcrumb)
                        <!-- Each breadcrumb item is also a flex item that can shrink -->
                        <li class="flex items-center min-w-0 {{ $loop->last ? 'flex-shrink-0' : '' }}">
                            @if ($breadcrumb['url'])
                                <!-- The link itself will be truncated if needed -->
                                <a href="{{ $breadcrumb['url'] }}" class="truncate hover:text-accent-primary hover:underline"
                                    title="{{ $breadcrumb['name'] }}">
                                    <span>{{ $breadcrumb['name'] }}</span>
                                </a>
                                <!-- Separator icon -->
                                @if (!$loop->last)
                                    <i class="fas fa-angle-left text-gray-400 mx-2 flex-shrink-0"></i>
                                @endif
                            @else
                                <!-- The last item (current page) will not be truncated and will be bold -->
                                <span class="font-medium text-text-primary truncate" title="{{ $breadcrumb['name'] }}">
                                    {{ $breadcrumb['name'] }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                @endisset
            </ol>
        </nav>
        <h1 class="text-2xl font-bold text-text-primary" x-text="translate('page_title')"></h1>
        <div class="page-actions flex flex-row-reverse w-full" style="position: relative;">
            <button class="btn btn-icon" id="pageActionsToggle" aria-label="Toggle actions menu"
                style="font-size: 18px; background: none; border: none; cursor: pointer; color: var(--dark-text);">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="page-actions-dropdown hidden" id="pageActionsDropdown"
                dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
                style="position: absolute;
                    {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 0;
                    top: 100%;
                    background: white;
                    border: 1px solid var(--border-color);
                    border-radius: 4px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                    padding: 8px 0;
                    z-index: 1000;
                    min-width: 160px;">

                <button class="btn btn-secondary btn-small" id="markAllNotificationsRead"
                    dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
                    style="width: 100%; text-align: left; padding: 8px 16px; border: none; background: none; color: var(--dark-text); cursor: pointer; font-size: 14px;">
                    Mark all as read
                </button>
                <button class="btn btn-danger btn-small" id="clearAllNotifications"
                    style="width: 100%; text-align: left; padding: 8px 16px; border: none; background: none; color: #dc3545; cursor: pointer; font-size: 14px;">
                    <i class="fas fa-trash-alt"></i> Clear All
                </button>
            </div>
        </div>
    </div>
    {{-- </div> --}}

    <ul class="notifications-list" id="notificationsListUL">
        <li class="notification-item unread" data-notification-id="1">
            <span class="notification-icon property-alert"><i class="fas fa-home"></i></span>
            <div class="notification-content">
                <p><strong>New Property Enquiry:</strong> John
                    Doe is interested in "Beachside Villa".</p>
                <span class="notification-time">15 minutes
                    ago</span>
            </div>
            <div class="notification-actions">
                <button class="btn-icon btn-delete-notification" title="Delete" aria-label="Delete notification"><i
                        class="fas fa-times"></i></button>
            </div>
        </li>
        <li class="notification-item unread" data-notification-id="2">
            <span class="notification-icon system-alert"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="notification-content">
                <p><strong>System Update:</strong> Scheduled
                    maintenance tonight at 2 AM.</p>
                <span class="notification-time">1 hour
                    ago</span>
            </div>
            <div class="notification-actions">
                <!-- <button class="btn-icon btn-mark-read"
                                                                        title="Mark as read"
                                                                        aria-label="Mark as read"><i
                                                                            class="far fa-circle"></i></button> -->
                <button class="btn-icon btn-delete-notification" title="Delete" aria-label="Delete notification"><i
                        class="fas fa-times"></i></button>
            </div>
        </li>
        <li class="notification-item" data-notification-id="3">
            <!-- {/* Example of a read notification */} -->
            <span class="notification-icon message-alert"><i class="fas fa-comment-dots"></i></span>
            <div class="notification-content">
                <p><strong>New Message:</strong> Alice Smith
                    sent you a message regarding
                    "Downtown Apartment".</p>
                <span class="notification-time">3 hours
                    ago</span>
            </div>
            <div class="notification-actions">
                <!-- <button class="btn-icon btn-mark-unread"
                                                                        title="Mark as unread"
                                                                        aria-label="Mark as unread"><i
                                                                            class="fas fa-check-circle"></i></button> -->
                <button class="btn-icon btn-delete-notification" title="Delete" aria-label="Delete notification"><i
                        class="fas fa-times"></i></button>
            </div>
        </li>
        <!-- {/* More notifications */} -->
    </ul>
    <p id="noNotificationsMessage" class="hidden" style="text-align: center; padding: 30px; color: #777; font-size: 16px;">
        You
        have no new notifications.</p>
@endsection

@push('script')
    <script>
        const notificationsListUL = document.getElementById('notificationsListUL');
        const markAllNotificationsReadBtn = document.getElementById('markAllNotificationsRead');
        const clearAllNotificationsBtn = document.getElementById('clearAllNotifications');
        const noNotificationsMessageEl = document.getElementById('noNotificationsMessage');

        function updateNoNotificationsMessageVisibility() {
            if (notificationsListUL && noNotificationsMessageEl) {
                const items = notificationsListUL.querySelectorAll('.notification-item:not(.deleting)');
                noNotificationsMessageEl.classList.toggle('hidden', items.length > 0);
            }
        }

        if (notificationsListUL) {
            notificationsListUL.addEventListener('click', function(event) {
                const target = event.target;
                const notificationItem = target.closest('.notification-item');
                if (!notificationItem) return;

                const notificationId = notificationItem.dataset.notificationId;

                // Mark as read/unread
                if (target.closest('.btn-mark-read') || target.closest('.btn-mark-unread')) {
                    const isUnread = notificationItem.classList.contains('unread');
                    notificationItem.classList.toggle('unread', !isUnread);
                    const iconButton = notificationItem.querySelector('.btn-mark-read, .btn-mark-unread');
                    if (iconButton) {
                        if (isUnread) { // Was unread, now marking as read
                            iconButton.classList.remove('btn-mark-read');
                            iconButton.classList.add('btn-mark-unread');
                            iconButton.title = "Mark as unread";
                            iconButton.innerHTML = '<i class="fas fa-check-circle"></i>';
                        } else { // Was read, now marking as unread
                            iconButton.classList.remove('btn-mark-unread');
                            iconButton.classList.add('btn-mark-read');
                            iconButton.title = "Mark as read";
                            iconButton.innerHTML = '<i class="far fa-circle"></i>';
                        }
                    }
                    console.log(`Notification ${notificationId} marked as ${isUnread ? 'read' : 'unread'}`);
                    // TODO: API call to update status on server
                }

                // Delete notification
                if (target.closest('.btn-delete-notification')) {
                    if (confirm('Are you sure you want to delete this notification?')) {
                        notificationItem.classList.add('deleting');
                        // Wait for animation before removing from DOM
                        notificationItem.addEventListener('transitionend', () => {
                            notificationItem.remove();
                            updateNoNotificationsMessageVisibility();
                        }, {
                            once: true
                        }); // Ensure listener is called only once

                        console.log(`Notification ${notificationId} deleted`);
                        // TODO: API call to delete on server
                    }
                }
            });
        }

        if (markAllNotificationsReadBtn && notificationsListUL) {
            markAllNotificationsReadBtn.addEventListener('click', () => {
                notificationsListUL.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    const iconButton = item.querySelector('.btn-mark-read');
                    if (iconButton) {
                        iconButton.classList.remove('btn-mark-read');
                        iconButton.classList.add('btn-mark-unread');
                        iconButton.title = "Mark as unread";
                        iconButton.innerHTML = '<i class="fas fa-check-circle"></i>';
                    }
                });
                console.log("All notifications marked as read");
                // TODO: API call to update all on server
            });
        }

        if (clearAllNotificationsBtn && notificationsListUL) {
            clearAllNotificationsBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to clear ALL notifications? This cannot be undone.')) {
                    const items = notificationsListUL.querySelectorAll('.notification-item');
                    items.forEach(item => item.classList.add('deleting'));

                    // Wait for the last item's transition to finish or use a timeout
                    if (items.length > 0) {
                        // A simple timeout approach for bulk removal after animation starts
                        setTimeout(() => {
                            notificationsListUL.innerHTML = ''; // Clear all children
                            updateNoNotificationsMessageVisibility();
                        }, 300); // Match transition duration
                    } else {
                        updateNoNotificationsMessageVisibility();
                    }
                    console.log("All notifications cleared");
                    // TODO: API call to delete all on server
                }
            });
        }
        // Initial check for no notifications message
        updateNoNotificationsMessageVisibility();

        // New code: toggle dropdown menu for page actions
        const pageActionsToggle = document.getElementById('pageActionsToggle');
        const pageActionsDropdown = document.getElementById('pageActionsDropdown');

        if (pageActionsToggle && pageActionsDropdown) {
            pageActionsToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                pageActionsDropdown.classList.toggle('hidden');
            });

            // Close dropdown if clicking outside
            document.addEventListener('click', () => {
                if (!pageActionsDropdown.classList.contains('hidden')) {
                    pageActionsDropdown.classList.add('hidden');
                }
            });
        }
    </script>
@endpush
