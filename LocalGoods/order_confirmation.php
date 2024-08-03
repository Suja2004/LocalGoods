<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

include_once 'dbcon.php';

if (!isset($_GET['order_id'])) {
    header("Location: cart.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$stmt = $con->prepare("
    SELECT o.address, o.phone, o.subtotal, o.sales_tax, o.grand_total
    FROM orders o
    WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_details = $stmt->get_result()->fetch_assoc();
$stmt->close();

unset($_GET['order_id']); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="home.css">
</head>

<body>
    <div class="home-container">
        <div class="confirmation-page">
            <h1>Order Confirmation</h1>
            <p>Thank you for your order!</p>
            <p>Your order ID is: <?php echo htmlspecialchars($order_id); ?></p>
            <h2>Order Details</h2>
            <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order_details['address']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($order_details['phone']); ?></p>
            <p><strong>Subtotal:</strong> ₹ <?php echo htmlspecialchars($order_details['subtotal']); ?></p>
            <p><strong>Sales Tax:</strong> ₹ <?php echo htmlspecialchars($order_details['sales_tax']); ?></p>
            <p><strong>Grand Total:</strong> ₹ <?php echo htmlspecialchars($order_details['grand_total']); ?></p>
        </div>
    <button class="btn"><a href="home.php">Home</a></button>

    </div>
</body>
<script src="script.js"></script>

</html>
