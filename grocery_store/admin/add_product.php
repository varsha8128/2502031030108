<?php

session_start();

include '../config/database.php';


// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {

    header("Location: admin_login.php");
    exit();

}

$message = "";


// ================= ADD PRODUCT =================

if (isset($_POST['add_product'])) {

    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $discount = floatval($_POST['discount']);
    $stock = intval($_POST['stock']);

    $image = "";


    // ================= IMAGE UPLOAD =================

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

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                $upload_path
            );

        }

    }


    // ================= INSERT PRODUCT =================

    if (empty($message)) {

        $sql = "INSERT INTO products
                (category_id, name, description, price, discount, stock, image)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "issddis",
            $category_id,
            $name,
            $description,
            $price,
            $discount,
            $stock,
            $image
        );


        if (mysqli_stmt_execute($stmt)) {

            header("Location: products.php");
            exit();

        } else {

            $message = "Product could not be added.";

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

    <title>Add Product - FreshMart Admin</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>


<!-- ================= ADMIN NAVBAR ================= -->

<header class="admin-navbar">

    <div class="admin-logo">
        🛒 FreshMart Admin
    </div>

    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="products.php">
            Products
        </a>

        <a href="categories.php">
            Categories
        </a>

        <a href="orders.php">
            Orders
        </a>

        <a href="users.php">
            Users
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<!-- ================= ADD PRODUCT FORM ================= -->

<section class="add-product-section">

    <div class="add-product-container">

        <h1>
            Add New Product ➕
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
                placeholder="Example: Fresh Apples"
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
                placeholder="Enter product description"
            ></textarea>


            <!-- PRICE -->

            <label>
                Price (₹)
            </label>

            <input
                type="number"
                name="price"
                step="0.01"
                min="0"
                placeholder="Enter price"
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
                value="0"
            >


            <!-- STOCK -->

            <label>
                Stock
            </label>

            <input
                type="number"
                name="stock"
                min="0"
                placeholder="Enter stock"
                required
            >


            <!-- IMAGE -->

            <label>
                Product Image
            </label>

            <input
                type="file"
                name="image"
                accept=".jpg,.jpeg,.png,.webp"
            >


            <!-- SUBMIT -->

            <button
                type="submit"
                name="add_product"
            >
                ➕ Add Product
            </button>


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