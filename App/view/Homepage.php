
<?php
// Start the session to enable access to session variables
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$username = $isLoggedIn ? $_SESSION['user_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarRent - Rent the Best Car Around the World</title>
    <base href="/PrincipleProject/">
    <link rel="stylesheet" href="Public/css/Homepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>
    <header>
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <a href="#"><img src="Public/images/logo.jpg" alt="CarHub Logo"></a>
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
                                <li><a href="#"><i class="fas fa-user-circle"></i> My Profile</a></li>
                                <li><a href="#"><i class="fas fa-car"></i> My Rentals</a></li>
                                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                                <li class="logout"><a href="Logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- User is not logged in - show sign in/up buttons -->
                <div class="auth-buttons">
                    <button class="sign-in" onclick="window.location.href='Login.php'">Sign In</button>
                    <button class="sign-up" onclick="window.location.href='Login.php'">Sign Up</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <span class="subtitle">Car rental experience</span>
                    <h1>Rent the Best Car Around the World</h1>
                    <p>We provide the best car options, and expert service for the greatest customer experience.</p>
                </div>
                <div class="hero-image">
                    <img src="Public/images/carr.png" alt="Mercedes Benz Car">
                </div>
            </div>
        </div>
        <div class="hero-shape"></div>
    </section>

    <section class="features">
        <div class="container">
            <h2>Why choose CarRent?</h2>
            
            <div class="feature-cards">
                <div class="feature-card">
                    <div class="feature-icon booking">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#c039fb" width="48" height="48">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                    </div>
                    <h3>Fast & Easy Booking</h3>
                    <p>Skip the hassle and get on the road quicker. With our user-friendly platform, you can book your car in just a few clicks—no hidden fees, no complicated forms, just a smooth, straightforward process from start to finish.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon location">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ff9933" width="48" height="48">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </div>
                    <h3>Many Pickup Location</h3>
                    <p>Wherever you are, we’re nearby. With a wide network of convenient pickup points across the city and beyond, CarRent makes it easy to start your journey from a location that works best for you.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon customers">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#4cd964" width="48" height="48">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <h3>Satisfied Customers</h3>
                    <p>We put our customers first, and it shows. From excellent service to reliable vehicles, thousands of happy renters have trusted CarRent—and keep coming back for the experience they can count on.</p>
                </div>
            </div>
        </div>
    </section>


    <section class="car-booking">
    <div class="container">
        <div class="booking-header">
            <h2>Book your suitable car</h2>
            <div class="filter-options">
                <div class="filter-option">
                    <input type="text" placeholder="Price">
                </div>
                <div class="filter-option">
                    <input type="text" placeholder="Brand">
                </div>
                <div class="filter-option">
                    <input type="text" placeholder="Type">
                </div>
            </div>
        </div>
        
        <div class="car-grid">
            <!-- Car Card 1 -->
            <div class="car-card">
                <h3>Hyundai Elantra</h3>
                <div class="car-image">
                    <img src="Public/images/hyundai.png" alt="Mercedes-Benz C Red">
                </div>
                <div class="car-price">$210/Day</div>
                <div class="car-rating">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star">★</span>
                </div>
                <div class="car-features">
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>4 Seats</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>GPS</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M7 2v11h3v9l7-12h-4l4-8z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>Turbo</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>Automatic</span>
                    </div>
                </div>
            </div>
            
            <!-- Car Card 2 -->
            <div class="car-card">
                <h3>jeep Range Rover</h3>
                <div class="car-image">
                    <img src="Public/images/jeep.png" alt="Mercedes-Benz C Blue">
                </div>
                <div class="car-price">$240/Day</div>
                <div class="car-rating">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                </div>
                <div class="car-features">
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>4 Seats</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>GPS</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M7 2v11h3v9l7-12h-4l4-8z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>Turbo</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>Automatic</span>
                    </div>
                </div>
            </div>
            
            <!-- Car Card 3 -->
            <div class="car-card">
                <h3>Kia tasman</h3>
                <div class="car-image">
                    <img src="Public/images/kia.png" alt="Mercedes-Benz C Yellow">
                </div>
                <div class="car-price">$280/Day</div>
                <div class="car-rating">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                </div>
                <div class="car-features">
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>4 Seats</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>GPS</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M7 2v11h3v9l7-12h-4l4-8z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>Turbo</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>Automatic</span>
                    </div>
                </div>
            </div>
            
            <!-- Car Card 4 -->
            <div class="car-card">
                <h3>Tesla Model X</h3>
                <div class="car-image">
                    <img src="Public/images/tesla.png" alt="Mercedes-Benz C Blue Hatchback">
                </div>
                <div class="car-price">$250/Day</div>
                <div class="car-rating">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star">★</span>
                </div>
                <div class="car-features">
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>4 Seats</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>GPS</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M7 2v11h3v9l7-12h-4l4-8z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>Turbo</span>
                    </div>
                    <div class="feature">
                        <div class="feature-icon-small">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="#fff"/>
                            </svg>
                        </div>
                        <span>Automatic</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="rent-button-container">
            <button class="rent-button">Rent a Car</button>
        </div>
    </div>
</section>
<!-- Improved HTML for "How It Works" section -->
<section class="how-it-works">
    <div class="container">
        <div class="section-header">
            <h2>How it works</h2>
        </div>
        
        <div class="timeline">
            <!-- Timeline path with car icons will be added by JavaScript -->
            <div class="timeline-path"></div>
            
            <div class="timeline-steps">
                <!-- Step 1 -->
                <div class="timeline-step">
                    <div class="step-circle"></div>
                    <div class="step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M17 10.5V7c0-.55-.45-1-1-1H8c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h8c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4zM14 13h-3v3H9v-3H6v-2h3V8h2v3h3v2z" fill="#ffcc00"/>
                        </svg>
                    </div>
                    <div class="step-content">
                        <h3>Open</h3>
                        <p>the CarRent Website</p>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="timeline-step">
                    <div class="step-circle"></div>
                    <div class="step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#ffcc00"/>
                        </svg>
                    </div>
                    <div class="step-content">
                        <h3>Select where</h3>
                        <p>you want to pickup your car</p>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="timeline-step">
                    <div class="step-circle"></div>
                    <div class="step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" fill="#ffcc00"/>
                        </svg>
                    </div>
                    <div class="step-content">
                        <h3>Submit all the</h3>
                        <p>required documents</p>
                    </div>
                </div>
                
                <!-- Step 4 -->
                <div class="timeline-step">
                    <div class="step-circle"></div>
                    <div class="step-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M19.44 9.03L15.41 5H11v2h3.59l2 2H5c-2.8 0-5 2.2-5 5s2.2 5 5 5c2.46 0 4.45-1.69 4.9-4h1.65l2.77-2.77c-.21.54-.32 1.14-.32 1.77 0 2.8 2.2 5 5 5s5-2.2 5-5c0-2.65-1.97-4.77-4.56-4.97zM7.82 15C7.4 16.15 6.28 17 5 17c-1.63 0-3-1.37-3-3s1.37-3 3-3c1.28 0 2.4.85 2.82 2H5v2h2.82zM19 17c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z" fill="#ffcc00"/>
                        </svg>
                    </div>
                    <div class="step-content">
                        <h3>When approved, you</h3>
                        <p>may start driving</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Customer Testimonials Section -->
<section class="testimonials">
    <div class="container">
        <div class="testimonials-header">
            <h2>What Customers are saying</h2>
        </div>
        
        <div class="testimonials-grid">
            <!-- Testimonial 1 - Small -->
            <div class="testimonial-card small">
                <div class="rating">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                </div>
                <p class="testimonial-text">“I booked a car through carRent for a weekend getaway, and the entire process was incredibly smooth. The site was easy to navigate, and the car was ready on time and in perfect condition. I’ll definitely be using carRent again!”</p>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <img src="Public/images/userrr.png" alt="Kathy West">
                    </div>
                    <span class="author-name">Kathy West</span>
                </div>
            </div>
            
            <!-- Testimonial 2 - Medium -->
            <div class="testimonial-card medium">
                <div class="rating">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                </div>
                <p class="testimonial-text">“I was honestly surprised by how smooth my experience with carRent was. Booking took less than five minutes, and I got exactly the type of car I needed at a price that beat the bigger rental companies. The car was clean, fueled, and ready to go when I arrived.

What really impressed me was how quick the pickup and return process was—no long lines or paperwork headaches. Everything was straightforward, and the staff was friendly and professional. I’ll definitely be using carRent again for my next trip!”</p>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <img src="Public/images/userrr.png" alt="Tommy Ward">
                    </div>
                    <span class="author-name">Tommy Ward</span>
                </div>
            </div>
            
            <!-- Testimonial 3 - Large -->
            <div class="testimonial-card large">
                <div class="rating">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                </div>
                <p class="testimonial-text">“When my flight got delayed, I thought I’d lose my reservation, but carRent’s support team was super helpful and adjusted everything for me. Great customer service like this is rare these days.”</p>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <img src="Public/images/userrr.png" alt="Stephen Baker">
                    </div>
                    <span class="author-name">Stephen Baker</span>
                </div>
            </div>
            
            <!-- Testimonial 4 - Medium -->
            <div class="testimonial-card medium">
                <div class="rating">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                </div>
                <p class="testimonial-text">“I needed a car last-minute for a business trip, and carRent came through. The pickup and drop-off process was fast and efficient, and the car ran perfectly. I’ll be a repeat customer for sure.”</p>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <img src="Public/images/userrr.png" alt="Angelina Keith">
                    </div>
                    <span class="author-name">Angelina Keith</span>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Footer Section -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <!-- Company Info -->
            <div class="footer-column company-info">
                <div class="footer-logo">
                    <a href="#">CarRent</a>
                </div>
                <p class="company-description">
                    Your premium car rental service. Find the perfect vehicle for any occasion, anywhere in the world.
                </p>
                <div class="social-icons">
                    <a href="#" class="social-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z" fill="#fff"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" fill="#fff"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" fill="#fff"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Car Catalogue</a></li>
                    <li><a href="#">Locations</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Special Offers</a></li>
                </ul>
            </div>
            
            <!-- Customer Support -->
            <div class="footer-column">
                <h3>Support</h3>
                <ul class="footer-links">
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Booking Process</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                </ul>
            </div>
            
            <!-- Newsletter -->
            <div class="footer-column newsletter">
                <h3>Newsletter</h3>
                <p>Subscribe to our newsletter for special offers and updates</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email">
                    <button type="submit">Subscribe</button>
                </form>
                <div class="app-badges">
                    <a href="#" class="app-badge">
                        <img src="Public/images/app-store.png" alt="App Store">
                    </a>
                    <a href="#" class="app-badge">
                        <img src="Public/images/app.png" alt="Google Play">
                    </a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="copyright">
                <p>&copy; 2025 CarRent. All rights reserved.</p>
            </div>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
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
    <script src="Public/js/Homepage.js"></script>
</body>
</html>