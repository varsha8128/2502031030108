<?php

session_start();

include '../config/database.php';

// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ================= CHECK PRODUCT ID =================

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);


// ================= FETCH PRODUCT =================

$sql = "SELECT *
        FROM products
        WHERE id = $product_id";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Product not found.");
}

$product = mysqli_fetch_assoc($result);

$message = "";


// ================= UPDATE PRODUCT =================

if (isset($_POST['update_product'])) {

    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $discount = floatval($_POST['discount']);
    $stock = intval($_POST['stock']);

    $image = $product['image'];

    // ================= NEW IMAGE =================

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        $allowed_types = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];

        if (!in_array($file_type, $allowed_types)) {

            $message = "Only JPG, PNG and WEBP images are allowed.";

        } elseif ($file_size > 2 * 1024 * 1024) {

            $message = "Image size must be less than 2 MB.";

        } else {

            $extension = pathinfo(
                $_FILES['image']['name'],
                PATHINFO_EXTENSION
            );

            $image = time() . "_" . uniqid() . "." . $extension;

            $upload_path = "../uploads/" . $image;

            if (move_uploaded_file(
                $_FILES['image']['tmp_name'],
                $upload_path
            )) {

                // Delete old image
                if (!empty($product['image'])) {

                    $old_image = "../uploads/" . $product['image'];

                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }

                }

            } else {

                $message = "Image upload failed.";

            }

        }
    }


    // ================= UPDATE DATABASE =================

    if (empty($message)) {

        $update_sql = "UPDATE products
                       SET category_id = ?,
                           name = ?,
                           description = ?,
                           price = ?,
                           discount = ?,
                           stock = ?,
                           image = ?
                       WHERE id = ?";

        $stmt = mysqli_prepare($conn, $update_sql);

        mysqli_stmt_bind_param(
            $stmt,
            "issddisi",
            $category_id,
            $name,
            $description,
            $price,
            $discount,
            $stock,
            $image,
            $product_id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: products.php");
            exit();

        } else {

            $message = "Product could not be updated.";

        }

        mysqli_stmt_close($stmt);
    }
}


// ================= FETCH CATEGORIES =================

$category_sql = "SELECT id, name
                 FROM categories
                 ORDER BY name ASC";

$category_result = mysqli_query($conn, $category_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Product - FreshMart Admin</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>


<!-- ================= ADMIN NAVBAR ================= -->

<header class="admin-navbar">

    <div class="admin-logo">
        🛒 FreshMart Admin
    </div>

    <nav>

        <a href="dashboard.php">Dashboard</a>

        <a href="products.php">Products</a>

        <a href="categories.php">Categories</a>

        <a href="orders.php">Orders</a>

        <a href="users.php">Users</a>

        <a href="logout.php">Logout</a>

    </nav>

</header>


<!-- ================= EDIT PRODUCT ================= -->

<section class="add-product-section">

    <div class="add-product-container">

        <h1>
            Edit Product ✏️
        </h1>


        <?php if (!empty($message)): ?>

            <div class="error-message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- PRODUCT NAME -->

            <label>
                Product Name
            </label>

            <input
                type="text"
                name="name"
                value="<?php echo htmlspecialchars($product['name']); ?>"
                required
            >


            <!-- CATEGORY -->

            <label>
                Category
            </label>

            <select
                name="category_id"
                required
            >

                <option value="">
                    Select Category
                </option>

                <?php while ($category = mysqli_fetch_assoc($category_result)): ?>

                    <option
                        value="<?php echo $category['id']; ?>"
                        <?php
                        if ($category['id'] == $product['category_id']) {
                            echo "selected";
                        }
                        ?>
                    >
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>

                <?php endwhile; ?>

            </select>


            <!-- DESCRIPTION -->

            <label>
                Description
            </label>

            <textarea
                name="description"
                rows="5"
            ><?php echo htmlspecialchars($product['description']); ?></textarea>


            <!-- PRICE -->

            <label>
                Price (₹)
            </label>

            <input
                type="number"
                name="price"
                step="0.01"
                min="0"
                value="<?php echo $product['price']; ?>"
                required
            >


            <!-- DISCOUNT -->

            <label>
                Discount (%)
            </label>

            <input
                type="number"
                name="discount"
                step="0.01"
                min="0"
                max="100"
                value="<?php echo $product['discount']; ?>"
            >


            <!-- STOCK -->

            <label>
                Stock
            </label>

            <input
                type="number"
                name="stock"
                min="0"
                value="<?php echo $product['stock']; ?>"
                required
            >


            <!-- CURRENT IMAGE -->

            <label>
                Current Image
            </label>

            <?php if (!empty($product['image'])): ?>

                <div>

                    <img
                        src="../uploads/<?php echo htmlspecialchars($product['image']); ?>"
                        alt="Current Product"
                        width="120"
                    >

                </div>

            <?php else: ?>

                <p>
                    No image available.
                </p>

            <?php endif; ?>


            <!-- NEW IMAGE -->

            <label>
                Replace Image
            </label>

            <input
                type="file"
                name="image"
                accept=".jpg,.jpeg,.png,.webp"
            >


            <!-- UPDATE -->

            <button
                type="submit"
                name="update_product"
            >
                💾 Update Product
            </button>


            <a
                href="products.php"
                class="back-btn"
            >
                ← Back to Products
            </a>

        </form>

    </div>

</section>

<!-- ================= FOOTER ================= -->

<footer class="footer">

    <div class="footer-container">

        <!-- FreshMart -->
        <div class="footer-column">

            <h2>🛒 FreshMart</h2>

            <p>
                Fresh groceries delivered to your doorstep.
            </p>

            <p>
                Quality products, fresh choices,
                and great prices for your everyday needs.
            </p>

        </div>


        <!-- Quick Links -->
        <div class="footer-column">

            <h3>Quick Links</h3>

            <a href="index.php">Home</a>

            <a href="categories.php">Categories</a>

            <a href="products.php">Products</a>

            <a href="cart.php">Cart</a>

            <a href="orders.php">My Orders</a>

            <a href="wishlist.php">Wishlist ❤️</a>

        </div>


        <!-- Customer Support -->
        <div class="footer-column">

            <h3>Customer Support</h3>

            <a href="#">Contact Us</a>

            <a href="#">FAQs</a>

            <a href="#">Privacy Policy</a>

            <a href="#">Terms & Conditions</a>

        </div>


        <!-- Contact -->
        <div class="footer-column">

            <h3>Contact Us</h3>

            <p>📧 support@freshmart.com</p>

            <p>📱 +91 98765 43210</p>

            <p>📍 India</p>

        </div>

    </div>


    <!-- Copyright -->

    <div class="footer-bottom">

        <p>
            © 2026 FreshMart Grocery Store. All Rights Reserved.
        </p>

    </div>

</footer>

</body>

</html>