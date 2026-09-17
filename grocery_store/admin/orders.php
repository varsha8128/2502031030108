<?php

session_start();

include '../config/database.php';


// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {

    header("Location: admin_login.php");
    exit();

}


// ================= UPDATE ORDER STATUS =================

if (isset($_POST['update_status'])) {

    $order_id = intval($_POST['order_id']);
    $status = trim($_POST['status']);

    $allowed_status = [
        'Pending',
        'Confirmed',
        'Processing',
        'Shipped',
        'Delivered',
        'Cancelled',
        
    ];

    if (in_array($status, $allowed_status)) {

        $update_sql = "UPDATE orders
                       SET status = ?
                       WHERE id = ?";

        $stmt = mysqli_prepare(
            $conn,
            $update_sql
        );

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $status,
            $order_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    header("Location: orders.php");
    exit();

}


// ================= FETCH ORDERS =================

$sql = "SELECT orders.id,
               orders.total_amount,
               orders.status,
               orders.order_date,
               users.name,
               users.email

        FROM orders

        INNER JOIN users
        ON orders.user_id = users.id

        ORDER BY orders.id DESC";

$result = mysqli_query($conn, $sql);

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
        Manage Orders - FreshMart Admin
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


<!-- ================= ORDERS ================= -->

<section class="admin-orders">

    <h1>
        Manage Orders 📦
    </h1>


    <?php if (mysqli_num_rows($result) > 0): ?>

        <div class="admin-order-table">

            <table>

                <thead>

                    <tr>

                        <th>
                            Order ID
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php while (
                        $order = mysqli_fetch_assoc($result)
                    ): ?>

                        <tr>

                            <td>
                                #<?php
                                echo $order['id'];
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $order['name']
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $order['email']
                                );
                                ?>
                            </td>


                            <td>

                                ₹<?php
                                echo number_format(
                                    $order['total_amount'],
                                    2
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $order['order_date']
                                );
                                ?>

                            </td>


                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $order['status']
                                    );
                                    ?>

                                </strong>

                            </td>


                            <td>

                                <form
                                    method="POST"
                                    action=""
                                >

                                    <input
                                        type="hidden"
                                        name="order_id"
                                        value="<?php
                                        echo $order['id'];
                                        ?>"
                                    >


                                    <select
                                        name="status"
                                        required
                                    >

                                        <option
                                            value="Pending"
                                            <?php
                                            if (
                                                $order['status']
                                                == 'Pending'
                                            ) {
                                                echo 'selected';
                                            }
                                            ?>
                                        >
                                            Pending
                                        </option>


                                        <option
                                            value="Confirmed"
                                            <?php
                                            if (
                                                $order['status']
                                                == 'Confirmed'
                                            ) {
                                                echo 'selected';
                                            }
                                            ?>
                                        >
                                            Confirmed
                                        </option>


                                        <option
                                            value="Shipped"
                                            <?php
                                            if (
                                                $order['status']
                                                == 'Shipped'
                                            ) {
                                                echo 'selected';
                                            }
                                            ?>
                                        >
                                            Shipped
                                        </option>


                                        <option
                                            value="Delivered"
                                            <?php
                                            if (
                                                $order['status']
                                                == 'Delivered'
                                            ) {
                                                echo 'selected';
                                            }
                                            ?>
                                        >
                                            Delivered
                                        </option>


                                        <option
                                            value="Cancelled"
                                            <?php
                                            if (
                                                $order['status']
                                                == 'Cancelled'
                                            ) {
                                                echo 'selected';
                                            }
                                            ?>
                                        >
                                            Cancelled
                                        </option>

                                        <option
                                            value="Processing"
                                            <?php
                                            if (
                                                $order['status']
                                                == 'Processing'
                                            ) {
                                                echo 'selected';
                                            }
                                            ?>
                                        >
                                            Processing
                                        </option>

                                    </select>


                                    <button
                                        type="submit"
                                        name="update_status"
                                    >
                                        Update
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="no-orders">

            <h2>
                No Orders Found
            </h2>

            <p>
                Customers have not placed any orders yet.
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