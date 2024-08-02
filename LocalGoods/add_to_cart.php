<?php
include_once 'dbcon.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $email = $_SESSION['email']; 
    $product_id = $_POST['product_id'];

    $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            $stmt = $con->prepare("
                INSERT INTO cart (user_id, product_id, quantity)
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE
                quantity = quantity + 1
            ");
            if ($stmt) {
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
                $stmt->close();
                echo "Product added to cart";
            } else {
                echo "Error preparing statement: " . $con->error;
            }
        } else {
            echo "User not found";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error;
    }
}
?>
