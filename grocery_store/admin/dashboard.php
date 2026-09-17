
<?php

session_start();

include '../config/database.php';


// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {

    header("Location: admin_login.php");
    exit();

}


// ================= DASHBOARD COUNTS =================

// TOTAL USERS
$users_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);

$users = mysqli_fetch_assoc($users_result)['total'];


// TOTAL PRODUCTS
$products_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM products"
);

$products = mysqli_fetch_assoc($products_result)['total'];


// TOTAL ORDERS
$orders_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM orders"
);

$orders = mysqli_fetch_assoc($orders_result)['total'];


// TOTAL CATEGORIES
$categories_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM categories"
);

$categories = mysqli_fetch_assoc($categories_result)['total'];


// ================= TOTAL SALES / REVENUE =================

$revenue_result = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(total_amount), 0) AS total_revenue FROM orders"
);

$revenue = mysqli_fetch_assoc($revenue_result)['total_revenue'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - FreshMart</title>

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


<!-- ================= DASHBOARD ================= -->

<section class="admin-dashboard">

    <h1>
        Welcome,
        <?php echo htmlspecialchars($_SESSION['admin_name']); ?> 👋
    </h1>


    <!-- ================= DASHBOARD CARDS ================= -->

    <div class="dashboard-cards">


        <!-- USERS -->

        <div class="dashboard-card">

            <h2>
                👥 Users
            </h2>

            <p>
                <?php echo $users; ?>
            </p>

        </div>


        <!-- PRODUCTS -->

        <div class="dashboard-card">

            <h2>
                🛒 Products
            </h2>

            <p>
                <?php echo $products; ?>
            </p>

        </div>


        <!-- CATEGORIES -->

        <div class="dashboard-card">

            <h2>
                📂 Categories
            </h2>

            <p>
                <?php echo $categories; ?>
            </p>

        </div>


        <!-- ORDERS -->

        <div class="dashboard-card">

            <h2>
                📦 Orders
            </h2>

            <p>
                <?php echo $orders; ?>
            </p>

        </div>


        <!-- TOTAL REVENUE -->

        <div class="dashboard-card">

            <h2>
                💰 Total Revenue
            </h2>

            <p>
                ₹<?php echo number_format($revenue, 2); ?>
            </p>

        </div>


    </div>


    <!-- ================= QUICK ACTIONS ================= -->

    <div class="quick-actions">

        <h2>
            Quick Actions
        </h2>


        <a href="products.php">
            ➕ Manage Products
        </a>


        <a href="categories.php">
            📂 Manage Categories
        </a>


        <a href="orders.php">
            📦 Manage Orders
        </a>


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
