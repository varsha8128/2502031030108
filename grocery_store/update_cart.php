<?php

session_start();

include 'config/database.php';

header('Content-Type: application/json');


// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit();
}


$user_id = $_SESSION['user_id'];


// ================= GET DATA =================

$cart_id = intval($_POST['cart_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);


// ================= VALIDATION =================

if ($cart_id <= 0 || $quantity < 1) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid cart information."
    ]);

    exit();
}


// ================= FETCH CART + STOCK =================

$check_sql = "SELECT cart.product_id,
                     products.stock,
                     products.name

              FROM cart

              INNER JOIN products
              ON cart.product_id = products.id

              WHERE cart.id = ?
              AND cart.user_id = ?";

$check_stmt = mysqli_prepare(
    $conn,
    $check_sql
);

mysqli_stmt_bind_param(
    $check_stmt,
    "ii",
    $cart_id,
    $user_id
);

mysqli_stmt_execute($check_stmt);

$check_result = mysqli_stmt_get_result(
    $check_stmt
);

$cart_item = mysqli_fetch_assoc(
    $check_result
);

mysqli_stmt_close($check_stmt);


// ================= CHECK CART ITEM =================

if (!$cart_item) {

    echo json_encode([
        "success" => false,
        "message" => "Cart item not found."
    ]);

    exit();
}


// ================= CHECK STOCK =================

$stock = intval($cart_item['stock']);

$product_name = $cart_item['name'];


if ($stock <= 0) {

    echo json_encode([
        "success" => false,
        "message" => $product_name . " is currently out of stock."
    ]);

    exit();
}


if ($quantity > $stock) {

    echo json_encode([
        "success" => false,
        "message" => "Only " . $stock .
                     " item(s) of " .
                     $product_name .
                     " are available."
    ]);

    exit();
}


// ================= UPDATE CART =================

$sql = "UPDATE cart
        SET quantity = ?
        WHERE id = ?
        AND user_id = ?";

$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "iii",
    $quantity,
    $cart_id,
    $user_id
);


if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "Cart updated successfully."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to update cart."
    ]);

}


mysqli_stmt_close($stmt);

?>