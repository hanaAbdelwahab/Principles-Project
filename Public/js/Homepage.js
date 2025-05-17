// JavaScript for CarRent Homepage

document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the target section from the href attribute
            const targetId = this.getAttribute('href');
            
            // Only scroll if it's an internal link
            if(targetId.startsWith('#')) {
                const targetSection = document.querySelector(targetId);
                
                if(targetSection) {
                    targetSection.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    // Feature card hover effect enhancement
    const featureCards = document.querySelectorAll('.feature-card');
    
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });
    
    // Auth buttons click handlers
    const signInBtn = document.querySelector('.sign-in');
    const signUpBtn = document.querySelector('.sign-up');
    
    if(signInBtn) {
        signInBtn.addEventListener('click', function() {
            console.log('Sign In button clicked');
            // Add sign in functionality here
        });
    }
    
    if(signUpBtn) {
        signUpBtn.addEventListener('click', function() {
            console.log('Sign Up button clicked');
            // Add sign up functionality here
        });
    }
    
    // Optional: Add responsive menu toggle for mobile
    function setupMobileMenu() {
        const navElement = document.querySelector('nav');
        const mobileBreakpoint = 768;
        
        // Check if we're on mobile
        function checkMobile() {
            if (window.innerWidth <= mobileBreakpoint) {
                if(!document.querySelector('.mobile-toggle')) {
                    createMobileToggle();
                }
                navElement.classList.add('mobile-nav');
            } else {
                navElement.classList.remove('mobile-nav');
                const toggle = document.querySelector('.mobile-toggle');
                if(toggle) {
                    toggle.remove();
                }
                navElement.style.display = '';
            }
        }
        
        // Create mobile menu toggle button
        function createMobileToggle() {
            const toggle = document.createElement('button');
            toggle.classList.add('mobile-toggle');
            toggle.innerHTML = '☰';
            toggle.style.cssText = 'background: none; border: none; font-size: 24px; cursor: pointer;';
            
            toggle.addEventListener('click', function() {
                if(navElement.style.display === 'block') {
                    navElement.style.display = 'none';
                } else {
                    navElement.style.display = 'block';
                }
            });
            
            document.querySelector('.logo').after(toggle);
            navElement.style.display = 'none';
        }
        
        // Initialize and add resize listener
        checkMobile();
        window.addEventListener('resize', checkMobile);
    }
    
    // Uncomment to enable mobile menu
    // setupMobileMenu();
// Car booking section interactions
const carCards = document.querySelectorAll('.car-card');
const rentButton = document.querySelector('.rent-button');
const filterInputs = document.querySelectorAll('.filter-option input');

// Car card hover effects
carCards.forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.3)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.boxShadow = 'none';
    });
    
    // Add click event to select car
    card.addEventListener('click', function() {
        // Remove active class from all cards
        carCards.forEach(c => c.classList.remove('active-card'));
        
        // Add active class to clicked card
        this.classList.add('active-card');
        
        // Optional: Add styling for active card
    });
});

// Rent button click handler
if(rentButton) {
    rentButton.addEventListener('click', function() {
        const activeCard = document.querySelector('.car-card.active-card');
        
        if(activeCard) {
            const carModel = activeCard.querySelector('h3').textContent;
            console.log(`Renting ${carModel}`);
            // Add rental process functionality here
            
            // Example: Show confirmation message
            alert(`Thank you for renting ${carModel}! Our team will contact you shortly.`);
        } else {
            alert('Please select a car first!');
        }
    });
}

// Filter inputs handlers
filterInputs.forEach(input => {
    input.addEventListener('input', function() {
        console.log(`Filtering by ${this.placeholder}: ${this.value}`);
        // Add filtering functionality here
        
        // Example implementation could filter the visible cars
        // based on the input values
    });
});
});
// Improved JavaScript for "How It Works" section
document.addEventListener('DOMContentLoaded', function() {
    // Function to add car icons to the timeline
    function addCarIcons() {
        const timelinePath = document.querySelector('.timeline-path');
        if (!timelinePath) return;
        
        // Create two car icons
        for (let i = 0; i < 2; i++) {
            const carIcon = document.createElement('div');
            carIcon.className = 'car-icon';
            
            // Add car SVG - simplified version for better rendering
            carIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30">
                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z" fill="#ffcc00"/>
                </svg>
            `;
            
            timelinePath.appendChild(carIcon);
        }
    }
    
    // Simple animation for timeline steps
    function setupTimelineSteps() {
        const timelineSteps = document.querySelectorAll('.timeline-step');
        if (!timelineSteps.length) return;
        
        // Add animation classes to timeline steps
        timelineSteps.forEach((step, index) => {
            // Start with steps invisible
            step.style.opacity = '0';
            step.style.transform = 'translateY(20px)';
            step.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            // Add delay based on index
            step.style.transitionDelay = `${index * 0.2}s`;
        });
        
        // Use Intersection Observer to trigger animations when section is visible
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                timelineSteps.forEach(step => {
                    step.style.opacity = '1';
                    step.style.transform = 'translateY(0)';
                });
                // Stop observing once animation is triggered
                observer.unobserve(entries[0].target);
            }
        }, { threshold: 0.2 });
        
        // Start observing the timeline section
        const timelineSection = document.querySelector('.how-it-works');
        if (timelineSection) {
            observer.observe(timelineSection);
        }
    }
    
    // Check if we're on mobile and adjust if needed
    function checkMobileLayout() {
        const isMobile = window.innerWidth <= 992;
        const carIcons = document.querySelectorAll('.car-icon');
        
        carIcons.forEach(car => {
            if (isMobile) {
                // Rotate car for vertical movement
                car.querySelector('svg').style.transform = 'rotate(90deg)';
            } else {
                car.querySelector('svg').style.transform = 'none';
            }
        });
    }
    
    // Initialize everything
    function initHowItWorks() {
        // Add car icons to timeline
        addCarIcons();
        
        // Setup timeline steps animation
        setupTimelineSteps();
        
        // Check if mobile and adjust
        checkMobileLayout();
        
        // Listen for window resize to adjust mobile layout
        window.addEventListener('resize', checkMobileLayout);
    }
    
    // Call initialization function
    initHowItWorks();
});
// JavaScript for Testimonials Section

document.addEventListener('DOMContentLoaded', function() {
    // Animate testimonial cards when they enter the viewport
    function setupTestimonialAnimations() {
        const testimonialCards = document.querySelectorAll('.testimonial-card');
        
        if (!testimonialCards.length) return;
        
        // Initially set cards to be invisible
        testimonialCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            
            // Add slight delay between each card animation
            card.style.transitionDelay = `${index * 0.15}s`;
        });
        
        // Use Intersection Observer to detect when testimonials section is visible
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                // Animate all cards when section becomes visible
                testimonialCards.forEach(card => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
                
                // Stop observing once animation is triggered
                observer.unobserve(entries[0].target);
            }
        }, { threshold: 0.2 });
        
        // Start observing the testimonials section
        const testimonialsSection = document.querySelector('.testimonials');
        if (testimonialsSection) {
            observer.observe(testimonialsSection);
        }
    }
    
    // Add hover effects to testimonial cards
    function setupTestimonialHoverEffects() {
        const testimonialCards = document.querySelectorAll('.testimonial-card');
        
        testimonialCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 15px 25px rgba(0, 0, 0, 0.4)';
                this.style.borderColor = 'var(--primary-color)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.boxShadow = '0 10px 20px rgba(0, 0, 0, 0.3)';
                this.style.borderColor = '#333';
            });
        });
    }
    
    // Initialize all testimonial section functionality
    function initTestimonials() {
        setupTestimonialAnimations();
        setupTestimonialHoverEffects();
    }
    
    // Call the initialization function
    initTestimonials();
});
// Footer functionality
document.addEventListener('DOMContentLoaded', function() {
    // Newsletter form submission
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            if (validateEmail(email)) {
                // Here you would typically send the email to your server
                // For demo purposes, just show a success message
                showSubscriptionMessage('Thank you for subscribing!', 'success');
                emailInput.value = '';
            } else {
                showSubscriptionMessage('Please enter a valid email address', 'error');
            }
        });
    }
    
    // Social media links hover animation
    const socialIcons = document.querySelectorAll('.social-icon');
    socialIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            const svg = this.querySelector('svg');
            svg.style.transform = 'scale(1.2)';
            svg.style.transition = 'transform 0.3s';
        });
        
        icon.addEventListener('mouseleave', function() {
            const svg = this.querySelector('svg');
            svg.style.transform = 'scale(1)';
        });
    });
    
    // Footer links hover effect - show car icon
    const footerLinks = document.querySelectorAll('.footer-links a');
    footerLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.color = 'var(--primary-color)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.color = '';
        });
    });
});

// Helper function to validate email format
function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

// Helper function to show subscription message
function showSubscriptionMessage(message, type) {
    // Check if message element already exists and remove it
    const oldMessage = document.querySelector('.subscription-message');
    if (oldMessage) {
        oldMessage.remove();
    }
    
    // Create new message element
    const messageEl = document.createElement('div');
    messageEl.classList.add('subscription-message', type);
    messageEl.textContent = message;
    
    // Add to the DOM
    const newsletterForm = document.querySelector('.newsletter-form');
    newsletterForm.after(messageEl);
    
    // Add CSS to the message
    messageEl.style.marginTop = '10px';
    messageEl.style.padding = '8px 12px';
    messageEl.style.borderRadius = '4px';
    messageEl.style.fontSize = '14px';
    
    if (type === 'success') {
        messageEl.style.backgroundColor = 'rgba(76, 217, 100, 0.1)';
        messageEl.style.color = '#4cd964';
    } else {
        messageEl.style.backgroundColor = 'rgba(255, 59, 48, 0.1)';
        messageEl.style.color = '#ff3b30';
    }
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        messageEl.style.opacity = '0';
        messageEl.style.transition = 'opacity 0.5s';
        
        setTimeout(() => {
            messageEl.remove();
        }, 500);
    }, 3000);
}

// Current year for copyright
const currentYear = document.querySelector('.copyright p');
if (currentYear) {
    currentYear.innerHTML = currentYear.innerHTML.replace('2025', new Date().getFullYear());
}