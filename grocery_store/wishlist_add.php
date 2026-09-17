<?php

session_start();

include 'config/database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check product ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

// Check whether product already exists in wishlist
$check_sql = "SELECT id
              FROM wishlist
              WHERE user_id = $user_id
              AND product_id = $product_id";

$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) == 0) {

    // Add product to wishlist
    $insert_sql = "INSERT INTO wishlist (user_id, product_id)
                   VALUES ($user_id, $product_id)";

    mysqli_query($conn, $insert_sql);
}

// Go to wishlist
header("Location: wishlist.php");
exit();

?>