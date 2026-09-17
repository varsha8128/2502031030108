<?php

session_start();

include 'config/database.php';


// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];


// ================= FETCH ADDRESS =================

$address_sql = "SELECT *
                FROM addresses
                WHERE user_id = $user_id
                ORDER BY id DESC
                LIMIT 1";

$address_result = mysqli_query($conn, $address_sql);

$address = null;

if (mysqli_num_rows($address_result) > 0) {

    $address = mysqli_fetch_assoc($address_result);

}


// ================= FETCH CART =================

$sql = "SELECT cart.id AS cart_id,
               cart.quantity,
               products.id AS product_id,
               products.name,
               products.price,
               products.discount,
               products.stock

        FROM cart

        INNER JOIN products
        ON cart.product_id = products.id

        WHERE cart.user_id = $user_id";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0) {

    header("Location: cart.php");
    exit();

}


$grand_total = 0;

$cart_items = [];


// ================= CALCULATE TOTAL =================

while ($item = mysqli_fetch_assoc($result)) {

    $price = $item['price'];

    $discount = $item['discount'];

    $discount_amount = ($price * $discount) / 100;

    $final_price = $price - $discount_amount;

    $subtotal = $final_price * $item['quantity'];

    $grand_total += $subtotal;

    $item['final_price'] = $final_price;

    $item['subtotal'] = $subtotal;

    $cart_items[] = $item;

}


// ================= PLACE ORDER =================

if (isset($_POST['place_order'])) {

    if (!$address) {

        die("Please add delivery address first.");

    }

    // Start database transaction
    mysqli_begin_transaction($conn);

    try {

        // ================= CHECK STOCK =================

        foreach ($cart_items as $item) {

            $check_stock_sql = "SELECT stock
                                FROM products
                                WHERE id = ?
                                FOR UPDATE";

            $check_stock_stmt = mysqli_prepare(
                $conn,
                $check_stock_sql
            );

            mysqli_stmt_bind_param(
                $check_stock_stmt,
                "i",
                $item['product_id']
            );

            mysqli_stmt_execute($check_stock_stmt);

            $stock_result = mysqli_stmt_get_result(
                $check_stock_stmt
            );

            $stock_data = mysqli_fetch_assoc(
                $stock_result
            );

            mysqli_stmt_close($check_stock_stmt);


            // Product not found
            if (!$stock_data) {

                throw new Exception(
                    "Product not found."
                );

            }


            // Check available stock
            if (
                $item['quantity']
                > $stock_data['stock']
            ) {

                throw new Exception(
                    "Sorry, " .
                    $item['name'] .
                    " has only " .
                    $stock_data['stock'] .
                    " items available."
                );

            }

        }


        // ================= CREATE ORDER =================

        $order_sql = "INSERT INTO orders
                      (user_id, total_amount, status)
                      VALUES (?, ?, 'Pending')";

        $order_stmt = mysqli_prepare(
            $conn,
            $order_sql
        );

        mysqli_stmt_bind_param(
            $order_stmt,
            "id",
            $user_id,
            $grand_total
        );

        if (!mysqli_stmt_execute($order_stmt)) {

            throw new Exception(
                "Unable to create order."
            );

        }


        // Get newly created order ID
        $order_id = mysqli_insert_id($conn);

        mysqli_stmt_close($order_stmt);


        // ================= ADD ORDER ITEMS =================

        foreach ($cart_items as $item) {

            $item_sql = "INSERT INTO order_items
                         (order_id, product_id, quantity, price)
                         VALUES (?, ?, ?, ?)";

            $item_stmt = mysqli_prepare(
                $conn,
                $item_sql
            );

            mysqli_stmt_bind_param(
                $item_stmt,
                "iiid",
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['final_price']
            );

            if (!mysqli_stmt_execute($item_stmt)) {

                mysqli_stmt_close($item_stmt);

                throw new Exception(
                    "Unable to add order items."
                );

            }

            mysqli_stmt_close($item_stmt);


            // ================= REDUCE STOCK =================

            $stock_sql = "UPDATE products
                          SET stock = stock - ?
                          WHERE id = ?
                          AND stock >= ?";

            $stock_stmt = mysqli_prepare(
                $conn,
                $stock_sql
            );

            mysqli_stmt_bind_param(
                $stock_stmt,
                "iii",
                $item['quantity'],
                $item['product_id'],
                $item['quantity']
            );

            if (!mysqli_stmt_execute($stock_stmt)) {

                mysqli_stmt_close($stock_stmt);

                throw new Exception(
                    "Unable to update product stock."
                );

            }


            // Make sure stock was actually updated
            if (mysqli_stmt_affected_rows($stock_stmt) == 0) {

                mysqli_stmt_close($stock_stmt);

                throw new Exception(
                    "Stock is no longer available for " .
                    $item['name'] .
                    "."
                );

            }

            mysqli_stmt_close($stock_stmt);

        }


        // ================= EMPTY CART =================

        $clear_cart_sql = "DELETE FROM cart
                           WHERE user_id = ?";

        $clear_cart_stmt = mysqli_prepare(
            $conn,
            $clear_cart_sql
        );

        mysqli_stmt_bind_param(
            $clear_cart_stmt,
            "i",
            $user_id
        );

        if (!mysqli_stmt_execute($clear_cart_stmt)) {

            mysqli_stmt_close($clear_cart_stmt);

            throw new Exception(
                "Unable to clear cart."
            );

        }

        mysqli_stmt_close($clear_cart_stmt);


        // ================= COMMIT =================

        mysqli_commit($conn);


        // ================= REDIRECT =================

        header(
            "Location: payment.php?order_id=" .
            $order_id
        );

        exit();


    } catch (Exception $e) {

        // Undo everything if any operation fails
        mysqli_rollback($conn);

        echo "<script>
                alert(" .
                json_encode($e->getMessage()) .
                ");
                window.location.href = 'cart.php';
              </script>";

        exit();

    }

}


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Checkout - FreshMart</title>

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

    </nav>

</header>


<!-- ================= CHECKOUT ================= -->

<section class="checkout-section">

    <h1>Checkout 📦</h1>


    <div class="checkout-container">


        <!-- ================= ADDRESS ================= -->

        <div class="checkout-address">

            <h2>Delivery Address</h2>


            <?php if ($address): ?>

                <div class="saved-address">

                    <h3>
                        <?php echo htmlspecialchars($address['full_name']); ?>
                    </h3>

                    <p>
                        📞 <?php echo htmlspecialchars($address['phone']); ?>
                    </p>

                    <p>
                        <?php echo htmlspecialchars($address['address']); ?>
                    </p>

                    <p>
                        <?php echo htmlspecialchars($address['city']); ?>,
                        <?php echo htmlspecialchars($address['state']); ?>
                        -
                        <?php echo htmlspecialchars($address['pincode']); ?>
                    </p>

                </div>

            <?php else: ?>

                <p>
                    No delivery address found.
                </p>

                <a href="address.php">
                    + Add Delivery Address
                </a>

            <?php endif; ?>

        </div>


        <!-- ================= ORDER SUMMARY ================= -->

        <div class="order-summary">

            <h2>Order Summary</h2>


            <?php foreach ($cart_items as $item): ?>

                <div class="summary-item">

                    <div>

                        <h3>
                            <?php echo htmlspecialchars($item['name']); ?>
                        </h3>

                        <p>
                            Quantity:
                            <?php echo $item['quantity']; ?>
                        </p>

                    </div>


                    <div>

                        ₹<?php echo number_format($item['subtotal'], 2); ?>

                    </div>

                </div>

            <?php endforeach; ?>


            <hr>


            <div class="summary-total">

                <strong>
                    Total Amount
                </strong>

                <strong>
                    ₹<?php echo number_format($grand_total, 2); ?>
                </strong>

            </div>


            <?php if ($address): ?>

            <form method="POST" action="">

                <button
                type="submit"
                name="place_order"
                class="place-order-btn"
    >
                📦 Place Order
                </button>

            </form>

            <?php endif; ?>


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