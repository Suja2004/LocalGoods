<?php
session_start();

include_once 'dbcon.php';

// Get search query from URL
$search = $_GET['search'] ?? '';

// Sanitize input
$search_query = '%' . $search . '%';

// Fetch products based on search query
$stmt = $con->prepare("SELECT * FROM products WHERE product_name LIKE ?");
$stmt->bind_param("s", $search_query);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
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
                        <input type="text" id="searchInput" placeholder="Search for Product" value="<?php echo htmlspecialchars($search); ?>">
                        <div id="suggestions" class="suggestions-container"></div>
                    </div>
                    <div class="icons">
                        <a href="#" class="cart-link"><i class="fas fa-box"></i></a>
                        <a href="#" class="profile-link" onclick="toggleMenu()"><i class="fas fa-user"></i></a>
                    </div>
                </div>
            </nav>
            <div class="search-container">
                <?php if ($result->num_rows > 0) : ?>
                    <div class="search-results">
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <div class="product-card" data-id="<?php echo htmlspecialchars($row['product_id']); ?>">
                                <div class="product-image">
                                    <img src="uploaded_img/<?php echo htmlspecialchars($row['product_image']); ?>" alt="">
                                </div>
                                <div class="product-caption">
                                    <p class="product-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </p>
                                    <p class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></p>
                                    <p class="product-price"><b>₹ <?php echo htmlspecialchars($row['price']); ?></b></p>
                                </div>
                                <div class="sbtn details">
                                    <button><a href="product_details.php?id=<?php echo $row['product_id']; ?>" class="details-link">Details</a></button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <h2>No products found for "<?php echo htmlspecialchars($search); ?>"</h2>
                <?php endif; ?>
            </div>

            <script>
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