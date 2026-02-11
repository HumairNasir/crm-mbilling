<header>
    <div>
        <div class="sitelogo">
            <a href="{{ url('/') }}"><img src="../images/logo-header.png" alt=""></a>
        </div>
        <div class="search-main">
            <input type="search" name="search" id="search" placeholder="Search...">
            <img src="../images/search.svg" alt="">
        </div>
        <div class="header-notifications">
            {{-- Notification Bell --}}
            <div class="notification-wrapper" id="notificationWrapper">
                <div class="notification-bell" id="notificationBell">
                    <img src="../images/notifications.svg" alt="Notifications">
                    <span class="notification-badge" id="notificationBadge" style="display:none;">0</span>
                </div>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-dropdown-header">
                        <h6>Notifications</h6>
                        <button id="markAllReadBtn" class="mark-all-read-btn">Mark all read</button>
                    </div>
                    <div class="notification-dropdown-body" id="notificationList">
                        <div class="notification-empty">No notifications</div>
                    </div>
                </div>
            </div>

            <form id="logoutForm" class="m-0" method="POST" action="{{ route('logout') }}">
                @csrf
                <div id="logoutButton" onclick="submitForm(this)">
                    <img src="../images/logout.svg" alt="Logout" style="cursor: pointer;">
                </div>
            </form>
        </div>
    </div>
<script>
    function submitForm(button) {
        button.onclick = null;
        var form = document.getElementById('logoutForm');
        form.submit();
    }
</script>
</header>