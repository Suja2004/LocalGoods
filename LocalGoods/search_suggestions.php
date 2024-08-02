<?php
include_once 'dbcon.php';

$query = $_GET['q'] ?? '';

if ($query) {
    $stmt = $con->prepare("SELECT product_name FROM products WHERE product_name LIKE ? LIMIT 10");
    $search_query = "%$query%";
    $stmt->bind_param("s", $search_query);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode($suggestions);
}
?>
