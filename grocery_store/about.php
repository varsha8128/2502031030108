
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Us - FreshMart</title>

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

        <a href="wishlist.php">Wishlist ❤️</a>

        <a href="cart.php">Cart 🛒</a>

        <?php if (isset($_SESSION['user_id'])): ?>

            <a href="orders.php">My Orders 📦</a>
            <a href="profile.php">Profile 👤</a>

        <?php else: ?>

            <a href="login.php">Login</a>
            <a href="register.php">Register</a>

        <?php endif; ?>

    </nav>

</header>


<!-- ================= ABOUT US ================= -->

<section class="about-section">

    <div class="about-container">

        <h1>About FreshMart</h1>

        <p class="about-intro">
            FreshMart is an online grocery store designed to make
            everyday grocery shopping simple, convenient and reliable.
        </p>


        <!-- Company / Store -->

        <div class="about-card">

            <h2>🏪 Our Store</h2>

            <p>
                FreshMart provides customers with a convenient online
                platform where they can browse grocery products,
                add products to their cart and place orders from
                the comfort of their home.
            </p>

        </div>


        <!-- Business Concept -->

        <div class="about-card">

            <h2>💡 Business Concept</h2>

            <p>
                Our business concept is based on online grocery
                shopping. Customers can explore different categories,
                view product details, check prices and discounts,
                manage their cart and place orders online.
            </p>

        </div>


        <!-- Mission -->

        <div class="about-card">

            <h2>🎯 Our Mission</h2>

            <p>
                Our mission is to provide fresh and quality grocery
                products at reasonable prices while making grocery
                shopping easy, fast and convenient for customers.
            </p>

        </div>


        <!-- Vision -->

        <div class="about-card">

            <h2>🌟 Our Vision</h2>

            <p>
                Our vision is to become a trusted online grocery
                platform that provides a simple shopping experience,
                quality products and reliable service to customers.
            </p>

        </div>


        <!-- Products / Services -->

        <div class="about-card">

            <h2>🛒 Products & Services</h2>

            <p>
                FreshMart offers a variety of everyday grocery products,
                including:
            </p>

            <ul>

                <li>🍎 Fruits</li>

                <li>🥦 Vegetables</li>

                <li>🥛 Dairy Products</li>

                <li>🍪 Snacks</li>

                <li>🍚 Staples</li>

                <li>🧴 Personal Care Products</li>

            </ul>

            <p>
                Customers can also use services such as online cart
                management, wishlist, order placement, checkout and
                order history.
            </p>

        </div>

    </div>

</section>


<!-- ================= FOOTER ================= -->

<footer class="footer">

    <div class="footer-container">

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


        <div class="footer-column">

            <h3>Quick Links</h3>

            <a href="index.php">Home</a>

            <a href="about.php">About Us</a>

            <a href="categories.php">Categories</a>

            <a href="products.php">Products</a>

            <a href="cart.php">Cart</a>

            <a href="orders.php">My Orders</a>

            <a href="wishlist.php">Wishlist ❤️</a>

        </div>


        <div class="footer-column">

            <h3>Customer Support</h3>

            <a href="contact.php">Contact Us</a>

            <a href="#">FAQs</a>

            <a href="#">Privacy Policy</a>

            <a href="#">Terms & Conditions</a>

        </div>


        <div class="footer-column">

            <h3>Contact Us</h3>

            <p>📧 support@freshmart.com</p>

            <p>📱 +91 98765 43210</p>

            <p>📍 India</p>

        </div>

    </div>


    <div class="footer-bottom">

        <p>
            © 2026 FreshMart Grocery Store. All Rights Reserved.
        </p>

    </div>

</footer>

</body>

</html>

