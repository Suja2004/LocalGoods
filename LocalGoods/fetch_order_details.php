<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo 'You need to log in to view orders.';
    exit();
}

include_once 'dbcon.php';

$email = $_SESSION['email'];

// Fetch user ID
$stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Fetch orders with their items
$stmt = $con->prepare("
    SELECT orders.id, orders.order_date, order_items.product_id, products.product_name, order_items.quantity
    FROM orders
    JOIN order_items ON orders.id = order_items.order_id
    JOIN products ON order_items.product_id = products.product_id
    WHERE orders.user_id = ?
    ORDER BY orders.order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

if ($orders->num_rows > 0) {
    echo '<table>';
    echo '<thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
          </thead>';
    echo '<tbody>';
    while ($row = $orders->fetch_assoc()) {
        echo '<tr>
                <td>' . htmlspecialchars($row['id']) . '</td>
                <td>' . htmlspecialchars($row['order_date']) . '</td>
                <td>' . htmlspecialchars($row['product_name']) . '</td>
                <td>' . htmlspecialchars($row['quantity']) . '</td>
                <td><button class="cancel-btn" onclick="cancelOrder(' . htmlspecialchars($row['id']) . ')">Cancel Order</button></td>
              </tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>Place an Order</p>';
}
