<?php

session_start();

include 'config/database.php';


// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];


// ================= FETCH ORDERS =================

$sql = "SELECT id, total_amount, status, order_date
        FROM orders
        WHERE user_id = $user_id
        ORDER BY order_date DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Orders - FreshMart</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>


<!-- ================= NAVBAR ================= -->

<header class="navbar">

    <div class="logo">
        🛒 FreshMart
    </div>

    <nav>

        <a href="index.php">Home</a>

        <a href="index.php#products">Products</a>

        <a href="cart.php">Cart 🛒</a>

        <a href="orders.php">My Orders</a>

    </nav>

</header>


<!-- ================= ORDERS ================= -->

<section class="orders-section">

    <h1>My Orders 📦</h1>


    <?php if (mysqli_num_rows($result) > 0): ?>

        <div class="orders-container">


            <?php while ($order = mysqli_fetch_assoc($result)): ?>

                <div class="order-card">

                    <h2>
                        Order #<?php echo $order['id']; ?>
                    </h2>


                    <p>

                        <strong>
                            Order Date:
                        </strong>

                        <?php echo htmlspecialchars($order['order_date']); ?>

                    </p>


                    <p>

                        <strong>
                            Total Amount:
                        </strong>

                        ₹<?php echo number_format($order['total_amount'], 2); ?>

                    </p>


                    <p>

                        <strong>
                            Status:
                        </strong>

                        <span class="order-status">

                            <?php echo htmlspecialchars($order['status']); ?>

                        </span>

                    </p>


                    <a
                        href="order_details.php?id=<?php echo $order['id']; ?>"
                        class="view-order-btn"
                    >
                        View Order Details
                    </a>

                </div>

            <?php endwhile; ?>


        </div>


    <?php else: ?>

        <div class="empty-orders">

            <h2>No Orders Yet 📦</h2>

            <p>
                You haven't placed any orders yet.
            </p>

            <a href="index.php#products">
                Start Shopping
            </a>

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