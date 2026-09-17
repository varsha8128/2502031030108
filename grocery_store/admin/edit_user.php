<?php

session_start();

include '../config/database.php';

// ================= CHECK ADMIN LOGIN =================

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ================= CHECK USER ID =================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = (int) $_GET['id'];


// ================= UPDATE USER =================

if (isset($_POST['update_user'])) {

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));

    // Basic validation
    if ($name == '' || $email == '') {

        $error = "Name and Email are required.";

    } else {

        // Check whether email already belongs to another user
        $check_email = mysqli_query(
            $conn,
            "SELECT id FROM users
             WHERE email = '$email'
             AND id != $user_id
             LIMIT 1"
        );

        if (mysqli_num_rows($check_email) > 0) {

            $error = "This email is already registered with another user.";

        } else {

            $update_sql = "UPDATE users SET
                            name = '$name',
                            email = '$email',
                            phone = '$phone',
                            address = '$address'
                           WHERE id = $user_id";

            if (mysqli_query($conn, $update_sql)) {

                header("Location: view_user.php?id=$user_id&updated=1");
                exit();

            } else {

                $error = "Failed to update user details.";

            }
        }
    }
}


// ================= FETCH USER =================

$result = mysqli_query(
    $conn,
    "SELECT id, name, email, phone, address, profile_image
     FROM users
     WHERE id = $user_id
     LIMIT 1"
);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: users.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit User - FreshMart Admin</title>

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


<!-- ================= EDIT USER ================= -->

<section class="admin-edit-user">

    <div class="edit-user-container">

        <div class="edit-user-header">

            <h1>Edit User</h1>

            <a href="view_user.php?id=<?php echo $user['id']; ?>">
                ← Back to User
            </a>

        </div>


        <?php if (isset($error)) { ?>

            <div class="user-edit-error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>


        <!-- USER PROFILE -->

        <div class="edit-user-profile">

            <?php

            $image = !empty($user['profile_image'])
                ? '../uploads/' . $user['profile_image']
                : '../uploads/default-profile.png';

            ?>

            <img src="<?php echo htmlspecialchars($image); ?>"
                 alt="User Profile">

            <div>

                <h2>
                    <?php echo htmlspecialchars($user['name']); ?>
                </h2>

                <p>
                    User ID: #<?php echo $user['id']; ?>
                </p>

            </div>

        </div>


        <!-- EDIT FORM -->

        <form method="POST" class="edit-user-form">

            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($user['name']); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($user['email']); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="phone">
                    Phone
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?php echo htmlspecialchars($user['phone']); ?>"
                >

            </div>


            <div class="form-group">

                <label for="address">
                    Address
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="4"
                ><?php echo htmlspecialchars($user['address']); ?></textarea>

            </div>


            <div class="edit-user-actions">

                <button
                    type="submit"
                    name="update_user"
                    class="save-user-btn"
                >
                    💾 Save Changes
                </button>

                <a
                    href="view_user.php?id=<?php echo $user['id']; ?>"
                    class="cancel-user-btn"
                >
                    Cancel
                </a>

            </div>

        </form>

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