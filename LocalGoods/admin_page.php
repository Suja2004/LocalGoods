<?php

session_start();

if (!isset($_SESSION['email'])) {
   header("Location: index.html");
   exit();
}

include_once 'dbcon.php';

if (isset($_POST['add_product'])) {

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_desc = $_POST['product_desc'];
   $category = $_POST['category'];
   $tags = $_POST['tags'];
   $product_image = $_FILES['product_image']['name'];
   $product_image_size = $_FILES['product_image']['size'];
   $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
   $product_image_folder = 'uploaded_img/' . $product_image;

   if (empty($product_name) || empty($product_price) || $tags == 'Tags' || empty($product_desc) || $category == 'Category' || empty($product_image)) {
      $message[] = 'Please fill out all fields';
   } else if ($product_image_size > 1250000) {
      $message[] = "Image size is more than 1 MB";
   } else {
      $insert = "INSERT INTO products(product_name, price, product_image, product_category, product_desc, product_tags) VALUES('$product_name', '$product_price', '$product_image', '$category', '$product_desc', '$tags')";
      $upload = mysqli_query($con, $insert);
      if ($upload) {
         move_uploaded_file($product_image_tmp_name, $product_image_folder);
         $message[] = 'New product added successfully';
      } else {
         $message[] = 'Could not add the product';
      }
   }
}

if (isset($_GET['delete'])) {
   $id = $_GET['delete'];
   mysqli_query($con, "DELETE FROM products WHERE product_id = $id");
   $message[] = 'Product deleted successfully';
   echo '<script type="text/javascript">
            setTimeout(function(){
               window.location.href = "admin_page.php?show=products";
            }, 3000); 
         </script>';
}


if (isset($_GET['deletemail'])) {
   $id = intval($_GET['deletemail']);
   $stmt = $con->prepare("DELETE FROM contact_form WHERE id = ?");
   $stmt->bind_param("i", $id);
   if ($stmt->execute()) {
      header("Location: admin_page.php");
      exit;
   }
   $stmt->close();
}


$sql = "SELECT id, name, email, subject, message, created_at FROM contact_form ORDER BY created_at DESC";
$result = $con->query($sql);

if (isset($_POST['respond'])) {
   $id = intval($_POST['id']);
   $replySubject = $_POST['reply_subject'];
   $replyMessage = $_POST['reply_message'];

   $stmt = $con->prepare("SELECT email FROM contact_form WHERE id = ?");
   $stmt->bind_param("i", $id);
   $stmt->execute();
   $stmt->bind_result($email);
   $stmt->fetch();
   $stmt->close();

   if ($email) {
      $to = $email;
      $subject = $replySubject;
      $message = $replyMessage;
      $headers = "From: no-reply@yourdomain.com\r\n";

      if (mail($to, $subject, $message, $headers)) {
         echo "<p>Email sent successfully!</p>";
      } else {
         echo "<p>Failed to send email.</p>";
      }
   }

   header("Location:admin_page.php");
   exit;
}

if (isset($_POST['send_response'])) {
   $order_id = intval($_POST['order_id']);
   $response_message = $_POST['response_message'];
   $arrival_date = $_POST['arrival_date'];

   $stmt = $con->prepare("SELECT email FROM users INNER JOIN orders ON users.id = orders.user_id WHERE orders.id = ?");
   $stmt->bind_param("i", $order_id);
   $stmt->execute();
   $stmt->bind_result($customer_email);
   $stmt->fetch();
   $stmt->close();

   if ($customer_email) {
      $to = $customer_email;
      $subject = "Order Update";
      $message = "Your order will arrive on " . $arrival_date . ". " . $response_message;
      $headers = "From: no-reply@yourdomain.com\r\n";

      if (mail($to, $subject, $message, $headers)) {
         echo "<p>Email sent successfully!</p>";
      } else {
         echo "<p>Failed to send email.</p>";
      }
   }

   header("Location: admin_page.php?show=orders");
   exit;
}
$sql_orders = "SELECT 
                    orders.id, 
                    users.username, 
                    users.email, 
                    orders.address, 
                    orders.phone, 
                    orders.grand_total, 
                    orders.order_date 
               FROM 
                    orders
               INNER JOIN 
                    users ON orders.user_id = users.id
               ORDER BY 
                    orders.order_date";
$orderresult = $con->query($sql_orders);

$sql_order_items = "SELECT order_items.order_id, 
                    order_items.quantity, 
                    products.product_name
                    FROM order_items
                    INNER JOIN products ON order_items.product_id = products.product_id";
$orderItemsResult = $con->query($sql_order_items);

$orderItems = [];
$quantities = [];

while ($item = $orderItemsResult->fetch_assoc()) {
   $order_id = $item['order_id'];
   if (!isset($orderItems[$order_id])) {
      $orderItems[$order_id] = [];
      $quantities[$order_id] = [];
   }
   $orderItems[$order_id][] = $item['product_name'];
   $quantities[$order_id][] = $item['quantity'];
}




?>



<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Panel</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

   <link rel="stylesheet" href="adminstyle.css">
</head>

<body>

   <?php
   if (isset($message)) {
      foreach ($message as $msg) {
         echo '<span class="message">' . $msg . '</span>';
      }
      $redirectUrl = "admin_page.php";
      if (isset($_GET['delete'])) {
         $redirectUrl .= "?show=product";
      }
      echo '<script type="text/javascript">
            setTimeout(function(){
               window.location.href = "' . $redirectUrl . '";
            }, 2000); // Redirect after 3 seconds
         </script>';
   }
   ?>

   <div class="container">
      <div class="navbar-logo logo">
         <span class="p-1">L</span>
         <span class="p-2">G</span>
         <span class="logo-text" onclick="return confirmLogout();">Local Goods</span>
      </div>
      <nav class="navbar">
         <div class="option">
            <a href="#product-page" id="product">Home</a>
            <a href="#" id="home">Add Products</a>
            <a href="#orders-page" id="orders">Orders</a>
            <a href="#messages-page" id="messages">Messages</a>
         </div>
      </nav>
      <div class="admin-product-form-container">
         <div id="home-page" style='display:none'>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
               <h3>add a new product</h3>
               <input type="text" placeholder="enter product name" name="product_name" class="box">
               <input type="number" placeholder="enter product price" name="product_price" class="box">

               <?php
               $categories = array('Category', 'Consumer Electronics', 'Health & Beauty', 'Home & Garden', 'Accessories', 'Clothings');
               echo "<select name='category' class='box'>";
               foreach ($categories as $category) {
                  echo "<option value='$category'>$category</option>";
               }
               echo '</select>';
               ?>

               <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box">
               <input type="text" placeholder="enter product description" name="product_desc" class="box">

               <input type="text" placeholder="enter product tags" name="tags" class="box">

               <input type="submit" class="btn" name="add_product" value="add product">
            </form>
         </div>
      </div>

      <?php
      if (isset($_GET['query']) || isset($_GET['sort'])) {
         $display_style = 'display: block;';
      }
      ?>

      <div id="product-page" class="outer-wrapper" style="<?php echo $display_style; ?>">
         <div id="product" class="product-display table-wrapper">
            <form class="searchsort" method="get">
               <div class="search">
                  <input type="search" name="query" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="Search">
                  <input type="submit" class="searchbtn fbtn" value="Search">
               </div>
               <div class="sort">
                  <select name="sort">
                     <option value="" <?php echo (isset($_GET['sort']) && $_GET['sort'] == '') ? 'selected' : ''; ?>>Sort</option>
                     <option value="product_name" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'product_name') ? 'selected' : ''; ?>>Name</option>
                     <option value="price" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price') ? 'selected' : ''; ?>>Price</option>
                     <option value="product_category" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'product_category') ? 'selected' : ''; ?>>Category</option>
                  </select>
                  <input type="hidden" name="order" id="order" value="<?php echo isset($_GET['order']) ? htmlspecialchars($_GET['order']) : 'asc'; ?>">
                  <input type="submit" class="sortbtn fbtn" value="Sort">

                  <button type="button" class="toggle-btn tbtn" onclick="toggleOrder()">&#9650; &#9660;</button>
               </div>
            </form>


            <script>
               function toggleOrder() {
                  var orderInput = document.getElementById('order');
                  var currentOrder = orderInput.value;
                  var newOrder = (currentOrder === 'asc') ? 'desc' : 'asc';
                  orderInput.value = newOrder;
                  document.querySelector('.searchsort').submit();
               }
            </script>


            <table class="product-display-table">
               <thead>
                  <tr>
                     <th>product image</th>
                     <th>product name</th>
                     <th>product price</th>
                     <th>product category</th>
                     <th>product description</th>
                     <th>product tags</th>
                     <th>action</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $search_query = "";
                  if (isset($_GET['query'])) {
                     $search_query = $con->real_escape_string($_GET['query']);
                  }

                  $sort_by = "";
                  if (isset($_GET['sort']) && $_GET['sort'] != '') {
                     $sort_by = $con->real_escape_string($_GET['sort']);
                  }

                  $order_by = "ASC"; 
                  if (isset($_GET['order']) && ($_GET['order'] == 'desc' || $_GET['order'] == 'asc')) {
                     $order_by = strtoupper($con->real_escape_string($_GET['order']));
                  }

                  $sql = "SELECT * FROM products";
                  if (!empty($search_query)) {
                     $sql .= " WHERE product_name LIKE '%$search_query%' OR product_category LIKE '%$search_query%' OR product_tags LIKE '%$search_query%'";
                  }

                  if (!empty($sort_by)) {
                     $sql .= " ORDER BY $sort_by $order_by";
                  }

                  $select = mysqli_query($con, $sql);

                  while ($row = mysqli_fetch_assoc($select)) { ?>
                     <tr>
                        <td><img src="uploaded_img/<?php echo $row['product_image']; ?>" height="100" alt=""></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['price']; ?>/-</td>
                        <td><?php echo $row['product_category']; ?></td>
                        <td><?php echo $row['product_desc']; ?></td>
                        <td><?php echo $row['product_tags']; ?></td>
                        <td>
                           <a class="btn" href="admin_update.php?edit=<?php echo $row['product_id']; ?>"> <i class="fas fa-edit"></i> edit </a>
                           <a class="btn" href="admin_page.php?delete=<?php echo $row['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this item?');"> <i class="fas fa-trash"></i> delete </a>
                        </td>
                     </tr>
                  <?php } ?>

               </tbody>
            </table>
         </div>
      </div>

      <div class="messages" id="messages-page">
         <header class="header">
            <h1>Emails from Contact Form</h1>
         </header>

         <main class="main-content">
            <table class="email-table">
               <thead>
                  <tr>
                     <th>ID</th>
                     <th>Name</th>
                     <th>Email</th>
                     <th>Subject</th>
                     <th>Message</th>
                     <th>Received At</th>
                     <th>Actions</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  if ($result->num_rows > 0) {
                     while ($row = $result->fetch_assoc()) {
                        $email = htmlspecialchars($row['email']);
                        $subject = htmlspecialchars($row['subject']);
                        $message = htmlspecialchars($row['message']);
                        $id = htmlspecialchars($row['id']);
                        $created_at = htmlspecialchars($row['created_at']);

                        echo "<tr>";
                        echo "<td>$id</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>$email</td>";
                        echo "<td>$subject</td>";
                        echo "<td>" . nl2br($message) . "</td>";
                        echo "<td>$created_at</td>";
                        echo '<td>
                            <button class="btn respond-btn" onclick="showResponseForm(' . $id . ', \'' . $email . '\', \'' . addslashes($subject) . '\')">Respond</button>

                            <a href="?deletemail=' . $id . '" class="btn delete-btn" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</a>
                        </td>';
                        echo "</tr>";
                     }
                  } else {
                     echo "<tr><td colspan='7'>No records found</td></tr>";
                  }

                  $con->close();
                  ?>
               </tbody>
            </table>
         </main>
         <div id="response-form" class="response-form">
            <h2>Respond to Email</h2>
            <form action="admin_page.php" method="POST">
               <input type="hidden" name="id" id="response-id">
               <label for="reply_subject">Subject:</label>
               <input type="text" name="reply_subject" id="reply_subject" required>
               <label for="reply_message">Message:</label>
               <textarea name="reply_message" id="reply_message" required></textarea>
               <button type="submit" name="respond">Send Response</button>
               <button type="button" onclick="hideResponseForm()">Cancel</button>
            </form>
         </div>
      </div>
      <div id="orders-page" class="orders-page">
         <header class="header">
            <h1>Orders</h1>
         </header>

         <main class="main-content">
            <table class="orders-table">
               <thead>
                  <tr>
                     <th>ID</th>
                     <th>Name</th>
                     <th>Address</th>
                     <th>Phone</th>
                     <th>Product</th>
                     <th>Quantity</th>
                     <th>Total</th>
                     <th>Order Date</th>
                     <th>Actions</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  if ($orderresult->num_rows > 0) {
                     while ($order = $orderresult->fetch_assoc()) {
                        $order_id = htmlspecialchars($order['id']);
                        $customer_name = htmlspecialchars($order['username']);
                        $address = htmlspecialchars($order['address']);
                        $phone = htmlspecialchars($order['phone']);
                        $grand_total = htmlspecialchars($order['grand_total']);
                        $order_date = htmlspecialchars($order['order_date']);
                        $email = htmlspecialchars($order['email']);

                        echo "<tr>";
                        echo "<td>$order_id</td>";
                        echo "<td>$customer_name</td>";
                        echo "<td>$address</td>";
                        echo "<td>$phone</td>";


                        $product_list = [];
                        $quantity_list = [];

                        if (isset($orderItems[$order_id])) {
                           foreach ($orderItems[$order_id] as $index => $product_name) {
                              $quantity = isset($quantities[$order_id][$index]) ? $quantities[$order_id][$index] : 0;
                              $product_list[] = htmlspecialchars($product_name);
                           }
                           $product_list = implode("<br>", $product_list);
                        } else {
                           $product_list = "No products found";
                        }

                        echo "<td>$product_list</td>";
                        echo "<td>$quantity</td>";

                        echo "<td>$grand_total</td>";
                        echo "<td>$order_date</td>";
                        echo '<td>
                          <button class="btn respond-btn" onclick="showResponseorderForm(' . $order_id . ', \'' . $email . '\')">Respond</button>
                          
                      </td>';
                        echo "</tr>";
                     }
                  } else {
                     echo "<tr><td colspan='9'>No records found</td></tr>";
                  }
                  ?>
               </tbody>
            </table>
         </main>
      </div>


      <div id="order-response-form" class="response-form" style="display: none;">
         <h2>Respond to Order</h2>
         <form action="admin_page.php" method="POST">
            <input type="text" name="order_id" id="response-order-id" readonly>
            <label for="arrival_date">Expected Arrival Date:</label>
            <input type="date" name="arrival_date" id="arrival_date" required>
            <label for="response_message">Message:</label>
            <textarea name="response_message" id="response_message" required></textarea>
            <button type="submit" name="send_response">Send Response</button>
            <button type="button" onclick="hideResponseorderForm()">Cancel</button>
         </form>
      </div>
      <script>
         function confirmLogout() {
            if (confirm('Are you sure you want to Logout?')) {
               window.location.href = 'logout.php';
            }
            return false;
         }
      </script>
</body>
<script src="admin.js">
</script>

</html>