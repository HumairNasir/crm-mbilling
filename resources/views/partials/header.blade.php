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
            <img src="../images/notifications.svg" alt="">
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

