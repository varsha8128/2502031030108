<?php
session_start();
include 'config/database.php';

/* ================= CATEGORIES ================= */
$category_sql = "SELECT * FROM categories ORDER BY id ASC LIMIT 6";
$category_result = mysqli_query($conn, $category_sql);

/* ================= FEATURED PRODUCTS ================= */
$featured_sql = "SELECT products.*, categories.name AS category_name
                 FROM products
                 LEFT JOIN categories
                 ON products.category_id = categories.id
                 ORDER BY products.id ASC
                 LIMIT 8";
$featured_result = mysqli_query($conn, $featured_sql);

/* ================= LATEST PRODUCTS ================= */
$latest_sql = "SELECT products.*, categories.name AS category_name
               FROM products
               LEFT JOIN categories
               ON products.category_id = categories.id
               ORDER BY products.created_at DESC
               LIMIT 4";
$latest_result = mysqli_query($conn, $latest_sql);

/* ================= SPECIAL OFFERS ================= */
$offer_sql = "SELECT products.*, categories.name AS category_name
              FROM products
              LEFT JOIN categories
              ON products.category_id = categories.id
              WHERE products.discount > 0
              ORDER BY products.discount DESC
              LIMIT 4";
$offer_result = mysqli_query($conn, $offer_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FreshMart - Grocery Store</title>

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
        <a href="about.php">About Us</a>

        <a href="wishlist.php">Wishlist ❤️</a>

        <a href="cart.php">Cart 🛒</a>


        <?php if (isset($_SESSION['user_id'])): ?>

            <!-- Logged-in User -->

            <a href="orders.php">
                My Orders 📦
            </a>


            <?php

            $profile_image = 'default-profile.png';

            $profile_user = [
                'name' => 'Profile'
            ];

            $user_id = (int) $_SESSION['user_id'];

            $profile_sql = "SELECT name, profile_image
                            FROM users
                            WHERE id = $user_id";

            $profile_result = mysqli_query($conn, $profile_sql);


            if ($profile_result && mysqli_num_rows($profile_result) > 0) {

                $profile_user = mysqli_fetch_assoc($profile_result);

                if (!empty($profile_user['profile_image'])) {

                    $profile_image = $profile_user['profile_image'];

                }

            }

            ?>


            <!-- Profile -->

            <a href="profile.php" class="nav-profile">

                <img
                    src="uploads/profile/<?php echo htmlspecialchars($profile_image); ?>"
                    alt="Profile"
                    class="nav-profile-img"
                >

                <span>
                    <?php echo htmlspecialchars($profile_user['name']); ?>
                </span>

            </a>


        <?php else: ?>

            <!-- Guest User -->

            <a href="login.php">
                Login / Register
            </a>

        <?php endif; ?>

    </nav>

</header>


<!-- ================= SEARCH BAR ================= -->

<section class="search-section">

    <form action="products.php" method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search for groceries, fruits, vegetables..."
            required
        >

        <button type="submit">
            Search 🔍
        </button>

    </form>

</section>


<!-- ================= CAROUSEL ================= -->

<section class="carousel">

    <!-- Slide 1 -->

    <div
        class="carousel-slide active"
        style="background-image: url('uploads/freshmart_banner1.png');"
    >

        <a
            href="products.php"
            class="banner-click banner-one"
        ></a>

    </div>


    <!-- Slide 2 -->

    <div
        class="carousel-slide"
        style="background-image: url('uploads/freshmart_banner2.png?v=1');"
    >

        <a
            href="products.php?category=1"
            class="banner-click banner-two"
        ></a>

    </div>


    <!-- Slide 3 -->

    <div
        class="carousel-slide"
        style="background-image: url('uploads/freshmart_banner3.png?v=3');"
    >

        <a
            href="products.php?category=2"
            class="banner-click banner-three"
        ></a>

    </div>


    <button
        class="carousel-prev"
        onclick="changeSlide(-1)"
    >
        ❮
    </button>


    <button
        class="carousel-next"
        onclick="changeSlide(1)"
    >
        ❯
    </button>


    <div class="carousel-dots">

        <span
            class="dot active"
            onclick="currentSlide(1)"
        ></span>

        <span
            class="dot"
            onclick="currentSlide(2)"
        ></span>

        <span
            class="dot"
            onclick="currentSlide(3)"
        ></span>

    </div>

</section>

<<!-- ================= CATEGORIES ================= -->

<section id="categories" class="section">

<h2>Shop By Category</h2>

<div class="category-container">

    <?php if ($category_result && mysqli_num_rows($category_result) > 0): ?>

        <?php while ($category = mysqli_fetch_assoc($category_result)): ?>

            <a href="products.php?category=<?php echo $category['id']; ?>"
               class="category-link">

                <div class="category-card">

                    <div class="category-icon">

                        <?php
                        $category_icons = [
                            'Fruits' => '🍎',
                            'Vegetables' => '🥦',
                            'Dairy' => '🥛',
                            'Snacks' => '🍪',
                            'Staples' => '🍚',
                            'Personal Care' => '🧴'
                        ];

                        echo $category_icons[$category['name']] ?? '🛒';
                        ?>

                    </div>

                    <h3>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </h3>

                    <p>
                        Explore <?php echo htmlspecialchars($category['name']); ?>
                    </p>

                </div>

            </a>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No categories available.</p>

    <?php endif; ?>

</div>

</section>


<!-- ================================================= -->
<!-- =============== FEATURED PRODUCTS =============== -->
<!-- ================================================= -->

<section id="products" class="section">

    <h2>Featured Products</h2>


    <div class="product-container">


        <?php if ($featured_result && mysqli_num_rows($featured_result) > 0): ?>


            <?php while ($product = mysqli_fetch_assoc($featured_result)): ?>


                <a
                    href="product.php?id=<?php echo $product['id']; ?>"
                    class="product-link"
                >

                    <div class="product-card">


                        <div class="product-image">


                            <?php if (!empty($product['image'])): ?>

                                <img
                                    src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                >

                            <?php endif; ?>


                        </div>


                        <h3>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>


                        <p>
                            <?php echo htmlspecialchars($product['category_name']); ?>
                        </p>


                        <p class="price">
                            ₹<?php echo number_format($product['price'], 2); ?>
                        </p>


                        <button type="button">
                            View Product
                        </button>


                    </div>

                </a>


            <?php endwhile; ?>


        <?php else: ?>

            <p>No featured products available.</p>

        <?php endif; ?>


    </div>

</section>


<!-- ================================================= -->
<!-- ================= SPECIAL OFFERS ================= -->
<!-- ================================================= -->

<section class="section offers-section">

    <h2>🔥 Special Offers</h2>

    <div class="product-container">


        <?php if ($offer_result && mysqli_num_rows($offer_result) > 0): ?>


            <?php while ($product = mysqli_fetch_assoc($offer_result)): ?>


                <a
                    href="product.php?id=<?php echo $product['id']; ?>"
                    class="product-link"
                >

                    <div class="product-card offer-card">


                        <div class="discount-badge">

                            <?php echo $product['discount']; ?>% OFF

                        </div>


                        <div class="product-image">

                            <?php if (!empty($product['image'])): ?>

                                <img
                                    src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                >

                            <?php endif; ?>

                        </div>


                        <h3>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>


                        <p>
                            <?php echo htmlspecialchars($product['category_name']); ?>
                        </p>


                        <?php

                        $discount_price =
                            $product['price']
                            -
                            ($product['price'] * $product['discount'] / 100);

                        ?>


                        <p>

                            <span class="old-price">

                                ₹<?php echo number_format($product['price'], 2); ?>

                            </span>


                            <span class="offer-price">

                                ₹<?php echo number_format($discount_price, 2); ?>

                            </span>

                        </p>


                        <button type="button">
                            View Offer
                        </button>


                    </div>

                </a>


            <?php endwhile; ?>


        <?php else: ?>

            <p>No special offers available.</p>

        <?php endif; ?>


    </div>

</section>


<!-- ================================================= -->
<!-- ================= LATEST PRODUCTS ================ -->
<!-- ================================================= -->

<section class="section latest-section">

    <h2>🆕 New & Latest Products</h2>


    <div class="product-container">


        <?php if ($latest_result && mysqli_num_rows($latest_result) > 0): ?>


            <?php while ($product = mysqli_fetch_assoc($latest_result)): ?>


                <a
                    href="product.php?id=<?php echo $product['id']; ?>"
                    class="product-link"
                >

                    <div class="product-card">


                        <div class="latest-badge">
                            NEW
                        </div>


                        <div class="product-image">

                            <?php if (!empty($product['image'])): ?>

                                <img
                                    src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                                >

                            <?php endif; ?>

                        </div>


                        <h3>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>


                        <p>
                            <?php echo htmlspecialchars($product['category_name']); ?>
                        </p>


                        <p class="price">

                            ₹<?php echo number_format($product['price'], 2); ?>

                        </p>


                        <button type="button">
                            View Product
                        </button>


                    </div>

                </a>


            <?php endwhile; ?>


        <?php else: ?>

            <p>No latest products available.</p>

        <?php endif; ?>


    </div>

</section>


<!-- ================================================= -->
<!-- ===================== FOOTER ===================== -->
<!-- ================================================= -->

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

            <a href="index.php">
                Home
            </a>

            <a href="categories.php">
                Categories
            </a>

            <a href="products.php">
                Products
            </a>

            <a href="cart.php">
                Cart
            </a>

            <a href="orders.php">
                My Orders
            </a>

            <a href="wishlist.php">
                Wishlist ❤️
            </a>

        </div>


        <!-- Customer Support -->

        <div class="footer-column">

            <h3>Customer Support</h3>

            <a href="contact.php">Contact Us</a>

            <a href="#">
                FAQs
            </a>

            <a href="#">
                Privacy Policy
            </a>

            <a href="#">
                Terms & Conditions
            </a>

        </div>


        <!-- Contact -->

        <div class="footer-column">

            <h3>Contact Us</h3>

            <p>
                📧 support@freshmart.com
            </p>

            <p>
                📱 +91 98765 43210
            </p>

            <p>
                📍 India
            </p>

        </div>


    </div>


    <div class="footer-bottom">

        <p>
            © 2026 FreshMart Grocery Store.
            All Rights Reserved.
        </p>

    </div>


</footer>


<script src="js/script.js"></script>

</body>

</html>