<?php
include_once 'dbcon.php';

$query = $_GET['q'] ?? '';

if ($query) {
    $stmt = $con->prepare("SELECT product_name FROM products WHERE product_name LIKE ? OR product_tags LIKE ? OR product_category LIKE ? LIMIT 20");
    $search_query = "%$query%";
    $stmt->bind_param("sss", $search_query, $search_query, $search_query);
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
