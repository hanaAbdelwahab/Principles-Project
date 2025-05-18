<?php
// Start the session for user authentication
session_start();
// Check if there's a message from logout
$message = null;
$message_type = null;
// Check if the user is already logged in
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    
    // Clear the message from session
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}


// Include the UserController
require_once '../Controller/UserController.php';
$userController = new UserController();

// Initialize variables
$message = null;
$message_type = null; // 'success' or 'error'

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login-submit'])) {
    $email = $_POST['login-email'];
    $password = $_POST['login-password'];
    
    // Process login
    $result = $userController->login($email, $password);
    
    if ($result['success']) {
        // Redirect to homepage on successful login
        header("Location: " . $result['redirect']);
        exit();
    } else {
        $message = $result['error'];
        $message_type = 'error';
    }
}

// Handle signup form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup-submit'])) {
    // Prepare user data
    $userData = [
        'username' => $_POST['signup-username'],
        'email' => $_POST['signup-email'],
        'password' => $_POST['signup-password'],
        'confirm_password' => $_POST['signup-confirm-password'],
        'birthdate' => $_POST['birthdate-input'],
        'favorite_color' => $_POST['chosen-color'] ?? null
    ];
    
    // Prepare file data
    $fileData = [
        'driver_license' => $_FILES['driver-license'],
        'national_id' => $_FILES['national-id']
    ];
    
    // Process registration
    $result = $userController->register($userData, $fileData);
    
    if ($result['success']) {
        $message = "Account created successfully! Please log in.";
        $message_type = 'success';
    } else {
        $message = implode("<br>", $result['errors']);
        $message_type = 'error';
    }
}

// Handle forgot password request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forgot-submit'])) {
    $email = $_POST['forgot-email'];
    $color = $_POST['chosen-color'];
    
    $result = $userController->forgotPassword($email, $color);
    
    if ($result['success']) {
        $message = $result['message'];
        $message_type = 'success';
    } else {
        $message = $result['error'];
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>

        <base href="/PrincipleProject/">
        <link rel="stylesheet" href="Public/css/Login.css" />

        <title>Login/Signup Page</title>
        <link rel="icon" href="/images/logo1.png" type="image/x-icon" />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap"
            rel="stylesheet"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap"
            rel="stylesheet"
        />
        
    </head>

    <body>
    <!-- Navigation Bar - Placed OUTSIDE the login container -->
    <header>
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <a href="#">CarRent</a>
                </div>
                <nav>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Car Catalogue</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Help</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <button class="signin-btn">Sign in</button>
                    <button class="signup-btn">Sign Up</button>
                </div>
            </div>
        </div>
    </header>

    <!-- Login/Signup Container -->
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form id="sign-up-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <h1>Create Account</h1>
                <div class="form-inputs">
                    <input
                        type="text"
                        id="signup-username"
                        name="signup-username"
                        placeholder="Name"
                        value="<?php echo isset($_POST['signup-username']) ? htmlspecialchars($_POST['signup-username']) : ''; ?>"
                    />
                    <input 
                        type="email" 
                        id="signup-email" 
                        name="signup-email" 
                        placeholder="Email"
                        value="<?php echo isset($_POST['signup-email']) ? htmlspecialchars($_POST['signup-email']) : ''; ?>"
                    />
                    <input
                        type="password"
                        id="signup-password"
                        name="signup-password"
                        placeholder="Password"
                    />
                    <input
                        type="password"
                        id="signup-confirm-password"
                        name="signup-confirm-password"
                        placeholder="Confirm Password"
                    /><input
    type="date"
    id="birthdate-input"
    name="birthdate-input"
    max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>"
    value="<?php echo isset($_POST['birthdate-input']) ? htmlspecialchars($_POST['birthdate-input']) : ''; ?>"
/>
                    
                    <!-- Add favorite color selector for password recovery -->
                    <select id="chosen-color" name="chosen-color">
                        <option value="" disabled selected>
                            Select your favorite color 
                        </option>
                        <option value="White">White</option>
                        <option value="Black">Black</option>
                        <option value="Brown">Brown</option>
                        <option value="Grey">Grey</option>
                        <option value="Red">Red</option>
                        <option value="Pink">Pink</option>
                        <option value="Violet">Violet</option>
                        <option value="Blue">Blue</option>
                        <option value="Green">Green</option>
                        <option value="Orange">Orange</option>
                        <option value="Yellow">Yellow</option>
                    </select>
                    
                    <!-- File upload fields -->
                    <div class="file-upload-container">
                        <label for="driver-license" class="file-label">
                            <i class="fas fa-id-card"></i> Driver's License
                            <input type="file" id="driver-license" name="driver-license" accept="image/*,.pdf" />
                            <span class="file-selected" id="driver-license-name">No file selected</span>
                        </label>
                    </div>
                    
                    <div class="file-upload-container">
                        <label for="national-id" class="file-label">
                            <i class="fas fa-passport"></i> National ID
                            <input type="file" id="national-id" name="national-id" accept="image/*,.pdf" />
                            <span class="file-selected" id="national-id-name">No file selected</span>
                        </label>
                    </div>

                </div>
                <button type="submit" name="signup-submit">Sign Up</button>  
            </form>
        </div>
        
        <div class="form-container sign-in">
            <form id="sign-in-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h1>Login</h1>
                
                <input 
                    type="email" 
                    id="login-email" 
                    name="login-email" 
                    placeholder="Email"
                    value="<?php echo isset($_POST['login-email']) ? htmlspecialchars($_POST['login-email']) : ''; ?>"
                />
                <input
                    type="password"
                    id="login-password"
                    name="login-password"
                    placeholder="Password"
                />
                <a href="#" id="forgot-password">Forget Your Password?</a>
                <button type="submit" name="login-submit">Login</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>
                        Enter your personal details to use all of the site
                        features
                    </p>
                    <button class="hidden" id="login">Login</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Don't have an account?</h1>
                    <p>Register to use all of CarRent features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal for displaying messages -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <p id="modal-message"></p>
            <button id="confirm-button">OK</button>
        </div>
    </div>
    
    <!-- Forgot password modal with improved form -->
    <div id="forgot-password-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3>Reset Password</h3>
            <form id="forgot-password-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="email" name="forgot-email" placeholder="Enter your email" required>
                <p>Enter the color you chose while signing up:</p>
                <select id="forgotten-color" name="chosen-color" required>
                    <option value="" disabled selected>Select your favorite color</option>
                    <option value="White">White</option>
                    <option value="Black">Black</option>
                    <option value="Brown">Brown</option>
                    <option value="Grey">Grey</option>
                    <option value="Red">Red</option>
                    <option value="Pink">Pink</option>
                    <option value="Violet">Violet</option>
                    <option value="Blue">Blue</option>
                    <option value="Green">Green</option>
                    <option value="Orange">Orange</option>
                    <option value="Yellow">Yellow</option>
                </select>
                <button type="submit" name="forgot-submit" id="confirm-color">Reset Password</button>
            </form>
        </div>
    </div>

    <script>
        // Preserve the JavaScript functionality
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById("container");
            const registerBtn = document.getElementById("register");
            const loginBtn = document.getElementById("login");
            const signupBtn = document.querySelector(".signup-btn");
            const signinBtn = document.querySelector(".signin-btn");

            registerBtn.addEventListener("click", () => {
                container.classList.add("active");
            });

            loginBtn.addEventListener("click", () => {
                container.classList.remove("active");
            });
            
            signupBtn.addEventListener("click", () => {
                container.classList.add("active");
            });
            
            signinBtn.addEventListener("click", () => {
                container.classList.remove("active");
            });

            // File upload handling
            document.getElementById('driver-license').addEventListener('change', function() {
                document.getElementById('driver-license-name').textContent = this.files[0] ? this.files[0].name : 'No file selected';
            });
            
            document.getElementById('national-id').addEventListener('change', function() {
                document.getElementById('national-id-name').textContent = this.files[0] ? this.files[0].name : 'No file selected';
            });
            
            // Modal handling
            const modal = document.getElementById("modal");
            const modalMessage = document.getElementById("modal-message");
            const confirmButton = document.getElementById("confirm-button");
            const closeButtons = document.querySelectorAll(".close-button");
            
            // Check if there's a message to display
            <?php if ($message): ?>
                modalMessage.innerHTML = "<?php echo addslashes($message); ?>";
                modalMessage.className = "<?php echo $message_type; ?>";
                modal.style.display = "block";
            <?php endif; ?>
            
            // Forgot password modal
            const forgotPasswordLink = document.getElementById("forgot-password");
            const forgotPasswordModal = document.getElementById("forgot-password-modal");
            
            forgotPasswordLink.addEventListener("click", function(e) {
                e.preventDefault();
                forgotPasswordModal.style.display = "block";
            });
            
            closeButtons.forEach(button => {
                button.addEventListener("click", function() {
                    modal.style.display = "none";
                    forgotPasswordModal.style.display = "none";
                });
            });
            
            confirmButton.addEventListener("click", function() {
                modal.style.display = "none";
            });
            
            window.addEventListener("click", function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
                if (event.target == forgotPasswordModal) {
                    forgotPasswordModal.style.display = "none";
                }
            });
        });
    </script>
    
    <style>
        /* Add some styling for success/error message in modal */
        #modal-message.success {
            color: #28a745;
            border-left: 4px solid #28a745;
            padding-left: 15px;
        }
        
        #modal-message.error {
            color: #dc3545;
            border-left: 4px solid #dc3545;
            padding-left: 15px;
        }
        
        /* Modal styling improvements */
        .modal-content {
            border-radius: 8px;
            padding: 25px;
            max-width: 400px;
            width: 100%;
        }
        
        #confirm-button {
            background-color: #512da8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            font-weight: bold;
        }
        
        #confirm-button:hover {
            background-color: #4527a0;
        }
    </style>
</body>
</html>