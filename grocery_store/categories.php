<?php

session_start();

include 'config/database.php';


// ================= FETCH CATEGORIES =================

$sql = "SELECT *
        FROM categories
        ORDER BY id ASC";

$result = mysqli_query($conn, $sql);


// ================= CATEGORY EMOJIS =================

$category_emojis = [
    'fruit'       => '🍎',
    'fruits'      => '🍎',
    'vegetable'   => '🥦',
    'vegetables'  => '🥦',
    'dairy'       => '🥛',
    'snack'       => '🍪',
    'snacks'      => '🍪',
    'staple'      => '🌾',
    'staples'     => '🌾',
    'bakery'      => '🍞',
    'beverage'    => '🧃',
    'beverages'   => '🧃',
    'grocery'     => '🛒'
];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Categories - FreshMart</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

    <style>

        /* ================= CATEGORY PAGE ================= */

        .category-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-top: 30px;
        }

        .category-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.14);
        }

        .category-image {
            width: 100%;
            height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 12px;
            background: #f1f8f3;
            margin-bottom: 15px;
        }

        .category-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .category-emoji {
            font-size: 70px;
        }

        .category-card h3 {
            color: #2e8b57;
            font-size: 21px;
            margin: 12px 0 8px;
        }

        .category-card p {
            color: #666666;
            font-size: 14px;
            margin-bottom: 18px;
        }

        /* ================= VIEW PRODUCTS BUTTON ================= */

        .category-btn {
            display: inline-block;
            background: #2e8b57;
            color: #ffffff;
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .category-btn:hover {
            background: #246b45;
            transform: scale(1.04);
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 1000px) {

            .category-container {
                grid-template-columns: repeat(3, 1fr);
            }

        }

        @media (max-width: 700px) {

            .category-container {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 450px) {

            .category-container {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>


<!-- ================= NAVBAR ================= -->

<header class="navbar">

    <div class="logo">
        🛒 FreshMart
    </div>

    <nav>

        <a href="index.php">
            🏠 Home
        </a>

        <a href="categories.php" class="active">
            📂 Categories
        </a>

        <a href="products.php">
            🛍️ Products
        </a>

        <a href="wishlist.php">
            ❤️ Wishlist
        </a>

        <a href="cart.php">
            🛒 Cart
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>

            <a href="orders.php">
                📦 My Orders
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

            $profile_result = mysqli_query(
                $conn,
                $profile_sql
            );

            if (
                $profile_result &&
                mysqli_num_rows($profile_result) > 0
            ) {

                $profile_user = mysqli_fetch_assoc(
                    $profile_result
                );

                if (
                    !empty($profile_user['profile_image'])
                ) {

                    $profile_image =
                        $profile_user['profile_image'];

                }

            }

            ?>

            <a
                href="profile.php"
                class="nav-profile"
            >

                <img
                    src="uploads/profile/<?php
                    echo htmlspecialchars($profile_image);
                    ?>"
                    alt="Profile"
                    class="nav-profile-img"
                >

                <span>
                    <?php
                    echo htmlspecialchars(
                        $profile_user['name']
                    );
                    ?>
                </span>

            </a>

        <?php else: ?>

            <a href="login.php">
                👤 Login / Register
            </a>

        <?php endif; ?>

    </nav>

</header>


<!-- ================= CATEGORY SECTION ================= -->

<section class="section">

    <h1>
        🛍️ Shop by Category
    </h1>

    <div class="category-container">

        <?php if (
            $result &&
            mysqli_num_rows($result) > 0
        ): ?>

            <?php while (
                $category = mysqli_fetch_assoc($result)
            ): ?>

                <?php

                $category_name =
                    strtolower(trim($category['name']));

                $category_emoji = '🛒';

                foreach (
                    $category_emojis as $keyword => $emoji
                ) {

                    if (
                        strpos(
                            $category_name,
                            $keyword
                        ) !== false
                    ) {

                        $category_emoji = $emoji;
                        break;

                    }

                }

                $category_image =
                    trim($category['image'] ?? '');

                $image_path =
                    "uploads/" . $category_image;

                ?>

                <div class="category-card">

                    <!-- ================= CATEGORY IMAGE ================= -->

                    <div class="category-image">

                        <?php if (
                            !empty($category_image) &&
                            file_exists($image_path)
                        ): ?>

                            <img
                                src="<?php
                                echo htmlspecialchars($image_path);
                                ?>"
                                alt="<?php
                                echo htmlspecialchars(
                                    $category['name']
                                );
                                ?>"
                            >

                        <?php else: ?>

                            <div class="category-emoji">
                                <?php echo $category_emoji; ?>
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- ================= CATEGORY NAME ================= -->

                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $category['name']
                        );
                        ?>

                    </h3>


                    <!-- ================= CATEGORY DESCRIPTION ================= -->

                    <p>

                        <?php echo $category_emoji; ?>

                        Explore fresh
                        <?php
                        echo htmlspecialchars(
                            $category['name']
                        );
                        ?>

                    </p>


                    <!-- ================= BUTTON ================= -->

                    <a
                        href="products.php?category=<?php
                        echo (int) $category['id'];
                        ?>"
                        class="category-btn"
                    >

                        🛍️ View Products

                    </a>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <p>
                ❌ No categories available.
            </p>

        <?php endif; ?>

    </div>

</section>


<!-- ================= FOOTER ================= -->

<footer class="footer">

    <div class="footer-container">


        <!-- FreshMart -->

        <div class="footer-column">

            <h2>
                🛒 FreshMart
            </h2>

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

            <h3>
                🔗 Quick Links
            </h3>

            <a href="index.php">
                🏠 Home
            </a>

            <a href="categories.php">
                📂 Categories
            </a>

            <a href="products.php">
                🛍️ Products
            </a>

            <a href="cart.php">
                🛒 Cart
            </a>

            <a href="orders.php">
                📦 My Orders
            </a>

            <a href="wishlist.php">
                ❤️ Wishlist
            </a>

        </div>


        <!-- Customer Support -->

        <div class="footer-column">

            <h3>
                🧑‍💻 Customer Support
            </h3>

            <a href="contact.php">
                📞 Contact Us
            </a>

            <a href="#">
                ❓ FAQs
            </a>

            <a href="#">
                🔒 Privacy Policy
            </a>

            <a href="#">
                📄 Terms & Conditions
            </a>

        </div>


        <!-- Contact -->

        <div class="footer-column">

            <h3>
                📬 Contact Us
            </h3>

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


    <!-- COPYRIGHT -->

    <div class="footer-bottom">

        <p>
            © 2026 FreshMart Grocery Store.
            All Rights Reserved.
        </p>

    </div>

</footer>


</body>

</html>