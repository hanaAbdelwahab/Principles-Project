<!-- Includes/NavBar.php-->
<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['user_name'] : '';
?>
    <base href="/PrincipleProject/">
     <link rel="stylesheet" href="Public/css/NavBar.css">
<header>
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <a href="#"><img src="Public/images/Logooo.png" alt="CarHub Logo"></a>
            </div>
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Car Catalogue</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Help</a></li>
                </ul>
            </nav>
            
            <?php if($isLoggedIn): ?>
            <!-- User is logged in - show profile icon with dropdown -->
            <div class="auth-buttons">
                <span class="welcome-message">Welcome, <?php echo htmlspecialchars($username); ?></span>
                <div class="user-profile">
                    <div class="user-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="dropdown-menu">
                        <ul>
                            <li class="user-info"><?php echo htmlspecialchars($username); ?></li>
                            <li><a href="Profile.php"><i class="fas fa-user-circle"></i> My Profile</a></li>
                            <li class="logout"><a href="index.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- User is not logged in - show sign in/up buttons -->
            <div class="auth-buttons">
                <button class="sign-in" onclick="window.location.href='App/view/Login.php'">Sign In</button>
                <button class="sign-up" onclick="window.location.href='App/view/Login.php'">Sign Up</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    // Toggle dropdown menu when clicking on user icon
    document.addEventListener('DOMContentLoaded', function() {
        const userProfile = document.querySelector('.user-profile');
        const dropdownMenu = document.querySelector('.dropdown-menu');
        
        if (userProfile) {
            userProfile.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('active');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                if (dropdownMenu && dropdownMenu.classList.contains('active')) {
                    dropdownMenu.classList.remove('active');
                }
            });
            
            // Prevent closing when clicking inside dropdown
            if (dropdownMenu) {
                dropdownMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        }
    });
</script>