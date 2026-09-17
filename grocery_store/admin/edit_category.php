<?php

session_start();

include '../config/database.php';


// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {

    header("Location: admin_login.php");
    exit();

}


// ================= CHECK CATEGORY ID =================

if (!isset($_GET['id'])) {

    header("Location: categories.php");
    exit();

}

$category_id = intval($_GET['id']);


// ================= FETCH CATEGORY =================

$sql = "SELECT *
        FROM categories
        WHERE id = $category_id";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {

    die("Category not found.");

}

$category = mysqli_fetch_assoc($result);

$message = "";


// ================= UPDATE CATEGORY =================

if (isset($_POST['update_category'])) {

    $name = trim($_POST['name']);

    if ($name == "") {

        $message = "Category name is required.";

    } else {

        $image = $category['image'];


        // ================= NEW IMAGE =================

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] == 0
        ) {

            $allowed_types = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];


            if (!in_array($file_type, $allowed_types)) {

                $message =
                    "Only JPG, PNG and WEBP images are allowed.";

            } elseif ($file_size > 2 * 1024 * 1024) {

                $message =
                    "Image size must be less than 2 MB.";

            } else {

                $extension = pathinfo(
                    $_FILES['image']['name'],
                    PATHINFO_EXTENSION
                );

                $image =
                    time() . "_" .
                    uniqid() . "." .
                    $extension;

                $upload_path =
                    "../uploads/" . $image;


                if (
                    move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $upload_path
                    )
                ) {

                    // Delete old image

                    if (!empty($category['image'])) {

                        $old_image =
                            "../uploads/" .
                            $category['image'];

                        if (file_exists($old_image)) {

                            unlink($old_image);

                        }

                    }

                } else {

                    $message =
                        "Image upload failed.";

                }

            }

        }


        // ================= UPDATE DATABASE =================

        if (empty($message)) {

            $update_sql =
                "UPDATE categories
                 SET name = ?,
                     image = ?
                 WHERE id = ?";


            $stmt =
                mysqli_prepare(
                    $conn,
                    $update_sql
                );


            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $name,
                $image,
                $category_id
            );


            if (
                mysqli_stmt_execute($stmt)
            ) {

                header(
                    "Location: categories.php"
                );

                exit();

            } else {

                $message =
                    "Category could not be updated.";

            }


            mysqli_stmt_close($stmt);

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Edit Category - FreshMart Admin
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

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


<!-- ================= EDIT CATEGORY ================= -->

<section class="add-product-section">

    <div class="add-product-container">

        <h1>
            Edit Category ✏️
        </h1>


        <?php if (!empty($message)): ?>

            <div class="error-message">

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- CATEGORY NAME -->

            <label>
                Category Name
            </label>

            <input
                type="text"
                name="name"
                value="<?php
                echo htmlspecialchars(
                    $category['name']
                );
                ?>"
                required
            >


            <!-- CURRENT IMAGE -->

            <label>
                Current Image
            </label>


            <?php if (!empty($category['image'])): ?>

                <div>

                    <img
                        src="../uploads/<?php
                        echo htmlspecialchars(
                            $category['image']
                        );
                        ?>"
                        alt="Category Image"
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
                name="update_category"
            >
                💾 Update Category
            </button>


            <a
                href="categories.php"
                class="back-btn"
            >
                ← Back to Categories
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