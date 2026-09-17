<?php

session_start();

include '../config/database.php';


// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {

    header("Location: admin_login.php");
    exit();

}


// ================= DELETE PRODUCT =================

if (isset($_GET['delete'])) {

    $product_id = intval($_GET['delete']);

    mysqli_query(
        $conn,
        "DELETE FROM products WHERE id = $product_id"
    );

    header("Location: products.php");
    exit();

}


// ================= FETCH PRODUCTS =================

    $sql = "SELECT products.*,
                categories.name AS category_name

            FROM products

            LEFT JOIN categories
            ON products.category_id = categories.id

            ORDER BY products.id DESC";

    $result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Products - FreshMart Admin</title>

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


<!-- ================= PRODUCTS ================= -->

<section class="admin-products">

    <div class="admin-title">

        <h1>
            Manage Products 🛒
        </h1>

        <a
            href="add_product.php"
            class="add-product-btn"
        >
            ➕ Add Product
        </a>

    </div>


    <?php if (mysqli_num_rows($result) > 0): ?>

        <div class="admin-product-table">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Image</th>

                        <th>Name</th>

                        <th>Category</th>

                        <th>Price</th>

                        <th>Discount</th>

                        <th>Stock</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($product = mysqli_fetch_assoc($result)): ?>

                        <tr>

                            <td>
                                <?php echo $product['id']; ?>
                            </td>


                            <td>

                                <?php if (!empty($product['image'])): ?>

                                    <img
                                        src="../uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                        width="60"
                                        height="60"
                                        alt="Product"
                                    >

                                <?php else: ?>

                                    No Image

                                <?php endif; ?>

                            </td>


                            <td>
                                <?php echo htmlspecialchars($product['name']); ?>
                            </td>


                            <td>
                                <?php echo htmlspecialchars($product['category_name'] ?? 'No Category'); ?>
                            </td>


                            <td>
                                ₹<?php echo number_format($product['price'], 2); ?>
                            </td>


                            <td>
                                <?php echo number_format($product['discount'], 2); ?>%
                            </td>


                            <td>
                                <?php echo $product['stock']; ?>
                            </td>


                            <td>

                                <a
                                    href="edit_product.php?id=<?php echo $product['id']; ?>"
                                    class="edit-btn"
                                >
                                    ✏️ Edit
                                </a>


                                <a
                                    href="products.php?delete=<?php echo $product['id']; ?>"
                                    class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this product?');"
                                >
                                    🗑️ Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="no-products">

            <h2>
                No Products Found
            </h2>

            <p>
                Add your first grocery product.
            </p>

        </div>

    <?php endif; ?>

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