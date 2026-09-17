
<?php

session_start();

include 'config/database.php';

$message_status = "";
$message_type = "";

if (isset($_POST['send_message'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);


    // ================= VALIDATION =================

    if (empty($name) || empty($email) || empty($message)) {

        $message_status = "Please fill all required fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message_status = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        // ================= INSERT MESSAGE =================

        $sql = "INSERT INTO contact_messages
                (name, email, phone, subject, message)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sssss",
            $name,
            $email,
            $phone,
            $subject,
            $message
        );

        if (mysqli_stmt_execute($stmt)) {

            $message_status = "Your message has been sent successfully!";
            $message_type = "success";

        } else {

            $message_status = "Failed to send message. Please try again.";
            $message_type = "error";
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

    <title>Contact Us - FreshMart</title>

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


<!-- ================= CONTACT PAGE ================= -->

<section class="contact-section">

    <div class="contact-container">

        <h1>Contact Us</h1>

        <p class="contact-intro">
            Have a question or need assistance?
            Get in touch with the FreshMart team.
        </p>


        <!-- ================= CONTACT INFORMATION ================= -->

        <div class="contact-info-grid">

            <div class="contact-info-card">

                <div class="contact-icon">📍</div>

                <h3>Address</h3>

                <p>FreshMart Grocery Store</p>

                <p>India</p>

            </div>


            <div class="contact-info-card">

                <div class="contact-icon">📱</div>

                <h3>Phone</h3>

                <p>+91 98765 43210</p>

            </div>


            <div class="contact-info-card">

                <div class="contact-icon">📧</div>

                <h3>Email</h3>

                <p>support@freshmart.com</p>

            </div>

        </div>


        <!-- ================= CONTACT FORM ================= -->

        <div class="contact-form-card">

            <h2>Send Us a Message</h2>


            <?php if (!empty($message_status)): ?>

                <div class="contact-message <?php echo $message_type; ?>">

                    <?php echo htmlspecialchars($message_status); ?>

                </div>

            <?php endif; ?>


            <form method="POST" action="">


                <label for="name">
                    Name *
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your name"
                    required
                >


                <label for="email">
                    Email *
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
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
                    maxlength="10"
                >


                <label for="subject">
                    Subject
                </label>

                <input
                    type="text"
                    id="subject"
                    name="subject"
                    placeholder="Enter subject"
                >


                <label for="message">
                    Message *
                </label>

                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    placeholder="Write your message here..."
                    required
                ></textarea>


                <button
                    type="submit"
                    name="send_message"
                >
                    Send Message
                </button>

            </form>

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

