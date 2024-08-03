<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.html");
    exit();
}

$email = $_SESSION["email"];

include_once 'dbcon.php';

$user_id = $username = $user_email = $full_name = $phone_number = $date_of_birth = $address = '';

$query = "
    SELECT 
        u.id,
        u.username, 
        u.email AS user_email, 
        ud.full_name, 
        ud.phone_number, 
        ud.date_of_birth, 
        ud.address
    FROM 
        users u
    LEFT JOIN 
        userdata ud
    ON 
        u.id = ud.user_id
    WHERE 
        u.email = ?
";

if ($stmt = $con->prepare($query)) {
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $username, $user_email, $full_name, $phone_number, $date_of_birth, $address);
    $stmt->fetch();
    $stmt->close();
} else {
    echo '<script>showPopup("Failed to fetch user data.")</script>';
    exit;
}

$full_name = $full_name ?? '';
$phone_number = $phone_number ?? '';
$date_of_birth = $date_of_birth ?? '';
$address = $address ?? '';

$sql = "SELECT * FROM products";
$all_product = $con->query($sql);

$stmt = $con->prepare("SELECT id,username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $user_name);
$stmt->fetch();
$stmt->close();

$result = null;

if ($user_id) {
    $stmt = $con->prepare("
        SELECT cart.cart_id, cart.user_id, cart.product_id, products.product_name, products.price, cart.quantity
        FROM cart 
        JOIN users ON cart.user_id = users.id
        JOIN products ON cart.product_id = products.product_id
        WHERE cart.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}

$stmt = $con->prepare("
    SELECT id, address, phone, subtotal, sales_tax, grand_total, order_date
    FROM orders
    WHERE user_id = ?
    ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();

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
    <link rel="stylesheet" href="order.css">
</head>

<body>
    <div class="home-page">
        <div class="navbar-logo logo"  >
            <span class="p-1">L</span>
            <span class="p-2">G</span>
            <span  class="logo-text" onclick="window.history.back()">Local Goods</span>
        </div>
        <div class="shop-page">
            <nav class="navbar">
                <div class="left">
                    <a href="home.php" id="home">Home</a>
                    <a href="#" id="product">Products</a>
                    <a href="#" id="help">Help</a>
                    <a href="#" id="contact">Contact</a>
                </div>
                <div class="right">
                    <div class="searchbar">
                        <input type="text" id="searchInput" placeholder="Search for Product">
                        <div id="suggestions" class="suggestions-container"></div>
                    </div>
                    <div class="icons">
                        <a href="cart.php" class="cart-link"><i class="fas fa-box"></i></a>
                        <a href="#" onclick="toggleMenu()"><i class="fas fa-user"></i></a>
                    </div>
                </div>
            </nav>
            <div class="sub-menu-container" id="subMenu">
                <div class="sub-menu">
                    <a href="#profile-page" id="showProfile" class="sub-menu-link profile-link">
                        <p>Account</p>
                        <span>></span>
                    </a>
                    <a href="#" id="toggle-orders" class="sub-menu-link">
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
                    <a href="#" onclick="showLogoutPopup()" class="sub-menu-link">
                        <p>Logout</p>
                        <span>></span>
                    </a>
                </div>
            </div>
            <div class="home-container">
                <div id="home-page" class="home-page-content">
                    <div class="home-title title">
                    <h1>Welcome <?php echo htmlspecialchars($user_name); ?></h1>
                    </div>
                    <div class="contents">
                        <h1>All Products</h1>
                    </div>
                    <div class="contentload">
                        <?php while ($row = mysqli_fetch_assoc($all_product)) { ?>
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
                        <?php } ?>
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

                        while ($row = $product->fetch_assoc()) { ?>
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
                        <?php }
                        $stmt->close();
                        $con->close();
                        ?>
                    </div>
                </div>


                <div id="profile-page" class="profile-page">
                    <div class="profile-page-content">
                        <div class="profile-top">
                            <h2>Welcome to Your Profile Page</h2>
                        </div>
                        <div class="profile">
                            <form class="update-profile-form" id="profile-form" action="update_profile.php" method="POST">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

                                <div class="form-group noedit">
                                    <label for="username">Username:</label>
                                    <input id="username" type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="full_name">Full Name:</label>
                                    <input id="full_name" type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" readonly required>
                                </div>
                                <div class="form-group noedit">
                                    <label for="email">Email:</label>
                                    <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="phone_number">Phone Number:</label>
                                    <input id="phone_number" type="text" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth:</label>
                                    <input id="date_of_birth" type="date" name="date_of_birth" value="<?php echo htmlspecialchars($date_of_birth); ?>" readonly required>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address:</label>
                                    <textarea id="address" name="address" readonly required><?php echo htmlspecialchars($address); ?></textarea>
                                </div>

                                <div class="edit-btns">
                                    <input id="save-button" class="btn btn-primary" type="submit" value="Save Profile" style="display: none;">
                                    <button id="cancel-button" class="btn btn-danger" type="button" onclick="cancelUpdate()" style="display: none;">Cancel</button>
                                </div>
                            </form>

                            <div id="account" class="account">
                                <button class="btn btn-primary" onclick="showLogoutPopup()">Log Out</button>
                                <button id="edit-button" class="btn btn-primary" onclick="enableEditing()">Update Profile</button>
                                <button class="btn btn-danger" onclick="showDeletePopup()">Delete Account</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="orders-section" class="sliding-section">
                    <div class="sliding-content">
                        <button class="close-btn" id="close-orders">&times;</button>
                        <h2>Your Orders</h2>
                        <div id="orders-list">

                        </div>
                    </div>
                </div>
                <div id="help-page" class="help">

                    <header>
                        <h1>Help Center</h1>
                        <nav>
                            <ul>
                                <li><a href="#faqs">FAQs</a></li>
                                <li><a href="#contact">Contact Us</a></li>
                                <li><a href="#guides">Guides & Tutorials</a></li>
                                <li><a href="#policies">Policies</a></li>
                            </ul>
                        </nav>
                    </header>

                    <main>
                        <section id="faqs">
                            <h2>Frequently Asked Questions (FAQs)</h2>
                            <div class="faq">
                                <h3>Order Issues</h3>
                                <p class="faq-content">To check your order, log in to your account and go to "My Orders". </p>
                            </div>
                            <div class="faq">
                                <h3>Password Issues</h3>
                                <p class="faq-content">To change your password, click the forgot password option at login page</p>
                            </div>

                        </section>

                        <section id="contact">
                            <h2>Contact Us</h2>
                            <p><strong>Customer Support:</strong> Reach out to us at <a href="mailto:support@example.com">support@example.com</a> or call us at 1-800-123-4567.</p>
                            <p><strong>Support Hours:</strong> Monday to Friday, 9 AM - 6 PM (EST).</p>
                            <p><strong>Feedback:</strong> We value your feedback. Share your thoughts <a href="#" id="gocontact">here</a>.</p>
                        </section>

                        <section id="guides">
                            <h2>Guides and Tutorials</h2>
                            <ul>
                                <li><a href="#">Placing an Order</a></li>
                                <li><a href="#">Using Discount Codes</a></li>
                                <li><a href="#">Canceling Your Order</a></li>
                            </ul>
                        </section>

                        <section id="policies">
                            <h2>Policies and Legal Information</h2>
                            <ul>
                                <li><a href="#">Privacy Policy</a></li>
                                <li><a href="#">Terms and Conditions</a></li>
                                <li><a href="#">Cookie Policy</a></li>
                            </ul>
                        </section>


                    </main>

                    <footer>
                        <p>&copy; 2024 Your Company. All rights reserved.</p>
                    </footer>
                </div>
                <div class="contact" id="contact-page">
                    <section id="contact">
                        <h2>Contact Us</h2>
                        <form action="submit_form.php" method="post" class="contact-form">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input id="full_name" type="text" name="name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly required>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button class="contactbtn" type="submit">Send Message</button>
                        </form>
                    </section>
                </div>

            </div>
        </div>
        <!-- Settings -->
        <div class="settings" id="setting">
            
        </div>
        <!-- Popups -->
        <div id="logout-popup" class="popup">
            <div class="popup-content">
                <p>Are you sure you want to logout?</p>
                <form class="logout-form" action="logout.php" method="post">
                    <div class="popbtns">
                        <button class="pbtn" type="submit">Logout</button>
                        <button class="pbtn" type="button" onclick="hideLogoutPopup()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="delete-popup" class="popup">
            <div class="popup-content">
                <p>Are you sure you want to delete your account?</p>
                <form class="delete-form" action="deleteaccount.php" method="post">
                    <button class="btn btn-danger" type="submit">Delete</button>
                    <button class="btn btn-secondary btn-danger" type="button" onclick="hideDeletePopup()">Cancel</button>
                </form>
            </div>
        </div>

        <div id="popup" class="popup">
            <div class="popup-content">
                <p id="popup-message"></p>
                <button class="btn btn-secondary" onclick="hidePopup()">Close</button>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


    </div>
</body>
<script src="script.js"></script>
<script src="profile.js"></script>

</html>