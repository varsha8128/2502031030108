<?php

session_start();

include 'config/database.php';


// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];

$message = "";


// ================= SAVE ADDRESS =================

if (isset($_POST['save_address'])) {

    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $pincode = trim($_POST['pincode']);


    if (
        empty($full_name) ||
        empty($phone) ||
        empty($address) ||
        empty($city) ||
        empty($state) ||
        empty($pincode)
    ) {

        $message = "Please fill all fields.";

    } else {

        $sql = "INSERT INTO addresses
                (user_id, full_name, phone, address, city, state, pincode)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "issssss",
            $user_id,
            $full_name,
            $phone,
            $address,
            $city,
            $state,
            $pincode
        );


        if (mysqli_stmt_execute($stmt)) {

            header("Location: checkout.php");
            exit();

        } else {

            $message = "Unable to save address.";

        }

        mysqli_stmt_close($stmt);

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Address - FreshMart</title>

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

        <a href="checkout.php">Checkout</a>

    </nav>

</header>


<!-- ================= ADDRESS FORM ================= -->

<section class="address-section">

    <div class="address-container">

        <h1>Delivery Address 📍</h1>

        <p>Enter your delivery details</p>


        <?php if (!empty($message)): ?>

            <div class="address-message">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="">


            <!-- Full Name -->

            <label for="full_name">
                Full Name
            </label>

            <input
                type="text"
                id="full_name"
                name="full_name"
                placeholder="Enter full name"
                required
            >


            <!-- Phone -->

            <label for="phone">
                Phone Number
            </label>

            <input
                type="tel"
                id="phone"
                name="phone"
                placeholder="Enter phone number"
                required
            >


            <!-- Address -->

            <label for="address">
                Address
            </label>

            <textarea
                id="address"
                name="address"
                rows="4"
                placeholder="House no., Street, Area"
                required
            ></textarea>


            <!-- City -->

            <label for="city">
                City
            </label>

            <input
                type="text"
                id="city"
                name="city"
                placeholder="Enter city"
                required
            >


            <!-- State -->

            <label for="state">
                State
            </label>

            <input
                type="text"
                id="state"
                name="state"
                placeholder="Enter state"
                required
            >


            <!-- Pincode -->

            <label for="pincode">
                Pincode
            </label>

            <input
                type="text"
                id="pincode"
                name="pincode"
                placeholder="Enter pincode"
                required
            >


            <!-- Save -->

            <button 
    type="submit" 
    name="save_address"
    class="save-address-btn"
>
    📍 Save Address
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