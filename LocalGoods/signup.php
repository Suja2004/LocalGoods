<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="signup-page">
        <div class="signup-page-content">
            <form action="signup.php" class="signup-page-form" method="POST">
                <input type="text" class="form-signup-input" name="username" placeholder="User Name" required>
                <input type="email" class="form-signup-input" name="email" placeholder="Email" required>
                <input type="password" class="form-signup-input" name="password" id="SpasswordInput" placeholder="Password" required>
                <div class="password-strength" id="passwordStrength"></div>
                <input type="submit" class="signup-btn" value="Sign Up" id="signup_Btn">
                <span>or</span>
            </form>
            <button class="form-signup-btn signup" onclick="location.href='index.html';">Log In</button>
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
    
function showPopup(message, redirectUrl = null) {
    const Popup = document.getElementById("popup");
    const Message = document.getElementById("popup-message");

    Message.textContent = message;
    Popup.style.display = "flex";
    
    if (redirectUrl) {
        setTimeout(function() {
            window.location.href = redirectUrl;
        }, 1500); 
    }
}

function hidePopup() {
    document.getElementById("popup").style.display = "none";
}


document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('SpasswordInput');
    const signupBtn = document.getElementById('signup_Btn');
    const passwordStrength = document.getElementById('passwordStrength');
    
    function validatePasswordStrength() {
        const password = passwordInput.value;

        const strongRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&.]{8,}$/;
        const mediumRegex = /^(?=.*[a-zA-Z])(?=.*\d)[A-Za-z\d.]{8,}$/;


        if (strongRegex.test(password)) {
            passwordStrength.textContent = 'Strong';
            passwordStrength.className = 'password-strength strong';
            signupBtn.disabled = false; // Enable submit button
            return true;
        }else if (mediumRegex.test(password)) {
            passwordStrength.textContent = 'Medium';
            passwordStrength.className = 'password-strength medium';
            signupBtn.disabled = true; // Disable submit button
            return true;
        }
         else {
            passwordStrength.textContent = 'Weak';
            passwordStrength.className = 'password-strength weak';
            signupBtn.disabled = true; // Disable submit button
            return false;
        }
    }

    signupBtn.addEventListener('click', function(event) {
        if (!validatePasswordStrength()) {
            event.preventDefault(); 
            showPopup('Password must be at least 8 characters long and contain letters, numbers, and special characters.');
            passwordStrength.textContent = '';
        }
    });


    passwordInput.addEventListener('input', validatePasswordStrength);
});
</script>
</html>
<?php
session_start();
include_once 'dbcon.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>showPopup('Invalid email format.');</script>";
        exit();
    }

    // Check if email is already registered
    if ($stmt = $con->prepare("SELECT id FROM users WHERE email = ?")) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>showPopup('Email is already registered.');</script>";
            exit();
        }
        $stmt->close();
    }

    // Check if username is already taken
    if ($stmt = $con->prepare("SELECT id FROM users WHERE username = ?")) {
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>showPopup('Name is already taken. Please choose another name.');</script>";
            exit();
        }
        $stmt->close();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if ($stmt = $con->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)")) {
        

        $stmt->bind_param('sss', $name, $email,  $hashed_password);

        if ($stmt->execute()) {
            unset($_SESSION['temp_user']);
            echo "<script>alert('User registered successfully.');</script>";
            echo "<script>window.location.href = 'index.html'; </script>";
            exit();
        } else {
            echo "<script>alert('Failed to register user: " . $stmt->error . "');</script>";
            echo "<script>window.location.href = 'signup.php'; </script>";
            exit();
        }
    } else {
        echo "<script>alert('Failed to prepare the SQL statement.');</script>";
        echo "<script>window.location.href = 'signup.php'; </script>";
        exit();
    }

}

$con->close();
?>
