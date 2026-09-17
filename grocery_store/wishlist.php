<?php

session_start();

include 'config/database.php';


// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];


// ================= REMOVE FROM WISHLIST =================

if (isset($_GET['remove'])) {

    $product_id = intval($_GET['remove']);

    $delete_sql = "DELETE FROM wishlist
                   WHERE user_id = $user_id
                   AND product_id = $product_id";

    mysqli_query($conn, $delete_sql);

    header("Location: wishlist.php");
    exit();

}


// ================= FETCH WISHLIST =================

$sql = "SELECT wishlist.product_id,
               products.name,
               products.description,
               products.price,
               products.discount,
               products.stock,
               products.image,
               categories.name AS category_name

        FROM wishlist

        INNER JOIN products
        ON wishlist.product_id = products.id

        LEFT JOIN categories
        ON products.category_id = categories.id

        WHERE wishlist.user_id = $user_id

        ORDER BY wishlist.id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Wishlist - FreshMart</title>

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

        <a href="index.php#categories">Categories</a>

        <a href="index.php#products">Products</a>

        <a href="wishlist.php">Wishlist ❤️</a>

        <a href="cart.php">Cart 🛒</a>

        <a href="orders.php">My Orders</a>

    </nav>

</header>


<!-- ================= WISHLIST ================= -->

<section class="wishlist-section">

    <h1>
        My Wishlist ❤️
    </h1>


    <?php if (mysqli_num_rows($result) > 0): ?>

        <div class="wishlist-container">


            <?php while ($product = mysqli_fetch_assoc($result)): ?>

                <?php

                $price = $product['price'];

                $discount = $product['discount'];

                $discount_amount = ($price * $discount) / 100;

                $final_price = $price - $discount_amount;

                ?>


                <div class="wishlist-card">


                    <!-- Product Image -->

                    <div class="wishlist-image">

                        <img
                            src="uploads/<?php echo htmlspecialchars($product['image']); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                        >

                    </div>


                    <!-- Product Information -->

                    <div class="wishlist-info">

                        <h2>

                            <?php echo htmlspecialchars($product['name']); ?>

                        </h2>


                        <p>

                            <?php echo htmlspecialchars($product['category_name']); ?>

                        </p>


                        <p class="wishlist-price">

                            ₹<?php echo number_format($final_price, 2); ?>

                        </p>


                        <?php if ($product['stock'] > 0): ?>

                            <p class="wishlist-stock">
                                ✅ In Stock
                            </p>

                        <?php else: ?>

                            <p class="wishlist-stock">
                                ❌ Out of Stock
                            </p>

                        <?php endif; ?>


                        <div class="wishlist-actions">


                            <a
                                href="product.php?id=<?php echo $product['product_id']; ?>"
                                class="view-product-btn"
                            >
                                View Product
                            </a>


                            <a
                                href="wishlist.php?remove=<?php echo $product['product_id']; ?>"
                                class="remove-wishlist-btn"
                                onclick="return confirm('Remove this product from wishlist?');"
                            >
                                Remove ❤️
                            </a>


                        </div>


                    </div>

                </div>


            <?php endwhile; ?>


        </div>


    <?php else: ?>

        <div class="empty-wishlist">

            <h2>
                Your Wishlist is Empty ❤️
            </h2>

            <p>
                Save your favorite grocery products here.
            </p>


            <a href="index.php#products">
                Browse Products
            </a>

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
            © 2026 FreshMart Grocery Store.All Rights Reserved.
        </p>

    </div>

</footer>


</body>

</html>