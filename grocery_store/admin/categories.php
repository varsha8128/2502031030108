<?php

session_start();

include '../config/database.php';


// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {

    header("Location: admin_login.php");
    exit();

}

$message = "";


// ================= ADD CATEGORY =================

if (isset($_POST['add_category'])) {

    $name = trim($_POST['name']);

    if ($name == "") {

        $message = "Category name is required.";

    } else {

        $image = "";

        // ================= CATEGORY IMAGE =================

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

                move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    "../uploads/" . $image
                );

            }
        }


        // ================= INSERT CATEGORY =================

        if (empty($message)) {

            $sql = "INSERT INTO categories (name, image)
                    VALUES (?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $name,
                $image
            );

            if (mysqli_stmt_execute($stmt)) {

                header("Location: categories.php");
                exit();

            } else {

                $message = "Category could not be added.";

            }

            mysqli_stmt_close($stmt);

        }

    }

}


// ================= DELETE CATEGORY =================

if (isset($_GET['delete'])) {

    $category_id = intval($_GET['delete']);

    // Check whether products use this category
    $check_sql = "SELECT COUNT(*) AS total
                  FROM products
                  WHERE category_id = $category_id";

    $check_result = mysqli_query($conn, $check_sql);

    $check = mysqli_fetch_assoc($check_result);

    if ($check['total'] > 0) {

        $message = "This category cannot be deleted because products are using it.";

    } else {

        $category_sql = "SELECT image
                         FROM categories
                         WHERE id = $category_id";

        $category_result = mysqli_query($conn, $category_sql);

        if (mysqli_num_rows($category_result) > 0) {

            $category = mysqli_fetch_assoc($category_result);

            if (!empty($category['image'])) {

                $image_path = "../uploads/" . $category['image'];

                if (file_exists($image_path)) {
                    unlink($image_path);
                }

            }

        }

        mysqli_query(
            $conn,
            "DELETE FROM categories WHERE id = $category_id"
        );

        header("Location: categories.php");
        exit();

    }

}


// ================= FETCH CATEGORIES =================

$sql = "SELECT *
        FROM categories
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Categories - FreshMart Admin</title>

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


<!-- ================= CATEGORY SECTION ================= -->

<section class="admin-category-section">

    <h1>
        Manage Categories 📂
    </h1>


    <?php if (!empty($message)): ?>

        <div class="error-message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- ================= ADD CATEGORY FORM ================= -->

    <div class="category-form">

        <h2>
            Add New Category
        </h2>

        <form
            method="POST"
            enctype="multipart/form-data"
        >

            <label>
                Category Name
            </label>

            <input
                type="text"
                name="name"
                placeholder="Example: Fruits"
                required
            >


            <label>
                Category Image
            </label>

            <input
                type="file"
                name="image"
                accept=".jpg,.jpeg,.png,.webp"
            >


            <button
                type="submit"
                name="add_category"
            >
                ➕ Add Category
            </button>

        </form>

    </div>


    <!-- ================= CATEGORY LIST ================= -->

    <div class="category-list">

        <h2>
            Existing Categories
        </h2>


        <?php if (mysqli_num_rows($result) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Image</th>

                        <th>Name</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($category = mysqli_fetch_assoc($result)): ?>

                        <tr>

                            <td>
                                <?php echo $category['id']; ?>
                            </td>


                            <td>

                                <?php if (!empty($category['image'])): ?>

                                    <img
                                        src="../uploads/<?php echo htmlspecialchars($category['image']); ?>"
                                        width="60"
                                        height="60"
                                        alt="Category"
                                    >

                                <?php else: ?>

                                    No Image

                                <?php endif; ?>

                            </td>


                            <td>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </td>


                            <td>

                                <a
                                    href="edit_category.php?id=<?php echo $category['id']; ?>"
                                    class="edit-btn"
                                >
                                    ✏️ Edit
                                </a>


                                <a
                                    href="categories.php?delete=<?php echo $category['id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Delete this category?');"
                                >
                                    🗑️ Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p>
                No categories found.
            </p>

        <?php endif; ?>

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