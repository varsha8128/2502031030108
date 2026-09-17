<?php

session_start();

include 'config/database.php';


// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];


// ================= GET ORDER ID =================

if (!isset($_GET['order_id'])) {

    header("Location: index.php");
    exit();

}

$order_id = intval($_GET['order_id']);

if ($order_id <= 0) {

    header("Location: index.php");
    exit();

}


// ================= FETCH ORDER =================

$order_sql = "SELECT *
              FROM orders
              WHERE id = ?
              AND user_id = ?";

$order_stmt = mysqli_prepare($conn, $order_sql);

mysqli_stmt_bind_param(
    $order_stmt,
    "ii",
    $order_id,
    $user_id
);

mysqli_stmt_execute($order_stmt);

$order_result = mysqli_stmt_get_result($order_stmt);

if (mysqli_num_rows($order_result) == 0) {

    mysqli_stmt_close($order_stmt);

    die("Order not found.");

}

$order = mysqli_fetch_assoc($order_result);

mysqli_stmt_close($order_stmt);


// ================= PAYMENT =================

$message = "";

if (isset($_POST['pay_now'])) {

    $payment_method = trim($_POST['payment_method'] ?? '');

    // ================= VALID PAYMENT METHOD =================

    $allowed_methods = [
        "Cash on Delivery",
        "Online Payment"
    ];

    if (!in_array($payment_method, $allowed_methods, true)) {

        $message = "Please select a valid payment method.";

    } else {

        try {

            // Start transaction
            mysqli_begin_transaction($conn);


            // ================= CHECK EXISTING PAYMENT =================

            $check_payment_sql = "SELECT id
                                  FROM payments
                                  WHERE order_id = ?
                                  ORDER BY id DESC
                                  LIMIT 1";

            $check_payment_stmt = mysqli_prepare(
                $conn,
                $check_payment_sql
            );

            mysqli_stmt_bind_param(
                $check_payment_stmt,
                "i",
                $order_id
            );

            mysqli_stmt_execute($check_payment_stmt);

            $payment_result = mysqli_stmt_get_result(
                $check_payment_stmt
            );

            $existing_payment = mysqli_fetch_assoc(
                $payment_result
            );

            mysqli_stmt_close($check_payment_stmt);


            // ================= PAYMENT DETAILS =================

            if ($payment_method == "Cash on Delivery") {

                $payment_status = "Pending";
                $transaction_id = null;

            } else {

                // Simulated / Demo Online Payment
                $payment_status = "Paid";

                $transaction_id =
                    "DEMO" . date("YmdHis") . rand(1000, 9999);
            }


            // ================= INSERT / UPDATE PAYMENT =================

            if ($existing_payment) {

                // Existing payment record ko update karo

                $payment_id = $existing_payment['id'];

                $update_payment_sql = "UPDATE payments
                                       SET payment_method = ?,
                                           payment_status = ?,
                                           transaction_id = ?
                                       WHERE id = ?";

                $update_payment_stmt = mysqli_prepare(
                    $conn,
                    $update_payment_sql
                );

                mysqli_stmt_bind_param(
                    $update_payment_stmt,
                    "sssi",
                    $payment_method,
                    $payment_status,
                    $transaction_id,
                    $payment_id
                );

                if (!mysqli_stmt_execute($update_payment_stmt)) {

                    throw new Exception(
                        "Payment update failed."
                    );
                }

                mysqli_stmt_close($update_payment_stmt);

            } else {

                // New payment record create karo

                $insert_payment_sql = "INSERT INTO payments
                                       (
                                           order_id,
                                           payment_method,
                                           payment_status,
                                           transaction_id
                                       )
                                       VALUES (?, ?, ?, ?)";

                $insert_payment_stmt = mysqli_prepare(
                    $conn,
                    $insert_payment_sql
                );

                mysqli_stmt_bind_param(
                    $insert_payment_stmt,
                    "isss",
                    $order_id,
                    $payment_method,
                    $payment_status,
                    $transaction_id
                );

                if (!mysqli_stmt_execute($insert_payment_stmt)) {

                    throw new Exception(
                        "Payment could not be processed."
                    );
                }

                mysqli_stmt_close($insert_payment_stmt);
            }


            // ================= UPDATE ORDER STATUS =================

            $order_update_sql = "UPDATE orders
                                 SET status = 'Confirmed'
                                 WHERE id = ?
                                 AND user_id = ?";

            $order_update_stmt = mysqli_prepare(
                $conn,
                $order_update_sql
            );

            mysqli_stmt_bind_param(
                $order_update_stmt,
                "ii",
                $order_id,
                $user_id
            );

            if (!mysqli_stmt_execute($order_update_stmt)) {

                throw new Exception(
                    "Order status could not be updated."
                );
            }

            mysqli_stmt_close($order_update_stmt);


            // ================= COMMIT =================

            mysqli_commit($conn);


            // ================= SUCCESS =================

            header(
                "Location: order_success.php?order_id=" . $order_id
            );

            exit();


        } catch (Exception $e) {

            // Rollback if something goes wrong
            mysqli_rollback($conn);

            $message = "Payment could not be processed. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Payment - FreshMart</title>

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

        <a href="cart.php">Cart 🛒</a>

    </nav>

</header>


<!-- ================= PAYMENT ================= -->

<section class="payment-section">

    <div class="payment-container">

        <h1>Payment 💳</h1>

        <p>
            Order ID:
            <strong>#<?php echo $order_id; ?></strong>
        </p>

        <h2>
            Total:
            ₹<?php echo number_format($order['total_amount'], 2); ?>
        </h2>


        <?php if (!empty($message)): ?>

            <div class="payment-message">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="">


            <h3>
                Select Payment Method
            </h3>


            <label>

                <input
                    type="radio"
                    name="payment_method"
                    value="Cash on Delivery"
                    required
                >

                Cash on Delivery

            </label>


            <br><br>


            <label>

                <input
                    type="radio"
                    name="payment_method"
                    value="Online Payment"
                >

                Online Payment
                <small>(Demo)</small>

            </label>


            <br><br>


            <button
                type="submit"
                name="pay_now"
                class="payment-btn"
            >
                Confirm Payment
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