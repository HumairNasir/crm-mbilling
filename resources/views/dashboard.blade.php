{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged as {{ Auth::user()->name }} ! 
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Poppins:wght@500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Kriss Crm</title>
</head>

<body>
    <header>
        <div>
            <div class="sitelogo">
                <img src="../images/logo-header.svg" alt="">
            </div>
            <div class="search-main">
                <input type="search" name="search" id="search" placeholder="Search...">
                <img src="../images/search.svg" alt="">
            </div>
            <div class="header-notifications">
                <img src="../images/notifications.svg" alt="">
                <img src="../images/img.svg" alt="">
            </div>
        </div>
    </header>
    <div class="page-content">
        <div class="sidebar">
            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M2.25 11.9991L11.204 3.04409C11.644 2.60509 12.356 2.60509 12.795 3.04409L21.75 11.9991M4.5 9.74909V19.8741C4.5 20.4951 5.004 20.9991 5.625 20.9991H9.75V16.1241C9.75 15.5031 10.254 14.9991 10.875 14.9991H13.125C13.746 14.9991 14.25 15.5031 14.25 16.1241V20.9991H18.375C18.996 20.9991 19.5 20.4951 19.5 19.8741V9.74909M8.25 20.9991H16.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg><span>Dashboard</span></a>
            <a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M6.4 8C6.55913 8 6.71174 7.93679 6.82426 7.82426C6.93679 7.71174 7 7.55913 7 7.4V3.6C7 3.44087 7.06321 3.28826 7.17574 3.17574C7.28826 3.06321 7.44087 3 7.6 3H16.4C16.4788 3 16.5568 3.01552 16.6296 3.04567C16.7024 3.07583 16.7685 3.12002 16.8243 3.17574C16.88 3.23145 16.9242 3.29759 16.9543 3.37039C16.9845 3.44319 17 3.52121 17 3.6V7.4C17 7.55913 17.0632 7.71174 17.1757 7.82426C17.2883 7.93679 17.4409 8 17.6 8H19.4C19.4788 8 19.5568 8.01552 19.6296 8.04567C19.7024 8.07583 19.7685 8.12002 19.8243 8.17574C19.88 8.23145 19.9242 8.29759 19.9543 8.37039C19.9845 8.44319 20 8.52121 20 8.6V20.4C20 20.4788 19.9845 20.5568 19.9543 20.6296C19.9242 20.7024 19.88 20.7685 19.8243 20.8243C19.7685 20.88 19.7024 20.9242 19.6296 20.9543C19.5568 20.9845 19.4788 21 19.4 21H4.6C4.44087 21 4.28826 20.9368 4.17574 20.8243C4.06321 20.7117 4 20.5591 4 20.4V8.6C4 8.44087 4.06321 8.28826 4.17574 8.17574C4.28826 8.06321 4.44087 8 4.6 8H6.4V8Z" stroke="#535353" stroke-width="1.5" />
                    <path d="M9.992 8H11.992M13.992 8H11.992M11.992 8V6M11.992 8V10M16 17.01L16.01 16.999M16 13.01L16.01 12.999M12 13.01L12.01 12.999M8 13.01L8.01 12.999M8 17.01L8.01 16.999M12 17.01L12.01 16.999" stroke="#535353" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg><span>Dental Offices</span></a>
        </div>
        <div class="content-main">
            <div class="dashboard">
                <h3>Dashboard</h3>
            </div>
            <div class="sales-main-graph">
                <div class="wd-sm">
                    <div class="graph-section-icons">
                        <img src="../images/calendar.svg" alt="" class="calendar">
                        <img src="../images/filter.svg" alt="" class="filter">
                    </div>
                    <p class="sales-year">Year 2024 Sales</p>
                    <p class="sales-amount">$230,181</p>
                </div>
                <div class="wd-sm">
                    <div class="sales-resp">
                        <div>
                            <h4>Sales Response</h4>
                            <p>This Year</p>
                            <h3>4192</h3>
                        </div>
                        <div> <img src="../images/filter.svg" alt="" class="filter">
                        </div>
                    </div>
                    <div id="donutchart"></div>
                </div>
                <div class="wd-bg">
                    <div class="sales-resp">
                        <div>
                            <h4>Sales by Month</h4>
                        </div>
                        <div><img src="../images/filter.svg" alt="" class="filter"></div>
                    </div>
                    <div id="barchart"></div>
                </div>
            </div>
            <div class="region-map-main">
                <div class="region-map">
                    <div class="sales-by-region">
                        <h3>Sales by Region 9</h3>
                    </div>
                    <div class="search-main">
                        <input type="search" name="search" id="search" placeholder="Search...">
                        <img src="../images/search.svg" alt="">
                    </div>
                    <div>
                        <img src="../images/filter.svg" alt="" class="filter">
                    </div>
                </div>
                <div class="map-vectors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="469" height="397" viewBox="0 0 469 397" fill="none">
                        <path d="M456.017 275.68C459.399 313.955 462.693 352.23 466.164 390.593V392.196C461.302 391.447 456.793 389.207 453.258 385.787C450.231 382.582 448.629 377.954 444.98 375.284C438.838 370.477 430.115 373.147 422.371 373.948C410.403 374.4 398.543 371.535 388.101 365.67L355.524 327.574C351.17 320.928 345.896 314.934 339.858 309.771C332.921 306.965 325.165 306.965 318.228 309.771C293.928 315.468 269.806 321.343 245.506 327.574L100.952 362.377L55.4672 373.414C48.8803 374.927 22.4443 386.054 19.2399 375.907C18.8841 373.129 18.8841 370.317 19.2399 367.539C18.6561 362.723 16.4229 358.257 12.9199 354.9C9.45415 351.504 6.75345 347.407 4.99805 342.884C20.1299 332.024 34.9948 320.987 49.5036 309.86C52.8362 307.974 55.7427 305.419 58.0404 302.355C60.3381 299.291 61.9774 295.786 62.8552 292.058C62.3964 285.304 59.4466 278.96 54.5773 274.256C14.6113 225.033 126.943 231.175 148.484 229.75C156.805 230.029 164.971 227.453 171.627 222.451C175.543 218.624 177.59 213.55 181.774 209.545C189.785 202.602 201.98 205.005 212.572 203.403C220.769 201.886 228.254 197.753 233.903 191.622C239.552 185.492 243.061 177.695 243.904 169.401C244.438 162.992 241.233 154.714 235.003 154.981C232.417 155.53 229.927 156.46 227.614 157.741C223.667 158.665 219.515 158.059 215.997 156.044C212.478 154.029 209.855 150.754 208.655 146.881C203.226 130.147 224.232 110.209 231.976 96.1449C241.47 77.9985 253.822 61.4984 268.56 47.2778C284.671 33.3031 318.228 29.4757 338.611 22.2658C358.995 15.0559 378.488 8.91411 398.338 2.23828L398.783 3.39547C404.747 42.0263 410.621 80.568 416.585 119.11C416.891 122.545 417.827 125.893 419.344 128.99C422.282 133.975 428.245 136.645 432.073 140.739C438.126 146.525 435.455 153.735 435.455 161.123C435.455 165.484 433.764 188.271 436.88 190.853C441.419 194.502 445.247 198.152 449.786 201.712C449.786 202.602 449.786 203.403 449.786 204.204L456.017 275.68Z" fill="#999999" stroke="#5F5F5F" stroke-width="8" stroke-miterlimit="10" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="424" height="282" viewBox="0 0 424 282" fill="none">
                        <path d="M188.85 255.302C158.675 260.375 128.411 265.36 98.1477 269.9C73.8477 273.816 49.5476 277.377 25.1586 280.937C21.6871 277.021 20.708 274.35 17.6817 270.612C17.4204 270.2 17.1224 269.813 16.7915 269.455C15.5453 254.59 14.6554 238.924 13.3202 223.881C12.1631 210.974 10.9167 198.068 9.75954 185.161C5.93207 142.525 1.9266 99.8885 -1.90088 57.3412C7.89034 50.4874 17.6816 43.4556 27.3838 36.4237C29.1392 40.9474 31.8402 45.0441 35.3059 48.4401C38.809 51.7971 41.0419 56.2631 41.6256 61.0797C41.2699 63.8574 41.2699 66.6691 41.6256 69.4467C44.83 79.594 71.2664 68.4676 77.8532 66.9544L123.338 55.9172L267.892 21.1138C292.192 15.2391 316.314 9.36436 340.614 3.3116C347.551 0.505507 355.306 0.505507 362.243 3.3116C368.281 8.47441 373.556 14.4683 377.909 21.1138L410.487 59.2105C408.084 79.6831 403.277 100.601 400.874 121.518C408.15 126.759 414.22 133.497 418.676 141.279C423.004 149.167 424.028 158.449 421.525 167.092C419.91 171.278 417.134 174.917 413.524 177.582C409.913 180.246 405.618 181.826 401.141 182.135L399.005 216.493H398.471C328.746 230.497 258.872 243.434 188.85 255.302Z" fill="#797979" stroke="#5F5F5F" stroke-width="1.756" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>
            <div class="sales-record-main">
                <div class="top-representatives">
                    <div class="region-map">
                        <div class="sales-by-region representativesales">
                            <h3>Sales Record of top Representatives</h3>
                        </div>
                        <div class="search-main search-employee">
                            <input type="search" name="search" id="search" placeholder="Search...">
                            <img src="../images/search.svg" alt="">
                        </div>
                        <div class="filter-employee">
                            <img src="../images/filter.svg" alt="" class="filter">
                        </div>
                    </div>
                    <div id="employeehart"></div>
                </div>
                <div class="sales-record">
                    <div class="sales-resp">
                        <div>
                            <h4>Sales Record</h4>
                        </div>
                        <div>
                            <img src="../images/filter.svg" alt="" class="filter">
                        </div>
                    </div>
                    <div id="saleschart"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('css/charts.js') }}"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script> -->
    </body>

    </html
