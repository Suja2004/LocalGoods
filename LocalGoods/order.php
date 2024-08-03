<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

$email = $_SESSION["email"];

include_once 'dbcon.php';

// Fetch user ID from email
$stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Initialize variables
$result = null;
$subtotal = 0;
$salesTaxRate = 0.001;
$grandTotal = 0;

// Fetch cart items for the user
if ($user_id) {
    $stmt = $con->prepare("
        SELECT cart.cart_id, cart.user_id, cart.product_id, products.product_name, products.price, cart.quantity
        FROM cart 
        JOIN users ON cart.user_id = users.id
        JOIN products ON cart.product_id = products.product_id
        WHERE cart.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total = $row['price'] * $row['quantity'];
        $subtotal += $total;
    }
    $salesTax = floor($subtotal * $salesTaxRate * 100 / 100);
    $grandTotal = $subtotal + $salesTax;
} else {
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Information</title>
    <link rel="stylesheet" href="home.css">
</head>

<body>
    <div class="home-container">
        <div class="delivery-page">
            <h1>Delivery Information</h1>
            <form action="process_order.php" method="post">
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="summary">
                    <p>Subtotal: ₹ <?php echo $subtotal; ?></p>
                    <p>Sales Tax: ₹ <?php echo $salesTax; ?></p>
                    <p>Grand Total: ₹ <?php echo $grandTotal; ?></p>
                </div>
                <button class="checkout" type="submit" name="place_order">Place Order</button>
            </form>
        </div>
    </div>
</body>
<script src="script.js"></script>

</html>
