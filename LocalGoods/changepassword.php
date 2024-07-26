<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Change Password page -->
    <div class="change-password-page">
        <div class="return">
            <a href="index.html" class="arrow">&larr;</a>
            <h1>Change Password</h1>
        </div>
        <div class="change-password-page-content">
            <form id="changePasswordForm" class="change-password-form" method="post" action="password.php">
                <input type="email" class="form-password-input" placeholder="Email" name="email" id="emailInput" required>
                <input type="password" class="form-password-input" placeholder="New Password" name="password" id="passwordInput" required>
                <div class="password-strength" id="passwordStrength"></div> 
                <input type="submit" class="form-password-btn" value="Confirm and Verify" id="submitBtn">
                <div class="loader" id="loader"></div> 
            </form>
        </div>
    </div>
    <div id="popup" class="popup">
        <div class="popup-content">
            <p id="popup-message"></p>
            <button class="btn" onclick="hidePopup()">Close</button>
        </div>
    </div>
</body>
<script>
    
function showPopup(message) {
    const Popup = document.getElementById("popup");
    const Message = document.getElementById("popup-message");

    Message.textContent = message;
    Popup.style.display = "flex";
}

function hidePopup() {
    document.getElementById("popup").style.display = "none";
}

document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('passwordInput');
    const submitBtn = document.getElementById('submitBtn');
    const passwordStrength = document.getElementById('passwordStrength');
    const loader = document.getElementById('loader');

    
    function validatePasswordStrength() {
        const password = passwordInput.value;

        const strongRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        const mediumRegex = /^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d]{8,}$/;


        if (strongRegex.test(password)) {
            passwordStrength.textContent = 'Strong';
            passwordStrength.className = 'password-strength strong';
            submitBtn.disabled = false; // Enable submit button
            return true;
        } else if (mediumRegex.test(password)) {
            passwordStrength.textContent = 'Medium';
            passwordStrength.className = 'password-strength medium';
            submitBtn.disabled = true; // Disable submit button
            return true;
        }
        else {
            passwordStrength.textContent = 'Weak';
            passwordStrength.className = 'password-strength weak';
            submitBtn.disabled = true; // Disable submit button
            return false;
        }
    }

    submitBtn.addEventListener('click', function (event) {
        if (!validatePasswordStrength()) {
            event.preventDefault(); 
            showPopup('Password must be at least 8 characters long and contain letters, numbers, and special characters.');
        }
    });

    
    // Check password strength on input change
    passwordInput.addEventListener('input', validatePasswordStrength);
});

</script>
</html>
