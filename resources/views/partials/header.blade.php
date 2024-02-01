<header>
    <div>
        <div class="sitelogo">
            <a href="{{ url('/') }}"><img src="../images/logo-header.svg" alt=""></a>
        </div>
        <div class="search-main">
            <input type="search" name="search" id="search" placeholder="Search...">
            <img src="../images/search.svg" alt="">
        </div>
        <div class="header-notifications">
            <img src="../images/notifications.svg" alt="">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <img src="../images/img.svg" alt="Logout" style="cursor: pointer;" onclick="event.preventDefault(); this.closest('form').submit();">
            </form>
        </div>
    </div>
</header>
