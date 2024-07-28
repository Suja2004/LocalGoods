<?php

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

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Panel</title>

   <!-- Font Awesome CDN link -->
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
         $redirectUrl .= "?show=products";
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
         <span class="logo-text">Local Goods</span>
      </div>
      <nav class="navbar">
         <div class="option">
            <a href="#" id="home">Home</a>
            <a href="#product-page" id="product">Products</a>
            <a href="#" id="help">Help</a>
            <a href="#" id="contact">Contact</a>
         </div>
      </nav>
      <div class="admin-product-form-container">
         <div id="home-page">
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

               <?php
               $tags = array('Tags', 'Trending', 'New', 'Top Seller', 'Daily');
               echo "<select name='tags' class='box'>";
               foreach ($tags as $tag) {
                  echo "<option value='$tag'>$tag</option>";
               }
               echo '</select>';
               ?>
               <input type="submit" class="btn" name="add_product" value="add product">
            </form>
         </div>
      </div>

      <?php
      $select = mysqli_query($con, "SELECT * FROM products");
      ?>
      <div id="product-page" class="outer-wrapper" style="display: none;">
         <div id="product" class="product-display table-wrapper">
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
               <?php while ($row = mysqli_fetch_assoc($select)) { ?>
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
            </table>
         </div>
      </div>

   </div>

</body>
<script>
   const home = document.getElementById('home');
   const homepage = document.getElementById('home-page');
   const product = document.getElementById('product');
   const productpage = document.getElementById('product-page');

   home.addEventListener('click', () => {
       homepage.style.display = 'block';
       productpage.style.display = 'none';
   });
   
   product.addEventListener('click', () => {
       homepage.style.display = 'none';
       productpage.style.display = 'block';
   });

   document.addEventListener('DOMContentLoaded', () => {
      const urlParams = new URLSearchParams(window.location.search);
      const showParam = urlParams.get('show');

      if (showParam === 'products') {
         homepage.style.display = 'none';
         productpage.style.display = 'block';
      }
   });
</script>

</html>
