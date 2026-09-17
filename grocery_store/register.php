<?php

include 'config/database.php';

$message = "";

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = trim($_POST['phone']);
    $confirm_password = $_POST['confirm_password'];
    $address = trim($_POST['address']);

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {

        $message = "Please fill all required fields.";
    
    } elseif (strlen($password) < 6) {
    
        $message = "Password must be at least 6 characters.";
    
    } elseif ($password !== $confirm_password) {
    
        $message = "Passwords do not match.";
    
    } else {

        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {

            $message = "Email already registered.";

        } else {

            // Secure password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $insert_sql = "INSERT INTO users
                           (name, email, password, phone, address)
                           VALUES (?, ?, ?, ?, ?)";

            $insert_stmt = mysqli_prepare($conn, $insert_sql);

            mysqli_stmt_bind_param(
                $insert_stmt,
                "sssss",
                $name,
                $email,
                $hashed_password,
                $phone,
                $address
            );

            if (mysqli_stmt_execute($insert_stmt)) {

                $message = "Registration successful! You can now login.";

            } else {

                $message = "Registration failed. Please try again.";

            }

            mysqli_stmt_close($insert_stmt);
        }

        mysqli_stmt_close($check_stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - FreshMart</title>

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

        <a href="#">Wishlist ❤️</a>

        <a href="#">Cart 🛒</a>

        <a href="register.php">Register</a>

    </nav>

</header>


<!-- ================= REGISTER FORM ================= -->

<section class="register-section">

    <div class="register-container">

        <h1>Create Your Account</h1>

        <p>Register to shop at FreshMart</p>


        <?php if (!empty($message)): ?>

            <div class="register-message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST" action="">

            <label for="name">
                Full Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your full name"
                required
            >


            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
            >


            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >
            <label for="confirm_password">
    Confirm Password
</label>

<input
    type="password"
    id="confirm_password"
    name="confirm_password"
    placeholder="Re-enter your password"
    required
>


            <label for="phone">
                Phone
            </label>

            <input
    type="tel"
    id="phone"
    name="phone"
    placeholder="Enter your phone number"
    required
    maxlength="10"
    pattern="[0-9]{10}"
>  


            <label for="address">
                Address
            </label>

            <textarea
                id="address"
                name="address"
                placeholder="Enter your address"
                rows="4"
            ></textarea>


            <button
                type="submit"
                name="register"
            >
                Create Account
            </button>

        </form>


        <p class="login-text">
            Already have an account?
            <a href="login.php">Login</a>
        </p>

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