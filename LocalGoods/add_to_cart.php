<?php
include_once 'dbcon.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $email = $_SESSION['email'];
    $product_id = $_POST['product_id'];

    // Fetch user ID from email
    $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            // Check if product is already in the cart
            $stmt = $con->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
            if ($stmt) {
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Product already in cart, update the quantity
                    $cart_item = $result->fetch_assoc();
                    $new_quantity = $cart_item['quantity'] + 1;

                    $stmt = $con->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                    if ($stmt) {
                        $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
                        $stmt->execute();
                        $stmt->close();
                        echo "Product quantity updated in cart";
                    } else {
                        echo "Error preparing statement: " . $con->error;
                    }
                } else {
                    $stmt = $con->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
                    if ($stmt) {
                        $stmt->bind_param("ii", $user_id, $product_id);
                        $stmt->execute();
                        $stmt->close();
                        echo "Product added to cart";
                    } else {
                        echo "Error preparing statement: " . $con->error;
                    }
                }
                $stmt->close();
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
