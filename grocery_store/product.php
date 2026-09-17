<?php

session_start();

include 'config/database.php';


// ================= PRODUCT ID =================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Product not found.");
}

$product_id = intval($_GET['id']);


// ================= ADD TO CART =================

if (isset($_POST['add_to_cart'])) {

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    } else {

        $user_id = intval($_SESSION['user_id']);
        $quantity = intval($_POST['quantity']);

        if ($quantity < 1) {
            $quantity = 1;
        }

        // Get current stock
        $stock_sql = "SELECT stock FROM products WHERE id = $product_id";
        $stock_result = mysqli_query($conn, $stock_sql);

        if ($stock_result && mysqli_num_rows($stock_result) > 0) {

            $stock_data = mysqli_fetch_assoc($stock_result);
            $available_stock = intval($stock_data['stock']);

            if ($quantity > $available_stock) {
                $quantity = $available_stock;
            }

            // Check whether product already exists in cart
            $check_sql = "SELECT id, quantity
                          FROM cart
                          WHERE user_id = $user_id
                          AND product_id = $product_id";

            $check_result = mysqli_query($conn, $check_sql);

            if ($check_result && mysqli_num_rows($check_result) > 0) {

                $cart_item = mysqli_fetch_assoc($check_result);
                $new_quantity = intval($cart_item['quantity']) + $quantity;

                if ($new_quantity > $available_stock) {
                    $new_quantity = $available_stock;
                }

                $update_sql = "UPDATE cart
                               SET quantity = $new_quantity
                               WHERE id = " . intval($cart_item['id']);

                mysqli_query($conn, $update_sql);

            } else {

                $insert_sql = "INSERT INTO cart
                               (user_id, product_id, quantity)
                               VALUES
                               ($user_id, $product_id, $quantity)";

                mysqli_query($conn, $insert_sql);
            }

            // Redirect to cart after adding product
            header("Location: cart.php");
            exit();

        } else {

            $login_message = "Product is no longer available.";
        }
    }
}


// ================= GET PRODUCT =================

$sql = "SELECT products.*, categories.name AS category_name
        FROM products
        LEFT JOIN categories
        ON products.category_id = categories.id
        WHERE products.id = $product_id";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Product not found.");
}

$product = mysqli_fetch_assoc($result);


// ================= PRICE CALCULATION =================

$price = (float) $product['price'];
$discount = (float) $product['discount'];

$discount_amount = ($price * $discount) / 100;

$final_price = $price - $discount_amount;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($product['name']); ?> - FreshMart
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

        <a href="categories.php">Categories</a>

        <a href="products.php">Products</a>

        <a href="wishlist.php">Wishlist ❤️</a>

        <a href="cart.php">Cart 🛒</a>

        <?php if (isset($_SESSION['user_id'])): ?>

            <a href="orders.php">My Orders 📦</a>

            <a href="profile.php">Profile 👤</a>

        <?php else: ?>

            <a href="login.php">Login / Register</a>

        <?php endif; ?>

    </nav>

</header>


<!-- ================= PRODUCT DETAILS ================= -->

<section class="product-details">


    <!-- PRODUCT IMAGE -->

    <div class="product-detail-image">

        <img
            src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
            alt="<?php echo htmlspecialchars($product['name']); ?>"
        >

    </div>


    <!-- PRODUCT INFORMATION -->

    <div class="product-detail-info">


        <!-- CATEGORY -->

        <p class="product-category">

            Category:
            <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>

        </p>


        <!-- PRODUCT NAME -->

        <h1>

            <?php echo htmlspecialchars($product['name']); ?>

        </h1>


        <!-- DESCRIPTION -->

        <p class="detail-description">

            <?php
            echo htmlspecialchars(
                !empty($product['description'])
                ? $product['description']
                : 'Fresh and quality product from FreshMart.'
            );
            ?>

        </p>


        <!-- PRICE -->

        <?php if ($discount > 0): ?>

            <div class="detail-price-section">

                <h2 class="detail-price">

                    ₹<?php echo number_format($final_price, 2); ?>

                </h2>

                <span class="old-price">

                    ₹<?php echo number_format($price, 2); ?>

                </span>

                <span class="discount-badge">

                    <?php echo number_format($discount, 0); ?>% OFF

                </span>

            </div>

        <?php else: ?>

            <h2 class="detail-price">

                ₹<?php echo number_format($price, 2); ?>

            </h2>

        <?php endif; ?>


        <!-- STOCK -->

        <p class="stock">

            <?php if ($product['stock'] > 0): ?>

                <span class="stock available">

                    ✅ In Stock
                    (<?php echo intval($product['stock']); ?> available)

                </span>

            <?php else: ?>

                <span class="stock out-of-stock">

                    ❌ Out of Stock

                </span>

            <?php endif; ?>

        </p>


        <!-- MESSAGES -->

        <?php if (isset($login_message)): ?>

            <p class="message">

                <?php echo htmlspecialchars($login_message); ?>

            </p>

        <?php endif; ?>


        <?php if (isset($cart_message)): ?>

            <p class="cart-success-message">

                ✅ <?php echo htmlspecialchars($cart_message); ?>

            </p>

        <?php endif; ?>


        <!-- QUANTITY + CART -->

        <?php if ($product['stock'] > 0): ?>

            <form method="POST" action="" class="product-cart-form">


                <div class="quantity">

                    <label for="quantity">

                        Quantity:

                    </label>

                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        value="1"
                        min="1"
                        max="<?php echo intval($product['stock']); ?>"
                        required
                    >

                </div>


                <div class="product-detail-actions">

                    <button
                        type="submit"
                        name="add_to_cart"
                        class="add-cart-btn"
                        id="addCartBtn"
                    >

                        🛒 Add to Cart

                    </button>


                    <a
                        href="wishlist_add.php?id=<?php echo intval($product['id']); ?>"
                        class="wishlist-btn"
                    >

                        ❤️ Add to Wishlist

                    </a>

                </div>


            </form>

        <?php else: ?>

            <button
                type="button"
                class="add-cart-btn disabled"
                disabled
            >

                ❌ Out of Stock

            </button>

        <?php endif; ?>


        <!-- PRODUCT SPECIFICATIONS -->

        <div class="product-specifications">

            <h3>Product Information</h3>

            <p>
                <strong>Product:</strong>
                <?php echo htmlspecialchars($product['name']); ?>
            </p>

            <p>
                <strong>Category:</strong>
                <?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?>
            </p>

            <p>
                <strong>Availability:</strong>

                <?php if ($product['stock'] > 0): ?>

                    In Stock

                <?php else: ?>

                    Out of Stock

                <?php endif; ?>

            </p>

        </div>


        <!-- BACK BUTTON -->

        <br>

        <a href="products.php" class="back-btn">

            ← Back to Products

        </a>

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


    <!-- COPYRIGHT -->

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