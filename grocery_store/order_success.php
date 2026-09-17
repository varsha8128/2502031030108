<?php

session_start();

include 'config/database.php';


// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

$user_id = (int) $_SESSION['user_id'];


// ================= GET ORDER ID =================

$order_id = isset($_GET['order_id'])
    ? intval($_GET['order_id'])
    : 0;


// ================= VALIDATE ORDER ID =================

if ($order_id <= 0) {

    header("Location: orders.php");
    exit();

}


// ================= FETCH ORDER =================

$order_sql = "SELECT
                    orders.*,
                    users.name AS customer_name
              FROM orders
              INNER JOIN users
              ON orders.user_id = users.id
              WHERE orders.id = ?
              AND orders.user_id = ?
              LIMIT 1";

$order_stmt = mysqli_prepare($conn, $order_sql);

mysqli_stmt_bind_param(
    $order_stmt,
    "ii",
    $order_id,
    $user_id
);

mysqli_stmt_execute($order_stmt);

$order_result = mysqli_stmt_get_result($order_stmt);

$order = null;

if ($order_result && mysqli_num_rows($order_result) > 0) {

    $order = mysqli_fetch_assoc($order_result);

}

mysqli_stmt_close($order_stmt);


// ================= ORDER NOT FOUND =================

if (!$order) {

    header("Location: orders.php");
    exit();

}


// ================= FETCH ORDER ITEMS =================

$items_sql = "SELECT
                    order_items.*,
                    products.name AS product_name,
                    products.image AS product_image
              FROM order_items
              INNER JOIN products
              ON order_items.product_id = products.id
              WHERE order_items.order_id = ?";

$items_stmt = mysqli_prepare($conn, $items_sql);

mysqli_stmt_bind_param(
    $items_stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($items_stmt);

$items_result = mysqli_stmt_get_result($items_stmt);

$order_items = [];

if ($items_result) {

    while ($item = mysqli_fetch_assoc($items_result)) {

        $order_items[] = $item;

    }

}

mysqli_stmt_close($items_stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Successful - FreshMart</title>

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

        <a href="categories.php">Categories</a>

        <a href="products.php">Products</a>

        <a href="cart.php">Cart 🛒</a>

        <a href="orders.php">My Orders 📦</a>

    </nav>

</header>


<!-- ================= SUCCESS PAGE ================= -->

<section class="success-section">

    <div class="success-container">


        <!-- ================= SUCCESS MESSAGE ================= -->

        <div class="success-header">

            <div class="success-icon">
                ✅
            </div>


            <h1>
                Order Placed Successfully!
            </h1>


            <p>
                Thank you for shopping with FreshMart.
            </p>


            <p class="order-id">

                Order ID:

                <strong>
                    #<?php echo $order['id']; ?>
                </strong>

            </p>


            <p>
                Your order has been successfully placed.
            </p>

        </div>


        <!-- ================= CUSTOMER + ORDER INFO ================= -->

        <div class="success-grid">


            <!-- CUSTOMER INFORMATION -->

            <div class="success-card customer-info">

                <h2>
                    Customer Information
                </h2>


                <p>

                    <strong>Name:</strong>

                    <?php echo htmlspecialchars(
                        $order['customer_name']
                    ); ?>

                </p>


                <p>

                    <strong>Email:</strong>

                    <?php echo htmlspecialchars(
                        $order['email']
                    ); ?>

                </p>


                <p>

                    <strong>Mobile:</strong>

                    <?php echo htmlspecialchars(
                        $order['phone'] ?? 'Not available'
                    ); ?>

                </p>


                <p>

                    <strong>Shipping Address:</strong><br>

                    <?php echo nl2br(
                        htmlspecialchars(
                            $order['shipping_address']
                        )
                    ); ?>

                </p>


                <p>

                    <strong>City:</strong>

                    <?php echo htmlspecialchars(
                        $order['city']
                    ); ?>

                </p>


                <p>

                    <strong>State:</strong>

                    <?php echo htmlspecialchars(
                        $order['state']
                    ); ?>

                </p>


                <p>

                    <strong>Pincode:</strong>

                    <?php echo htmlspecialchars(
                        $order['pincode']
                    ); ?>

                </p>

            </div>


            <!-- ORDER INFORMATION -->

            <div class="success-card">

                <h2>
                    Order Information
                </h2>


                <p>

                    <strong>Order ID:</strong>

                    #<?php echo $order['id']; ?>

                </p>


                <p>

                    <strong>Order Date:</strong>

                    <?php

                    echo date(
                        'd M Y, h:i A',
                        strtotime($order['order_date'])
                    );

                    ?>

                </p>


                <p>

                    <strong>Payment Method:</strong>

                    <span class="payment-method">

                        <?php

                        echo htmlspecialchars(
                            $order['payment_method']
                        );

                        ?>

                    </span>

                </p>


                <p>

                    <strong>Order Status:</strong>

                    <?php echo htmlspecialchars(
                        $order['status']
                    ); ?>

                </p>

            </div>

        </div>


        <!-- ================= ORDERED PRODUCTS ================= -->

        <div class="order-details">

            <h2>
                Ordered Products
            </h2>


            <?php if (count($order_items) > 0): ?>


                <?php foreach ($order_items as $item): ?>

                    <div class="ordered-product">


                        <!-- PRODUCT IMAGE -->

                        <?php if (!empty($item['product_image'])): ?>

                            <img
                                src="uploads/<?php echo htmlspecialchars(
                                    $item['product_image']
                                ); ?>"
                                alt="<?php echo htmlspecialchars(
                                    $item['product_name']
                                ); ?>"
                                class="product-image"
                            >

                        <?php else: ?>

                            <div class="product-image">
                                🛒
                            </div>

                        <?php endif; ?>


                        <!-- PRODUCT INFO -->

                        <div class="product-info">

                            <h3>

                                <?php echo htmlspecialchars(
                                    $item['product_name']
                                ); ?>

                            </h3>


                            <p>

                                Price:
                                ₹<?php echo number_format(
                                    $item['price'],
                                    2
                                ); ?>

                            </p>


                            <p>

                                Quantity:
                                <?php echo (int)$item['quantity']; ?>

                            </p>

                        </div>


                        <!-- PRODUCT TOTAL -->

                        <div class="product-total">

                            ₹<?php echo number_format(
                                $item['price'] * $item['quantity'],
                                2
                            ); ?>

                        </div>


                    </div>

                <?php endforeach; ?>


            <?php else: ?>

                <p>
                    No ordered products found.
                </p>

            <?php endif; ?>


            <!-- TOTAL -->

            <div class="order-total">

                <strong>
                    Total Amount
                </strong>


                <strong>

                    ₹<?php echo number_format(
                        $order['total_amount'],
                        2
                    ); ?>

                </strong>

            </div>

        </div>


        <!-- ================= BUTTONS ================= -->

        <div class="success-buttons">

            <a href="index.php">
                🛍️ Continue Shopping
            </a>


            <a
                href="orders.php"
                class="secondary"
            >
                📦 View Order History
            </a>

        </div>


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