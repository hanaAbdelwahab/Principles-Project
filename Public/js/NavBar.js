// Public/js/navbar.js
document.addEventListener('DOMContentLoaded', function() {
    // Get all navigation links
    const navLinks = document.querySelectorAll('nav a');
    
    // Handle navigation clicks
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // If it's an internal link (not starting with http)
            if (!this.href.startsWith('http') || this.href.includes(window.location.host)) {
                e.preventDefault();
                const path = this.getAttribute('href');
                
                // For Car Catalogue link, ensure it goes to the right place
                if (path.includes('CodeListing.php')) {
                    window.location.href = '/PrincipleProject/App/view/CodeListing.php';
                } else {
                    window.location.href = path;
                }
            }
            // External links will behave normally
        });
    });

    // Toggle dropdown menu when clicking on user icon
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