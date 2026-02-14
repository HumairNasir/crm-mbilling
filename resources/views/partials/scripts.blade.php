<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    /* SIDEBAR NAVIGATION LOGIC */
    $(document).ready(function() {
        let open = false;
        $(".burger-menu-button").click(function() {
            if(open){
                $(".sidebar").animate({ left: "-100%" }, 500);
                open = false;
            } else {
                $(".sidebar").animate({ left: "0%" }, 500);
                $(".sidebar-overlay").animate({ right: "0%" }, 500);
                open = true;
            }
        });

        $(".sidebar-overlay, .overlay-close").click(function() {
            $(".sidebar").animate({ left: "-100%" }, 500);
            $(".sidebar-overlay").animate({ right: "-100%" }, 500);
            open = false;
        });
    });
</script>

<script>
    /* GLOBAL LOGIC ONLY 
       Do NOT put dashboard chart logic here. 
       Dashboard logic belongs in dashboard.blade.php 
    */
    $(document).ready(function() {
        console.log("✅ Base Layout Scripts Loaded");
    });
</script>