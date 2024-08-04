<?php
session_start();

include_once 'dbcon.php';

$search = $_GET['search'] ?? '';
$search_query = '%' . $search . '%';

$price_min = $_GET['price_min'] ?? 0;
$price_max = $_GET['price_max'] ?? 1000;
$sort_by = $_GET['sort_by'] ?? 'product_name';
$order = $_GET['order'] ?? 'asc';

$sql = "SELECT * FROM products 
        WHERE (product_name LIKE ? OR product_tags LIKE ? OR product_category LIKE ?) 
        AND price BETWEEN ? AND ?";

if ($sort_by === 'name') {
    $sql .= " ORDER BY product_name " . $order;
} elseif ($sort_by === 'price') {
    $sql .= " ORDER BY price " . $order;
}

$stmt = $con->prepare($sql);

$stmt->bind_param("sssii", $search_query, $search_query, $search_query, $price_min, $price_max);

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
                    <a href="home.php?show=help" id="home">Help</a>
                    <a href="home.php?show=contact" id="home">Contact</a>
                </div>
                <div class="right">
                    <div class="searchbar">
                        <input type="text" id="searchInput" placeholder="Search for Product" value="<?php echo htmlspecialchars($search); ?>">
                        <button id="filterBtn">Filter</button>
                        <div id="suggestions" class="suggestions-container"></div>
                    </div>
                </div>
            </nav>
            <div id="filterSection" class="filter-section">
                <h3>Filter Products</h3>
                <form action="search_results.php" method="GET">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    
                    <label for="priceRange">Price Range:</label>
                    <div class="slider-container">
                        <input type="range" id="priceMin" name="price_min" min="0" max="1000" value="<?php echo htmlspecialchars($price_min); ?>">
                        <input type="range" id="priceMax" name="price_max" min="0" max="1000" value="<?php echo htmlspecialchars($price_max); ?>">
                        <div class="slider-values">
                            <span id="priceMinValue"><?php echo htmlspecialchars($price_min); ?></span> - <span id="priceMaxValue"><?php echo htmlspecialchars($price_max); ?></span>
                        </div>
                    </div>
                    
                    <label for="sortBy">Sort By:</label>
                    <select id="sortBy" name="sort_by">
                        <option value="name" <?php echo ($sort_by === 'name') ? 'selected' : ''; ?>>Name</option>
                        <option value="price" <?php echo ($sort_by === 'price') ? 'selected' : ''; ?>>Price</option>
                    </select>

                    <label for="order">Order:</label>
                    <div class="order-buttons">
                        <button type="submit" name="order" value="asc" class="<?php echo ($order === 'asc') ? 'active' : ''; ?>">Ascending</button>
                        <button type="submit" name="order" value="desc" class="<?php echo ($order === 'desc') ? 'active' : ''; ?>">Descending</button>
                    </div>
                    
                    <button type="submit">Apply Filters</button>
                </form>
                <button id="closeFilter">Close</button>
            </div>
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

                document.getElementById('filterBtn').addEventListener('click', function() {
                    document.getElementById('filterSection').classList.add('active');
                });

                document.getElementById('closeFilter').addEventListener('click', function() {
                    document.getElementById('filterSection').classList.remove('active');
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

                // Slider functionality
                const priceMin = document.getElementById('priceMin');
                const priceMax = document.getElementById('priceMax');
                const priceMinValue = document.getElementById('priceMinValue');
                const priceMaxValue = document.getElementById('priceMaxValue');

                priceMin.addEventListener('input', () => {
                    priceMinValue.textContent = priceMin.value;
                });

                priceMax.addEventListener('input', () => {
                    priceMaxValue.textContent = priceMax.value;
                });

                priceMinValue.textContent = priceMin.value;
                priceMaxValue.textContent = priceMax.value;
            </script>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>
