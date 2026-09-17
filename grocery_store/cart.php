<?php

session_start();

include 'config/database.php';


// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

$user_id = intval($_SESSION['user_id']);


// ================= REMOVE PRODUCT =================

if (isset($_GET['remove'])) {

    $cart_id = intval($_GET['remove']);

    $delete_sql = "DELETE FROM cart
                   WHERE id = $cart_id
                   AND user_id = $user_id";

    mysqli_query($conn, $delete_sql);

    header("Location: cart.php");
    exit();
}


// ================= UPDATE QUANTITY =================

if (isset($_POST['update_cart'])) {

    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        $quantity = 1;
    }


    // Get available stock
    $stock_sql = "SELECT products.stock
                  FROM cart
                  INNER JOIN products
                  ON cart.product_id = products.id
                  WHERE cart.id = $cart_id
                  AND cart.user_id = $user_id";

    $stock_result = mysqli_query($conn, $stock_sql);

    if ($stock_result && mysqli_num_rows($stock_result) > 0) {

        $stock_data = mysqli_fetch_assoc($stock_result);

        $available_stock = intval($stock_data['stock']);

        if ($available_stock <= 0) {
            $quantity = 0;
        } elseif ($quantity > $available_stock) {
            $quantity = $available_stock;
        }


        if ($quantity > 0) {

            $update_sql = "UPDATE cart
                           SET quantity = $quantity
                           WHERE id = $cart_id
                           AND user_id = $user_id";

            mysqli_query($conn, $update_sql);

        } else {

            // Remove item if product is out of stock
            $delete_sql = "DELETE FROM cart
                           WHERE id = $cart_id
                           AND user_id = $user_id";

            mysqli_query($conn, $delete_sql);
        }
    }

    header("Location: cart.php");
    exit();
}


// ================= FETCH CART PRODUCTS =================

$sql = "SELECT cart.id AS cart_id,
               cart.quantity,
               products.id AS product_id,
               products.name,
               products.price,
               products.discount,
               products.image,
               products.stock

        FROM cart

        INNER JOIN products
        ON cart.product_id = products.id

        WHERE cart.user_id = $user_id

        ORDER BY cart.id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Cart - FreshMart</title>

    <link rel="stylesheet" href="css/style.css">

    <script src="js/script.js" defer></script>

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


<!-- ================= CART ================= -->

<section class="cart-section">

    <h1>My Shopping Cart 🛒</h1>


    <?php if ($result && mysqli_num_rows($result) > 0): ?>

        <div class="cart-container">

            <?php

            $grand_total = 0;

            ?>


            <?php while ($item = mysqli_fetch_assoc($result)): ?>

                <?php

                $price = (float) $item['price'];

                $discount = (float) $item['discount'];

                $quantity = intval($item['quantity']);

                $stock = intval($item['stock']);

                // Calculate discount
                $discount_amount = ($price * $discount) / 100;

                // Final product price
                $final_price = $price - $discount_amount;

                // Subtotal
                $subtotal = $final_price * $quantity;

                $grand_total += $subtotal;

                ?>


                <!-- ================= CART ITEM ================= -->

                <div class="cart-item">


                    <!-- Product Image -->

                    <div class="cart-image">

                        <a href="product.php?id=<?php echo $item['product_id']; ?>">

                            <img
                                src="uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>"
                            >

                        </a>

                    </div>


                    <!-- Product Information -->

                    <div class="cart-info">

                        <h3>

                            <a
                                href="product.php?id=<?php echo $item['product_id']; ?>"
                                class="cart-product-name"
                            >

                                <?php echo htmlspecialchars($item['name']); ?>

                            </a>

                        </h3>


                        <!-- Product Price -->

                        <p>

                            Price:

                            <span class="product-price">

                                ₹<?php echo number_format($final_price, 2); ?>

                            </span>


                            <?php if ($discount > 0): ?>

                                <span class="old-price">

                                    ₹<?php echo number_format($price, 2); ?>

                                </span>

                                <span class="discount-badge">

                                    <?php echo number_format($discount, 0); ?>% OFF

                                </span>

                            <?php endif; ?>

                        </p>


                        <!-- ================= QUANTITY ================= -->

                        <div class="quantity-control">


                            <!-- Decrease -->

                            <button
                                type="button"
                                class="quantity-btn"
                                onclick="changeQuantity(
                                    <?php echo $item['cart_id']; ?>,
                                    -1,
                                    <?php echo $stock; ?>
                                )"
                            >

                                −

                            </button>


                            <!-- Quantity -->

                            <input
                                type="number"
                                id="quantity-<?php echo $item['cart_id']; ?>"
                                value="<?php echo $quantity; ?>"
                                min="1"
                                max="<?php echo $stock; ?>"
                                readonly
                            >


                            <!-- Increase -->

                            <button
                                type="button"
                                class="quantity-btn"
                                onclick="changeQuantity(
                                    <?php echo $item['cart_id']; ?>,
                                    1,
                                    <?php echo $stock; ?>
                                )"
                            >

                                +

                            </button>

                        </div>


                        <!-- Stock -->

                        <?php if ($stock > 0): ?>

                            <p class="cart-stock">

                                ✅ <?php echo $stock; ?> available

                            </p>

                        <?php else: ?>

                            <p class="cart-stock out-of-stock">

                                ❌ Out of Stock

                            </p>

                        <?php endif; ?>


                        <!-- Subtotal -->

                        <p
                            class="cart-subtotal"
                            id="subtotal-<?php echo $item['cart_id']; ?>"
                        >

                            Subtotal:

                            ₹<?php echo number_format($subtotal, 2); ?>

                        </p>


                        <!-- Remove -->

                        <a
                            href="cart.php?remove=<?php echo $item['cart_id']; ?>"
                            class="remove-btn"
                            onclick="return confirm('Remove this product from cart?');"
                        >

                            Remove ❌

                        </a>

                    </div>

                </div>


            <?php endwhile; ?>


            <!-- ================= TOTAL ================= -->

            <div class="cart-total">

                <h2>

                    Total:

                    ₹<span id="grand-total">

                        <?php echo number_format($grand_total, 2); ?>

                    </span>

                </h2>


                <!-- Checkout -->

                <?php if ($grand_total > 0): ?>

                    <a
                        href="checkout.php"
                        class="checkout-btn"
                    >

                        Proceed to Checkout →

                    </a>

                <?php endif; ?>

            </div>


        </div>


    <?php else: ?>


        <!-- ================= EMPTY CART ================= -->

        <div class="empty-cart">

            <h2>

                Your cart is empty 🛒

            </h2>

            <p>

                Add some fresh groceries to your cart.

            </p>

            <a href="products.php">

                Continue Shopping →

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

            © 2026 FreshMart Grocery Store.
            All Rights Reserved.

        </p>

    </div>

</footer>


</body>

</html>