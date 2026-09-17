<?php

session_start();

include '../config/database.php';

$message = "";

if (isset($_POST['admin_login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT *
            FROM admin
            WHERE email = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $admin = mysqli_fetch_assoc($result);

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];

            header("Location: dashboard.php");
            exit();

        } else {

            $message = "Invalid password.";

        }

    } else {

        $message = "Admin account not found.";

    }

    mysqli_stmt_close($stmt);

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login - FreshMart</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<section class="admin-login-section">

    <div class="admin-login-container">

        <h1>🛒 FreshMart Admin</h1>

        <h2>Admin Login</h2>

        <?php if (!empty($message)): ?>

            <p class="error-message">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                placeholder="Enter admin email"
                required
            >

            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                placeholder="Enter password"
                required
            >

            <button
                type="submit"
                name="admin_login"
            >
                Login
            </button>

        </form>

    </div>

</section>

</body>

</html>