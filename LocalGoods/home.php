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
    <v class="home-page">
        <div class="navbar-logo logo">
            <span class="p-1">L</span>
            <span class="p-2">G</span>
            <span class="logo-text">Local Goods</span>
        </div>
        <div class="shop-page">
            <nav class="navbar">
                <div class="left">
                    <a id="home">Home</a>
                    <a id="product">Products</a>
                    <a id="home">Help</a>
                    <a id="home">Contact</a>
                </div>
                <div class="right">
                    <div class="searchbar">
                        <input type="text" name="searchitem" placeholder="Search for Product">

                    </div>
                    <div class="icons">
                        <a href="#" class="cart-link"><i class="fas fa-box"></i></a>
                        <a href="profile.php" class="profile-link"><i class="fas fa-user"></i>
                        </a>
                    </div>
                </div>
            </nav>
            <div class="home-container">
                <div id="home-page" class="home-page-content">
                    <div class="home-title title">
                        <h1>Welcome to Home Page</h1>
                    </div>
                    <div class="contents">
                        <h1>Trending</h1>
                    </div>
                </div>

                <div id="product-page" class="product-page-content">
                    <div class="product-title title">
                        <h1>All Products</h1>
                    </div>
                    <div class="contents">
                        <h1>Product Categories</h1>
                        <div class="buttons">
                            <button class="categories">Consumer Electronics</button>
                            <button class="categories">Health & Beauty</button>
                            <button class="categories">Home & Garden</button>
                            <button class="categories">Accessories</button>
                            <button class="categories">Clothings</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
<script src="script.js"></script>
</html>