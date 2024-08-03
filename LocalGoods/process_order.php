<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

include_once 'dbcon.php';

$email = $_SESSION["email"];

// Fetch user ID from email
$stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect delivery information
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // Initialize variables
    $subtotal = 0;
    $salesTaxRate = 0.001;
    $grandTotal = 0;

    // Fetch cart items for the user
    $stmt = $con->prepare("
        SELECT cart.cart_id, cart.product_id, products.price, cart.quantity
        FROM cart 
        JOIN products ON cart.product_id = products.product_id
        WHERE cart.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_items = $stmt->get_result();

    // Calculate subtotal, sales tax, and grand total
    while ($row = $cart_items->fetch_assoc()) {
        $total = $row['price'] * $row['quantity'];
        $subtotal += $total;
    }
    $salesTax = floor($subtotal * $salesTaxRate * 100 / 100);
    $grandTotal = $subtotal + $salesTax;

    // Insert order into database
    $stmt = $con->prepare("
        INSERT INTO orders (user_id, address, phone, subtotal, sales_tax, grand_total) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issddd", $user_id, $address, $phone, $subtotal, $salesTax, $grandTotal);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get the ID of the inserted order
    $stmt->close();

    // Insert order items into database
    $stmt = $con->prepare("
    INSERT INTO order_items (order_id, product_id, quantity, price)
    SELECT ?, c.product_id, c.quantity, p.price
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Clear cart after order is placed
    $stmt = $con->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['order_id']); 

    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit();
} else {
    header("Location: cart.php");
    exit();
}
?>
