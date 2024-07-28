<?php

include_once 'dbcon.php';
$id = $_GET['edit'];

if (isset($_POST['update_product'])) {

    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_desc = $_POST['product_desc'];
    $category = $_POST['category'];
    $tags = $_POST['tags'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'uploaded_img/' . $product_image;

    if (empty($product_name) || empty($product_price) || $tags == 'Tags' || empty($product_desc) || $category == 'Category' || empty($product_image)) {
        $message[] = 'please fill out all';
    } else {

        $update_data = "UPDATE products SET product_name='$product_name', price='$product_price', product_image='$product_image', product_category='$category', product_desc='$product_desc', product_tags='$tags' WHERE product_id = '$id'";
        $upload = mysqli_query($con, $update_data);

        if ($upload) {
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
            $message[] = 'Product Updated successfully';
        } else {
            $message[] = 'please fill out all!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="adminstyle.css">
</head>

<body>

<?php
   if (isset($message)) {
      foreach ($message as $msg) {
         echo '<span class="message">' . $msg . '</span>';
      }
      $redirectUrl = "admin_page.php";
      if (isset($_GET['edit'])) {
         $redirectUrl .= "?show=products";
      }
      echo '<script type="text/javascript">
            setTimeout(function(){
               window.location.href = "' . $redirectUrl . '";
            }, 2000);
         </script>';
   }
?>

    <div class="container update-form">

        <div class="admin-product-form-container centered">

            <?php

            $select = mysqli_query($con, "SELECT * FROM products WHERE product_id = '$id'");
            while ($row = mysqli_fetch_assoc($select)) {

            ?>

                <form action="" method="post" enctype="multipart/form-data">
                    <h3 class="title">update the product</h3>
                    <input type="text" class="box" name="product_name" value="<?php echo $row['product_name']; ?>" placeholder="enter the product name">

                    <input type="number" min="0" class="box" name="product_price" value="<?php echo $row['price']; ?>" placeholder="enter the product price">

                    <?php
                    $selected = $row['product_category'];
                    $options = array('Category', 'Consumer Electronics', 'Health & Beauty', 'Home & Garden', 'Accessories', 'Clothings');
                    echo "<select name='category' class='box'>";
                    foreach ($options as $option) {
                        if ($selected == $option) {
                            echo "<option selected='selected' value='$option'>$option</option>";
                        } else {
                            echo "<option value='$option'>$option</option>";
                        }
                    }
                    echo '</select>';
                    ?>

                    <input type="file" class="box" name="product_image" accept="image/png, image/jpeg, image/jpg">

                    <input type="text" class="box" name="product_desc" value="<?php echo $row['product_desc']; ?>" placeholder="enter the product description">

                    <?php
                    $selected = $row['product_tags'];
                    $options = array('Tags', 'Trending', 'New', 'Top Seller', 'Daily');
                    echo "<select name='tags' class='box'>";
                    foreach ($options as $option) {
                        if ($selected == $option) {
                            echo "<option selected='selected' value='$option'>$option</option>";
                        } else {
                            echo "<option value='$option'>$option</option>";
                        }
                    }
                    echo '</select>';
                    ?>

                    <input type="submit" value="update product" name="update_product" class="btn">
                    <a href="admin_page.php?show=products" class="btn">go back!</a>
                </form>

            <?php } ?>

        </div>

    </div>

</body>

</html>
