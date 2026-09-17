<?php

session_start();

include 'config/database.php';


// ================= SEARCH + CATEGORY FILTER =================

$category_id = isset($_GET['category'])
    ? intval($_GET['category'])
    : 0;

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : '';


// ================= BASE QUERY =================

$sql = "SELECT products.*,
               categories.name AS category_name

        FROM products

        LEFT JOIN categories
        ON products.category_id = categories.id

        WHERE 1=1";


// ================= CATEGORY FILTER =================

if ($category_id > 0) {

    $sql .= " AND products.category_id = $category_id";

}


// ================= SEARCH FILTER =================

if ($search !== '') {

    $safe_search = mysqli_real_escape_string(
        $conn,
        $search
    );

    $sql .= " AND (
                products.name LIKE '%$safe_search%'
                OR categories.name LIKE '%$safe_search%'
              )";

}


// ================= ORDER =================

$sql .= " ORDER BY products.id DESC";


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

    <title>Products - FreshMart</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>


<!-- ================= NAVBAR ================= -->

<header class="navbar">

    <div class="logo">
        🛒 FreshMart
    </div>

    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="categories.php">
            Categories
        </a>

        <a href="products.php">
            Products
        </a>

        <a href="wishlist.php">
            Wishlist ❤️
        </a>

        <a href="cart.php">
            Cart 🛒
        </a>


        <?php if (isset($_SESSION['user_id'])): ?>

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

            $profile_result = mysqli_query(
                $conn,
                $profile_sql
            );


            if (
                $profile_result &&
                mysqli_num_rows($profile_result) > 0
            ) {

                $profile_user =
                    mysqli_fetch_assoc(
                        $profile_result
                    );


                if (
                    !empty(
                        $profile_user['profile_image']
                    )
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
                    echo htmlspecialchars(
                        $profile_image
                    );
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
                Login / Register
            </a>

        <?php endif; ?>

    </nav>

</header>



<!-- ================================================= -->
<!-- ================= PRODUCT LISTING ================= -->
<!-- ================================================= -->

<section class="section">

    <h1>
        <?php if ($category_id > 0): ?>

            Products 🛒

        <?php else: ?>

            All Products 🛒

        <?php endif; ?>

    </h1>


    <!-- ================= SEARCH ================= -->

    <div class="product-search">

        <form
            action="products.php"
            method="GET"
        >


            <?php if ($category_id > 0): ?>

                <input
                    type="hidden"
                    name="category"
                    value="<?php echo $category_id; ?>"
                >

            <?php endif; ?>


            <input
                type="text"
                name="search"
                value="<?php
                    echo htmlspecialchars($search);
                ?>"
                placeholder="Search products..."
            >


            <button type="submit">
                Search 🔍
            </button>


            <?php if ($search !== ''): ?>

                <a
                    href="<?php
                        echo $category_id > 0
                            ? 'products.php?category=' . $category_id
                            : 'products.php';
                    ?>"
                    class="clear-search-btn"
                >
                    Clear ❌
                </a>

            <?php endif; ?>


        </form>

    </div>



    <!-- ================= PRODUCTS ================= -->

    <div class="product-container">


        <?php if (
            $result &&
            mysqli_num_rows($result) > 0
        ): ?>


            <?php while (
                $product =
                mysqli_fetch_assoc($result)
            ): ?>


                <div class="product-card">


                    <!-- ================= PRODUCT IMAGE ================= -->

                    <div class="product-image">

                        <?php if (
                            !empty($product['image'])
                        ): ?>

                            <img
                                src="uploads/<?php
                                echo htmlspecialchars(
                                    $product['image']
                                );
                                ?>"
                                alt="<?php
                                echo htmlspecialchars(
                                    $product['name']
                                );
                                ?>"
                            >

                        <?php else: ?>

                            🛒

                        <?php endif; ?>

                    </div>



                    <!-- ================= PRODUCT NAME ================= -->

                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $product['name']
                        );
                        ?>

                    </h3>



                    <!-- ================= CATEGORY ================= -->

                    <p class="product-category">

                        <?php
                        echo htmlspecialchars(
                            $product['category_name']
                            ?? 'Uncategorized'
                        );
                        ?>

                    </p>



                    <!-- ================= DESCRIPTION ================= -->

                    <p class="product-description">

                        <?php

                        $description =
                            $product['description']
                            ?? 'Fresh and quality product from FreshMart.';

                        echo htmlspecialchars(
                            strlen($description) > 80
                                ? substr($description, 0, 80) . '...'
                                : $description
                        );

                        ?>

                    </p>



                    <!-- ================= PRICE ================= -->

                    <?php

                    $price = (float) $product['price'];

                    $discount = (float) $product['discount'];

                    $discount_amount =
                        ($price * $discount) / 100;

                    $final_price =
                        $price - $discount_amount;

                    ?>


                    <p class="price">

                        ₹<?php
                        echo number_format(
                            $final_price,
                            2
                        );
                        ?>


                        <?php if ($discount > 0): ?>

                            <span class="old-price">

                                ₹<?php
                                echo number_format(
                                    $price,
                                    2
                                );
                                ?>

                            </span>

                        <?php endif; ?>

                    </p>



                    <!-- ================= DISCOUNT ================= -->

                    <?php if ($discount > 0): ?>

                        <span class="discount-badge">

                            <?php
                            echo number_format(
                                $discount,
                                0
                            );
                            ?>% OFF

                        </span>

                    <?php endif; ?>



                    <!-- ================= STOCK ================= -->

                    <?php if ($product['stock'] > 0): ?>

                        <p class="stock available">

                            ✅
                            <?php
                            echo $product['stock'];
                            ?>
                            in stock

                        </p>

                    <?php else: ?>

                        <p class="stock out-of-stock">

                            ❌ Out of Stock

                        </p>

                    <?php endif; ?>



                    <!-- ================= ACTION BUTTONS ================= -->

                    <div class="product-actions">


                        <!-- VIEW DETAILS -->

                        <a
                            href="product.php?id=<?php
                            echo $product['id'];
                            ?>"
                            class="view-details-btn"
                        >

                            View Details

                        </a>



                        <!-- ADD TO CART -->

                        <?php if ($product['stock'] > 0): ?>
                           

                        <?php else: ?>

                            <button
                                type="button"
                                class="add-cart-btn disabled"
                                disabled
                            >

                                Out of Stock

                            </button>

                        <?php endif; ?>


                    </div>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <p>
                No products available.
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
                Quick Links
            </h3>

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

            <h3>
                Customer Support
            </h3>

            <a href="#">
                Contact Us
            </a>

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

            <h3>
                Contact Us
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