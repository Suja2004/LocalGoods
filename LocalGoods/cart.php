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

$result = null;
$subtotal = 0;
$salesTaxRate = 0.001;
$grandTotal = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $cart_id = $_POST['update'];
        $quantity = $_POST['quantity'][$cart_id];

        $stmt = $con->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['delete'])) {
        $cart_id = $_POST['delete'];

        $stmt = $con->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['checkout'])) {
        header("Location: order.php");
        exit();
    }
}

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="home.css">
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this item?");
        }
    </script>
</head>

<body>
    <div class="home-container">
        <div class="cart-page" id="cart">
            <div class="cartTop">
                <button class="back" onclick="goBack()" aria-label="Go back">&lt;</button>
                <h1>Your Cart</h1>
            </div>

            <?php if ($result && $result->num_rows > 0) : ?>
                <form action="cart.php" method="post">
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                $total = $row['price'] * $row['quantity'];
                                $subtotal += $total;
                            ?>
                                <tr>
                                    <td>
                                        <p><?php echo htmlspecialchars($row['product_name']); ?></p>
                                    </td>
                                    <td>₹ <?php echo htmlspecialchars($row['price']); ?></td>
                                    <td>
                                        <input type="number" value="<?php echo htmlspecialchars($row['quantity']); ?>" name="quantity[<?php echo $row['cart_id']; ?>]" min="1">
                                    </td>
                                    <td>₹ <?php echo htmlspecialchars($total); ?></td>
                                    <td>
                                        <div class="actions">
                                            <button class="acbtn green" type="submit" name="update" value="<?php echo $row['cart_id']; ?>">Update</button>
                                            <button class="acbtn red" type="submit" name="delete" value="<?php echo $row['cart_id']; ?>" onclick="return confirmDelete()">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                    <?php
                    $salesTax = floor($subtotal * $salesTaxRate * 100 / 100);
                    $grandTotal = $subtotal + $salesTax;
                    ?>

                    <div class="summary">
                        <p>Subtotal: ₹ <?php echo $subtotal; ?></p>
                        <p>Sales Tax: ₹ <?php echo $salesTax; ?></p>
                        <p>Grand Total: ₹ <?php echo $grandTotal; ?></p>
                        <button class="checkout" type="submit" name="checkout">Check out</button>
                    </div>
                </form>
            <?php else : ?>
                <h2>Add Items to Cart</h2>
            <?php endif; ?>
        </div>
    </div>


    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
<script src="script.js"></script>

</html>