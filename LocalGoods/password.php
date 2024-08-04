<?php
include_once 'dbcon.php';
include_once 'changepassword.php';
session_start();
$email=$_POST["email"];
if ($stmt = $con->prepare("SELECT id FROM users WHERE email = ?")) {
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
 $email = $_POST['email'];
 $newPassword = $_POST['password'];

 $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

 $sql = "UPDATE users SET password = ? WHERE email = ?";

 $stmt = $con->prepare($sql);
 $stmt->bind_param("ss", $hashedPassword, $email);

 // Execute the update statement
 if ($stmt->execute()) {
     echo "<script>alert('Password updated successfully.');</script>";
     echo "<script>window.location.href='index.html';</script>";
 } else {
     echo "<script>alert('Failed to update password. Please try again later.');</script>";
     
 }

    }else{
        
    echo "<script>
    showPopup('User does not exist');
    </script>";
    }
 // Close statement and connection
 $stmt->close();
 $con->close();
}