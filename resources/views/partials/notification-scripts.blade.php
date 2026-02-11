<script>
$(document).ready(function() {
    var ICON_MAP = { 'batch': '📋', 'convert': '✅' };

    // Toggle dropdown
    $('#notificationBell').on('click', function(e) {
        e.stopPropagation();
        var $dd = $('#notificationDropdown');
        $dd.toggleClass('open');
        if ($dd.hasClass('open')) {
            fetchNotifications();
        }
    });

    // Close on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.notification-wrapper').length) {
            $('#notificationDropdown').removeClass('open');
        }
    });

    // Fetch notifications
    function fetchNotifications() {
        $.get("/notifications", function(data) {
            updateBadge(data.unread_count);
            renderNotifications(data.notifications);
        });
    }

    // Update badge
    function updateBadge(count) {
        var $badge = $('#notificationBadge');
        if (count > 0) {
            $badge.text(count > 99 ? '99+' : count).show();
        } else {
            $badge.hide();
        }
    }

    // Render list
    function renderNotifications(notifications) {
        var $list = $('#notificationList');

        if (!notifications.length) {
            $list.html('<div class="notification-empty">No notifications yet</div>');
            return;
        }

        var html = '';
        for (var i = 0; i < notifications.length; i++) {
            var n = notifications[i];
            var isUnread = !n.read_at;
            var icon = ICON_MAP[n.icon] || 'ℹ️';
            var timeAgo = getTimeAgo(new Date(n.created_at));

            html += '<div class="notification-item ' + (isUnread ? 'unread' : '') + '" data-id="' + n.id + '" data-url="' + (n.action_url || '#') + '">';
            html += '  <div class="notification-icon-circle ' + n.icon + '">' + icon + '</div>';
            html += '  <div class="notification-content">';
            html += '    <div class="notification-title">' + escapeHtml(n.title) + '</div>';
            html += '    <div class="notification-message">' + escapeHtml(n.message) + '</div>';
            html += '    <div class="notification-time">' + timeAgo + '</div>';
            html += '  </div>';
            html += '</div>';
        }

        $list.html(html);
    }

    // Click notification → mark read + navigate
    $(document).on('click', '.notification-item', function() {
        var id = $(this).data('id');
        var url = $(this).data('url');
        var $item = $(this);

        $.post('/notifications/' + id + '/read', { _token: '{{ csrf_token() }}' }, function() {
            $item.removeClass('unread');
            var currentCount = parseInt($('#notificationBadge').text()) || 0;
            updateBadge(Math.max(0, currentCount - 1));

            if (url && url !== '#') {
                window.location.href = url;
            }
        });
    });

    // Mark all as read
    $('#markAllReadBtn').on('click', function(e) {
        e.stopPropagation();
        $.post("/notifications/mark-all-read", { _token: '{{ csrf_token() }}' }, function() {
            fetchNotifications();
        });
    });

    // Time ago helper
    function getTimeAgo(date) {
        var seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 60) return 'Just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
        if (seconds < 604800) return Math.floor(seconds / 86400) + 'd ago';
        return date.toLocaleDateString();
    }

    // Escape HTML
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Poll badge count every 30 seconds
    setInterval(function() {
        $.get("/notifications/unread-count", function(data) {
            updateBadge(data.unread_count);
        });
    }, 30000);

    // Initial badge load
    $.get("/notifications/unread-count", function(data) {
        updateBadge(data.unread_count);
    });
});
</script>
