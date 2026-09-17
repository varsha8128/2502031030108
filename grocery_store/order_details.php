<?php

session_start();

include 'config/database.php';


// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];


// ================= CHECK ORDER ID =================

if (!isset($_GET['id'])) {

    header("Location: orders.php");
    exit();

}

$order_id = intval($_GET['id']);


// ================= FETCH ORDER =================

$order_sql = "SELECT id, total_amount, status, order_date
              FROM orders
              WHERE id = $order_id
              AND user_id = $user_id";

$order_result = mysqli_query($conn, $order_sql);

if (mysqli_num_rows($order_result) == 0) {

    die("Order not found.");

}

$order = mysqli_fetch_assoc($order_result);


// ================= FETCH ORDER ITEMS =================

$items_sql = "SELECT order_items.quantity,
                     order_items.price,
                     products.name,
                     products.image

              FROM order_items

              INNER JOIN products
              ON order_items.product_id = products.id

              WHERE order_items.order_id = $order_id";

$items_result = mysqli_query($conn, $items_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Order #<?php echo $order_id; ?> - FreshMart
    </title>

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


<!-- ================= ORDER DETAILS ================= -->

<section class="order-details-section">

    <h1>
        Order Details 📦
    </h1>


    <div class="order-info">

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
                Status:
            </strong>

            <?php echo htmlspecialchars($order['status']); ?>

        </p>

    </div>


    <!-- ================= PRODUCTS ================= -->

    <div class="order-products">

        <h2>
            Ordered Products
        </h2>


        <?php if (mysqli_num_rows($items_result) > 0): ?>

            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>

                <div class="order-product">


                    <div class="order-product-image">

                        <img
                            src="uploads/<?php echo htmlspecialchars($item['image']); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                        >

                    </div>


                    <div class="order-product-info">

                        <h3>
                            <?php echo htmlspecialchars($item['name']); ?>
                        </h3>

                        <p>
                            Price:
                            ₹<?php echo number_format($item['price'], 2); ?>
                        </p>

                        <p>
                            Quantity:
                            <?php echo $item['quantity']; ?>
                        </p>

                        <p>

                            Subtotal:

                            ₹<?php
                            echo number_format(
                                $item['price'] * $item['quantity'],
                                2
                            );
                            ?>

                        </p>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <p>
                No products found.
            </p>

        <?php endif; ?>

    </div>


    <!-- ================= TOTAL ================= -->

    <div class="order-total">

        <h2>

            Total Amount:

            ₹<?php
            echo number_format(
                $order['total_amount'],
                2
            );
            ?>

        </h2>

    </div>


    <a
        href="orders.php"
        class="back-orders-btn"
    >
        ← Back to My Orders
    </a>


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

            <a href="contact.php">Contact Us</a>

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