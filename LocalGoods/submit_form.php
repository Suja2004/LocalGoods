<?php
include_once 'dbcon.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }

    if (!empty($errors)) {
        // Display errors
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
    } else {
        // Prepare and bind
        $stmt = $con->prepare("INSERT INTO contact_form (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        // Execute the query
        if ($stmt->execute()) {
            echo '<div class="message-container">';
            echo '<p class="thank-you-message">Thank you for your message. We will get back to you soon.</p>';
            echo '<p class="redirect-message">You will be redirected shortly...</p>';
            echo '</div>';
            echo '<script>
        setTimeout(function() {
            window.location.href = "home.php";
        }, 3000); // Redirect after 3 seconds
    </script>';
        } else {
            echo '<div class="message-container">';

            echo '<p class="thank-you-message">
Sorry, something went wrong. Please try again.';
            echo '<p class="redirect-message">You will be redirected shortly...</p>';
            echo '</div>';
            echo '<script>
        setTimeout(function() {
            window.location.href = "home.php";
        }, 3000); // Redirect after 3 seconds
    </script>';
        }

        $stmt->close();
    }
} else {
    header("Location: home.php");
    exit;
}

$con->close();
?>

<style>
    .message-container {
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        max-width: 600px;
        margin: 50px auto;
        text-align: center;
    }

    .thank-you-message {
        font-size: 1.2em;
        color: #4CAF50;
        margin-bottom: 20px;
    }

    .redirect-message {
        font-size: 1em;
        color: #555;/
    }

    .error-message {
        font-size: 1.2em;
        color: #f44336;
        padding: 20px;
        background-color: #fbe9e7;
        border: 1px solid #f44336;
        border-radius: 5px;
        max-width: 600px;
        margin: 50px auto;
    }
</style>