<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}


$email = $_SESSION["email"];

include_once 'dbcon.php';


$sql = "SELECT * FROM products";
$all_product = $con->query($sql);


$stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$result = null;

if ($user_id) {
    $stmt = $con->prepare("
        SELECT cart.cart_id, cart.user_id, cart.product_id, products.product_name,products.price, cart.quantity
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
                    <a href="home.php" id="home">Home</a>
                    <a href="#" id="product">Products</a>
                    <a href="#" id="home">Help</a>
                    <a href="#" id="home">Contact</a>
                </div>
                <div class="right">
                    <div class="searchbar">
                    <input type="text" id="searchInput" placeholder="Search for Product">
                    <div id="suggestions" class="suggestions-container"></div>
                    </div>
                    <div class="icons">
                        <a href="cart.php" class="cart-link" ><i class="fas fa-box"></i></a>
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
            <div class="home-container">
                <div id="home-page" class="home-page-content">
                    <div class="home-title title">
                        <h1>Welcome to Home Page</h1>
                    </div>
                    <div class="contents">
                        <h1>All Products</h1>
                    </div>
                    <div class="contentload">
                        <?php
                        while ($row = mysqli_fetch_assoc($all_product)) {
                        ?>
                            <div class="home-items card" data-id="<?php echo htmlspecialchars($row['product_id']); ?>">
                                <div class="image">
                                    <img src="uploaded_img/<?php echo htmlspecialchars($row['product_image']); ?>" alt="">
                                </div>
                                <div class="caption">
                                    <p class="rate">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </p>
                                    <p class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></p>
                                    <p class="price"><b>₹ <?php echo htmlspecialchars($row['price']); ?></b></p>
                                </div>
                                <div class="details">
                                    <button class="details-btn">Details</button>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>

                <div id="product-page" class="product-page-content">
                    <div class="contents">
                        <h1>Product Categories</h1>
                        <div class="buttons">
                            <button class="categories" data-category="Consumer Electronics">Consumer Electronics</button>
                            <button class="categories" data-category="Health & Beauty">Health & Beauty</button>
                            <button class="categories" data-category="Home & Garden">Home & Garden</button>
                            <button class="categories" data-category="Accessories">Accessories</button>
                            <button class="categories" data-category="Clothings">Clothings</button>
                        </div>
                    </div>
                    <div class="contentload">
                        <?php

                        $cat = 'Consumer Electronics';
                        $stmt = $con->prepare("SELECT * FROM products WHERE product_category = ?");
                        $stmt->bind_param("s", $cat);
                        $stmt->execute();
                        $product = $stmt->get_result();

                        while ($row = $product->fetch_assoc()) {
                        ?>
                            <div class="home-items card" data-id="<?php echo htmlspecialchars($row['product_id']); ?>">
                                <div class="image">
                                    <img src="uploaded_img/<?php echo htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                </div>
                                <div class="caption">
                                    <p class="rate">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </p>
                                    <p class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></p>
                                    <p class="price"><b>₹ <?php echo htmlspecialchars($row['price']); ?></b></p>
                                </div>
                                <div class="details">
                                    <button class="details-btn">Details</button>
                                </div>
                            </div>
                        <?php
                        }
                        $stmt->close();
                        $con->close();
                        ?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.categories').on('click', function() {
                var category = $(this).data('category');
                $.ajax({
                    url: 'load_products.php',
                    type: 'POST',
                    data: {
                        category: category
                    },
                    success: function(response) {
                        $('.contentload').html(response);
                        $('.details-btn').on('click', function() {
                            var productId = $(this).closest('.card').data('id');
                            window.location.href = 'product_details.php?id=' + productId;
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + status + error);
                    }
                });
            });
        });

        document.querySelectorAll('.details-btn').forEach(item => {
            item.addEventListener('click', () => {
                const productId = item.closest('.home-items').getAttribute('data-id');
                window.location.href = `product_details.php?id=${productId}`;
            });
        });

        function fetchProductDetails(productId) {
            $.ajax({
                url: 'product_details.php',
                type: 'POST',
                data: {
                    id: productId
                },
                success: function(response) {
                    $('.product-details').html(response);
                }
            });
        }
        
        document.getElementById('searchInput').addEventListener('input', function() {
    let query = this.value;
    if (query.length > 1) { 
        fetchSuggestions(query);
    } else {
        document.getElementById('suggestions').innerHTML = '';
    }
});

document.getElementById('searchInput').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        window.location.href = 'search_results.php?search=' + encodeURIComponent(this.value);
    }
});

function fetchSuggestions(query) {
    fetch('search_suggestions.php?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            let suggestions = document.getElementById('suggestions');
            suggestions.innerHTML = '';
            data.forEach(item => {
                let div = document.createElement('div');
                div.textContent = item.product_name;
                div.addEventListener('click', () => {
                    window.location.href = 'search_results.php?search=' + encodeURIComponent(item.product_name);
                });
                suggestions.appendChild(div);
            });
        });
}


    </script>
</body>
<script src="script.js"></script>

</html>