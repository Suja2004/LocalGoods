<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_POST['order_id'])) {
    echo 'Invalid request.';
    exit();
}

include_once 'dbcon.php';

$order_id = intval($_POST['order_id']);
$email = $_SESSION['email'];

$stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$con->begin_transaction();

try {
    $stmt = $con->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $con->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $stmt->close();

    $con->commit();
    echo 'Order cancelled successfully.';
    echo "   <script>
        window.location.reload();
     </script>";
} catch (Exception $e) {
    $con->rollback();
    echo 'Error cancelling order: ' . $e->getMessage();
}

$con->close();
