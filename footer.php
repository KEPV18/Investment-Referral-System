<br>
<br>
<br>
<br>
<footer>
    <nav class="navbar navbar-light bg-light navbar-expand fixed-bottom">
        <div class="container-fluid">
            <ul class="navbar-nav flex-row justify-content-between mx-auto">
                <li class="nav-item">
                    <a class="nav-link text-center" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i><br> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-center" href="investment_plans.php">
                        <i class="fas fa-chart-line"></i><br> Investment Plans
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-center" href="investment.php">
                        <i class="fas fa-wallet"></i><br> My Investments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-center" href="referrals.php">
                        <i class="fas fa-user-friends"></i><br> Team
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</footer>
<!-- Update Message -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
<script>
    function showNotification() {
    document.getElementById("updateNotification").style.display = "block";
}

function closeNotification() {
    document.getElementById("updateNotification").style.display = "none";
}

function contactSupport() {
    window.open('https://t.me/alibaba0pe', '_blank');
}

// Show notification box on page load
window.onload = function() {
    showNotification();
}

// Hide notification box when clicking anywhere on the page
window.onclick = function(event) {
    if (event.target.classList.contains('notification')) {
        closeNotification();
    }
}
</script>

</body>