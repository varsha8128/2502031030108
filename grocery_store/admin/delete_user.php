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


// ================= DEACTIVATE USER =================

$sql = "UPDATE users
        SET status = 'inactive'
        WHERE id = $user_id
        LIMIT 1";

if (mysqli_query($conn, $sql)) {

    header("Location: users.php?deactivated=1");
    exit();

} else {

    echo "Failed to deactivate user: " . mysqli_error($conn);

}

?>