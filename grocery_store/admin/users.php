<?php

session_start();

include '../config/database.php';


// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {

    header("Location: admin_login.php");
    exit();

}


// ================= SEARCH =================

$search = '';

if (isset($_GET['search'])) {

    $search = trim($_GET['search']);

}


// ================= FETCH USERS =================

if ($search != '') {

    $search_safe = mysqli_real_escape_string($conn, $search);

    $sql = "SELECT id,
                   name,
                   email,
                   phone,
                   address,
                   created_at,
                   profile_image

            FROM users

            WHERE name LIKE '%$search_safe%'
               OR email LIKE '%$search_safe%'
               OR phone LIKE '%$search_safe%'

            ORDER BY id DESC";

} else {

    $sql = "SELECT id,
    name,
    email,
    phone,
    address,
    created_at,
    profile_image,
    status
FROM users
ORDER BY id DESC";

}


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

    <title>
        Manage Users - FreshMart Admin
    </title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>

<body>


<!-- ================= ADMIN NAVBAR ================= -->

<header class="admin-navbar">

    <div class="admin-logo">

        🛒 FreshMart Admin

    </div>


    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="products.php">
            Products
        </a>

        <a href="categories.php">
            Categories
        </a>

        <a href="orders.php">
            Orders
        </a>

        <a href="users.php">
            Users
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<!-- ================= USERS ================= -->

<section class="admin-users">

    <h1>
        Registered Users 👥
    </h1>


    <!-- ================= SEARCH ================= -->

    <form
        method="GET"
        class="user-search"
    >

        <input
            type="text"
            name="search"
            placeholder="Search by name, email or phone..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            🔍 Search
        </button>


        <?php if ($search != ''): ?>

            <a
                href="users.php"
                class="clear-search"
            >
                Clear
            </a>

        <?php endif; ?>

    </form>


    <?php if (mysqli_num_rows($result) > 0): ?>

        <div class="admin-user-table">

            <table>

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Profile
                        </th>

                        <th>
                            Name
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Phone
                        </th>

                        <th>
                            Registered On
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($user = mysqli_fetch_assoc($result)): ?>

                        <tr>

                            <!-- ID -->

                            <td>
                                <?php echo $user['id']; ?>
                            </td>


                            <!-- PROFILE -->

                            <td>

                                <?php

                                $image = !empty($user['profile_image'])
                                    ? '../uploads/' . $user['profile_image']
                                    : '../uploads/default-profile.png';

                                ?>

                                <img
                                    src="<?php echo htmlspecialchars($image); ?>"
                                    alt="Profile"
                                    class="admin-user-image"
                                >

                            </td>


                            <!-- NAME -->

                            <td>
                                <?php
                                echo htmlspecialchars($user['name']);
                                ?>
                            </td>


                            <!-- EMAIL -->

                            <td>
                                <?php
                                echo htmlspecialchars($user['email']);
                                ?>
                            </td>


                            <!-- PHONE -->

                            <td>

                                <?php

                                echo !empty($user['phone'])
                                    ? htmlspecialchars($user['phone'])
                                    : 'Not Provided';

                                ?>

                            </td>


                            <!-- REGISTERED -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $user['created_at']
                                );

                                ?>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <div class="user-actions">

                                    <a
                                        href="view_user.php?id=<?php echo $user['id']; ?>"
                                        class="view-user-btn"
                                    >
                                        👁️ View
                                    </a>


                                    <a
                                        href="edit_user.php?id=<?php echo $user['id']; ?>"
                                        class="edit-user-btn"
                                    >
                                        ✏️ Edit
                                    </a>


                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>"
   class="delete-user-btn"
   onclick="return confirm('Are you sure you want to deactivate this user?');">
    🚫 Deactivate
</a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="no-users">

            <h2>
                No Users Found
            </h2>

            <p>

                <?php if ($search != ''): ?>

                    No users found for
                    "<strong><?php echo htmlspecialchars($search); ?></strong>".

                <?php else: ?>

                    No customers have registered yet.

                <?php endif; ?>

            </p>

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
            © 2026 FreshMart Grocery Store. All Rights Reserved.
        </p>

    </div>

</footer>


</body>

</html>