<?php

session_start();

include '../config/database.php';

// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ================= GET USER ID =================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = (int) $_GET['id'];

// ================= FETCH USER =================

$sql = "SELECT id, name, email, phone, address, created_at, profile_image
        FROM users
        WHERE id = $user_id
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "User not found.";
    exit();
}

$user = mysqli_fetch_assoc($result);

// ================= PROFILE IMAGE =================

$image = !empty($user['profile_image'])
    ? '../uploads/' . $user['profile_image']
    : '../uploads/default-profile.png';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View User - FreshMart Admin</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<!-- ================= ADMIN NAVBAR ================= -->

<nav class="admin-navbar">

    <div class="admin-logo">
        FreshMart Admin
    </div>

    <div class="admin-nav-links">

        <a href="dashboard.php">Dashboard</a>

        <a href="products.php">Products</a>

        <a href="categories.php">Categories</a>

        <a href="orders.php">Orders</a>

        <a href="users.php">Users</a>

        <a href="logout.php">Logout</a>

    </div>

</nav>


<!-- ================= USER DETAILS ================= -->

<section class="admin-user-details">

    <div class="user-details-container">

        <div class="user-details-header">

            <h1>User Details</h1>

            <a href="users.php" class="back-users-btn">
                ← Back to Users
            </a>

        </div>


        <div class="user-profile-section">

            <div class="user-profile-image">

                <img src="<?php echo htmlspecialchars($image); ?>"
                     alt="User Profile">

            </div>


            <div class="user-basic-info">

                <h2>
                    <?php echo htmlspecialchars($user['name']); ?>
                </h2>

                <p>
                    <?php echo htmlspecialchars($user['email']); ?>
                </p>

            </div>

        </div>


        <div class="user-info-grid">

            <div class="user-info-box">

                <span>User ID</span>

                <strong>
                    #<?php echo $user['id']; ?>
                </strong>

            </div>


            <div class="user-info-box">

                <span>Name</span>

                <strong>
                    <?php echo htmlspecialchars($user['name']); ?>
                </strong>

            </div>


            <div class="user-info-box">

                <span>Email</span>

                <strong>
                    <?php echo htmlspecialchars($user['email']); ?>
                </strong>

            </div>


            <div class="user-info-box">

                <span>Phone</span>

                <strong>
                    <?php echo !empty($user['phone'])
                        ? htmlspecialchars($user['phone'])
                        : 'Not Provided'; ?>
                </strong>

            </div>


            <div class="user-info-box">

                <span>Address</span>

                <strong>
                    <?php echo !empty($user['address'])
                        ? htmlspecialchars($user['address'])
                        : 'Not Provided'; ?>
                </strong>

            </div>


            <div class="user-info-box">

                <span>Registered On</span>

                <strong>
                    <?php
                    echo date(
                        'd M Y, h:i A',
                        strtotime($user['created_at'])
                    );
                    ?>
                </strong>

            </div>

        </div>


        <div class="user-details-actions">

            <a href="edit_user.php?id=<?php echo $user['id']; ?>"
               class="edit-user-btn">

                ✏️ Edit User

            </a>

            <a href="delete_user.php?id=<?php echo $user['id']; ?>"
               class="delete-user-btn"
               onclick="return confirm('Are you sure you want to delete this user?');">

                🗑️ Delete User

            </a>

        </div>

    </div>

</section>


<!-- ================= FOOTER ================= -->

<footer class="admin-footer">

    <p>
        © <?php echo date('Y'); ?> FreshMart Admin Panel. All Rights Reserved.
    </p>

</footer>

</body>

</html>