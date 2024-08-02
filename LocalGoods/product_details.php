<?php
include_once 'dbcon.php';


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Goods</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="home.css">

</head>

<body>
    <div class="home-page">
        <div class="navbar-logo logo">
            <span class="p-1">L</span>
            <span class="p-2">G</span>
            <span class="logo-text">Local Goods</span>
        </div>
        <div class="shop-page">
            <nav class="navbar">
                <div class="left">
                    <a href="home.php">Home</a>
                    <a href="home.php?show=products">Products</a>
                    <a href="#" id="home">Help</a>
                    <a href="#" id="home">Contact</a>
                </div>
                <div class="right">
                    <div class="searchbar">
                        <input type="text" name="searchitem" placeholder="Search for Product">

                    </div>
                    <div class="icons">
                        <a href="cart.php" class="cart-link">
                            <i class="fas fa-box"></i></a>
                        <a href="#" class="profile-link" onclick="toggleMenu()"><i class="fas fa-user"></i>
                        </a>
                    </div>
                </div>

            </nav>
            <div class="sub-menu-container " id="subMenu">
                <div class="sub-menu">
                    <a href="#" class="sub-menu-link">
                        <p>Account</p>
                        <span>></span>
                    </a>
                    <a href="#" class="sub-menu-link">
                        <p>My Orders</p>
                        <span>></span>
                    </a>
                    <a href="changepassword.php" class="sub-menu-link">
                        <p>Change Password</p>
                        <span>></span>
                    </a>
                    <a href="#" class="sub-menu-link">
                        <p>Settings</p>
                        <span>></span>
                    </a>
                    <a href="#" class="sub-menu-link">
                        <p>Logout</p>
                        <span>></span>
                    </a>
                </div>
            </div>
            <div class="container">
                <?php
                if (isset($_GET['id'])) {
                    $product_id = $_GET['id'];

                    $stmt = $con->prepare("SELECT * FROM products WHERE product_id = ?");
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $product = $result->fetch_assoc();

                    if ($product) {
                ?>
                        <div class="product-details">
                            <div class="image-container">
                                <img src="uploaded_img/<?php echo htmlspecialchars($product['product_image']); ?>" alt="">
                            </div>
                            <div class="product-info">
                                <div class="product-name">
                                    <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                                </div>
                                <p class="product-price">₹ <?php echo htmlspecialchars($product['price']); ?></p>
                                <p class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </p>
                                <div class="product-buttons">
                                    <button onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
                                </div>
                                <div class="product-category">
                                    <p><strong>Category:</strong> <?php echo htmlspecialchars($product['product_category']); ?></p>
                                    <p><strong>Tags:</strong> <?php echo htmlspecialchars($product['product_tags']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="desc">
                            <h3>Description</h3>
                            <p class="description"><?php echo htmlspecialchars($product['product_desc']); ?></p>
                        </div>

                <?php
                    }
                }
                ?>
            </div>

        </div>
    </div>
    <script>
        function addToCart(productId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "add_to_cart.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert("Product added to cart successfully!");
                }
            };
            xhr.send("product_id=" + productId);
        }
    </script>

</body>
<script src="script.js"></script>

</html>