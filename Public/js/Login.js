//Login.js
document.addEventListener("DOMContentLoaded", () => {
    // Remove the automatic adding of "active" class to let the page
    // start with the login form showing first
    // const container = document.getElementById("container");
    // container.classList.add("active"); // This was causing the issue
});

const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const signUpForm = document.getElementById('sign-up-form');
const signInForm = document.getElementById('sign-in-form');
const modal = document.getElementById('modal');
const modalMessage = document.getElementById('modal-message');
const closeButton = document.querySelector('.close-button');
const forgotPasswordLink = document.getElementById('forgot-password');

// New buttons in the navbar
const navSignInBtn = document.querySelector('.signin-btn');
const navSignUpBtn = document.querySelector('.signup-btn');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

// Add event listeners for the navbar buttons
navSignInBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

navSignUpBtn.addEventListener('click', () => {
    container.classList.add("active");
});

signUpForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const username = document.getElementById("signup-username").value;
        const email = document.getElementById("signup-email").value;
        const password = document.getElementById("signup-password").value;
        const confirmPassword = document.getElementById("signup-confirm-password").value;
        const birthdate = document.getElementById("birthdate-input").value;
        
        // Check if birthdate is provided
        if (!birthdate) {
            showModal("Please enter your date of birth");
            return;
        }
        
        // Calculate age from birthdate
        const today = new Date();
        const birthdateDate = new Date(birthdate);
        let age = today.getFullYear() - birthdateDate.getFullYear();
        const monthDiff = today.getMonth() - birthdateDate.getMonth();
        
        // Adjust age if birthday hasn't occurred yet this year
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdateDate.getDate())) {
            age--;
        }
        
        // Check if user is at least 18 years old
        if (age < 18) {
            showModal("You must be at least 18 years old to create an account");
            return;
        }
        
        const formattedDate = moment(birthdate).format('DD/MM/YYYY');
        
        if (
            username === "" ||
            email === "" ||
            password === "" ||
            confirmPassword === "" ||
            formattedDate === ""
        ) {
            showModal("All fields are required");
        } else if (!validateEmail(email)) {
            showModal("Please enter a valid email");
        } else if (password !== confirmPassword) {
            showModal("Passwords do not match");
        } else {
            signup(username, email, password, formattedDate);
        }
    });

function signup(username, email, password, formattedDate) {
    const formType = "Signup";

    $.ajax({
        url: "/login-signup",
        type: "POST",
        data: {
            username_inp: username,
            pass_inp: password,
            email_inp: email,
            birthdate_inp: formattedDate,
            form_type_inp: formType
        },
        success: function (response) {
            if (response.success) {
                window.location.href = '/'; 
            } else {
                showModal("Signup failed");
            }
        },
        error: function (xhr, status, error) {
            showModal("Signup Failed");
        },
    });
}

signInForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const email = document.getElementById("login-email").value;
    const password = document.getElementById("login-password").value;
    
    if (email === "" || password === "") {
        showModal("All fields are required");
    } else if (!validateEmail(email)) {
        showModal("Please enter a valid email");
    } else {
        login(email, password);
    }
});

function login(email, password) {
    const formType = "Login";
    $.ajax({
        url: "/login-signup",
        type: "POST",
        data: {
            email_inp: email,
            pass_inp: password,
            form_type_inp: formType
        },
        success: function (response) {
            if (response.success) {
                if (response.user.userType === "Admin"){
                    window.location.href = '/dashboard';
                } else{
                    console.log(response.user.id);
                    window.location.href = '/'; 
                }
            } else {
                showModal("Wrong password");
            }
        },
        error: function (xhr, status, error) {
            console.log("Error details:", xhr.responseText);
            showModal("Login failed: " + xhr.responseText);
        },
    });
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function showModal(message) {
    modalMessage.textContent = message;
    modal.style.display = "block";
}

closeButton.addEventListener("click", () => {
    modal.style.display = "none";
});

window.addEventListener("click", (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});

forgotPasswordLink.addEventListener("mouseenter", () => {
    forgotPasswordLink.style.color = "#f19f13";
});

forgotPasswordLink.addEventListener("mouseleave", () => {
    forgotPasswordLink.style.color = "#333"; // Reset color on mouse leave
});

forgotPasswordLink.addEventListener("mousedown", (event) => {
    event.preventDefault();
    showModalWithDropdown();
});

function showModalWithDropdown() {
    const forgotPasswordModal = document.getElementById(
        "forgot-password-modal"
    );
    forgotPasswordModal.style.display = "block";

    const closeButton = forgotPasswordModal.querySelector(".close-button");
    closeButton.addEventListener("click", () => {
        forgotPasswordModal.style.display = "none";
    });

    const confirmColorButton = document.getElementById("confirm-color");
    confirmColorButton.addEventListener("click", () => {
        const chosenColor = document.getElementById("chosen-color").value;
        const favoriteColorLabel = document.getElementById(
            "favorite-color-label"
        );
        if (favoriteColorLabel) {
            favoriteColorLabel.textContent = chosenColor;
        }
        forgotPasswordModal.style.display = "none";
    });
}

// Add an event listener to the confirm button
document.getElementById("confirm-button").addEventListener("click", () => {
    closeModal();
});

// Function to close the modal
function closeModal() {
    modal.style.display = "none";
}
 // Add event listener for role selection to show/hide car license
document.addEventListener('DOMContentLoaded', function() {
    // Handle file selection display for driver's license
    const driverLicenseInput = document.getElementById('driver-license');
    const driverLicenseName = document.getElementById('driver-license-name');
    
    driverLicenseInput.addEventListener('change', function() {
        if(this.files.length > 0) {
            driverLicenseName.textContent = this.files[0].name;
        } else {
            driverLicenseName.textContent = 'No file selected';
        }
    });
    
    // Handle file selection display for national ID
    const nationalIdInput = document.getElementById('national-id');
    const nationalIdName = document.getElementById('national-id-name');
    
    nationalIdInput.addEventListener('change', function() {
        if(this.files.length > 0) {
            nationalIdName.textContent = this.files[0].name;
        } else {
            nationalIdName.textContent = 'No file selected';
        }
    });
    
    // Handle role selection to show/hide car license
    const userRoleSelect = document.getElementById('user-role');
    const carLicenseContainer = document.getElementById('car-license-container');
    
    userRoleSelect.addEventListener('change', function() {
        if(this.value === 'owner') {
            carLicenseContainer.style.display = 'block';
        } else {
            carLicenseContainer.style.display = 'none';
        }
    });
    
    // Handle file selection display for car license
    const carLicenseInput = document.getElementById('car-license');
    const carLicenseName = document.getElementById('car-license-name');
    
    carLicenseInput.addEventListener('change', function() {
        if(this.files.length > 0) {
            carLicenseName.textContent = this.files[0].name;
        } else {
            carLicenseName.textContent = 'No file selected';
        }
    });
});
