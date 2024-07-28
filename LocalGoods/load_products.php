<?php
session_start();
include_once 'dbcon.php';

if (isset($_POST['category'])) {
    $category = $_POST['category'];

    $stmt = $con->prepare("SELECT * FROM products WHERE product_category = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo '<div class="home-items card" data-id="'.htmlspecialchars($row['product_id']).'">
                <div class="image">
                    <img src="uploaded_img/'.htmlspecialchars($row['product_image']).'" alt="'.htmlspecialchars($row['product_name']).'">
                </div>
                <div class="caption">
                    <p class="rate">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </p>
                    <p class="product-name">'.htmlspecialchars($row['product_name']).'</p>
                    <p class="price"><b>₹ '.htmlspecialchars($row['price']).'</b></p>
                </div>
                <div class="details">
                    <button class="details-btn">Details</button>
                </div>
              </div>';
    }
    $stmt->close();
}
$con->close();
?>
