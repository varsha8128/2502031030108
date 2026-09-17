
<?php

session_start();

include 'config/database.php';

// ================= CHECK LOGIN =================

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];


// =====================================================
// UPDATE PROFILE
// =====================================================

if (isset($_POST['update_profile'])) {

    $name    = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email   = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone   = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $city    = mysqli_real_escape_string($conn, trim($_POST['city']));
    $state   = mysqli_real_escape_string($conn, trim($_POST['state']));
    $pincode = mysqli_real_escape_string($conn, trim($_POST['pincode']));

    // Basic validation
    if (
        empty($name) ||
        empty($email) ||
        empty($phone) ||
        empty($address) ||
        empty($city) ||
        empty($state) ||
        empty($pincode)
    ) {

        $update_error = "Please fill all profile details.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $update_error = "Please enter a valid email address.";

    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {

        $update_error = "Mobile number must contain exactly 10 digits.";

    } elseif (!preg_match('/^[0-9]{6}$/', $pincode)) {

        $update_error = "Pincode must contain exactly 6 digits.";

    } else {

        // ================= CHECK EMAIL =================

        $email_check_sql = "SELECT id FROM users
                            WHERE email = '$email'
                            AND id != $user_id";

        $email_check_result = mysqli_query($conn, $email_check_sql);

        if ($email_check_result && mysqli_num_rows($email_check_result) > 0) {

            $update_error = "This email address is already registered.";

        } else {

            // ================= UPDATE USERS =================

            $update_user_sql = "UPDATE users SET
                                name = '$name',
                                email = '$email',
                                phone = '$phone',
                                address = '$address'
                                WHERE id = $user_id";

            if (mysqli_query($conn, $update_user_sql)) {

                // =================================================
                // CHECK IF USER ALREADY HAS AN ADDRESS
                // =================================================

                $address_check_sql = "SELECT id
                                      FROM addresses
                                      WHERE user_id = $user_id
                                      ORDER BY id DESC
                                      LIMIT 1";

                $address_check_result = mysqli_query($conn, $address_check_sql);

                if ($address_check_result && mysqli_num_rows($address_check_result) > 0) {

                    $saved_address = mysqli_fetch_assoc($address_check_result);
                    $address_id = (int) $saved_address['id'];

                    // ================= UPDATE ADDRESS =================

                    $update_address_sql = "UPDATE addresses SET
                                           full_name = '$name',
                                           phone = '$phone',
                                           address = '$address',
                                           city = '$city',
                                           state = '$state',
                                           pincode = '$pincode'
                                           WHERE id = $address_id
                                           AND user_id = $user_id";

                    mysqli_query($conn, $update_address_sql);

                } else {

                    // ================= INSERT ADDRESS =================

                    $insert_address_sql = "INSERT INTO addresses
                                           (user_id, full_name, phone, address, city, state, pincode)
                                           VALUES
                                           ($user_id, '$name', '$phone', '$address',
                                            '$city', '$state', '$pincode')";

                    mysqli_query($conn, $insert_address_sql);
                }

                $update_success = "Profile updated successfully!";

            } else {

                $update_error = "Failed to update profile.";
            }
        }
    }
}


// =====================================================
// PROFILE PHOTO UPLOAD
// =====================================================

if (isset($_POST['upload_photo'])) {

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {

        $file = $_FILES['profile_image'];

        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];

        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowed_extensions)) {

            $upload_error = "Only JPG, JPEG, PNG and WEBP images are allowed.";

        } elseif ($file_size > 5 * 1024 * 1024) {

            $upload_error = "Image size must be less than 5MB.";

        } else {

            // Create profile folder if it doesn't exist
            if (!is_dir('uploads/profile')) {
                mkdir('uploads/profile', 0777, true);
            }

            $new_file_name = "profile_" . $user_id . "_" . time() . "." . $extension;

            $upload_path = "uploads/profile/" . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {

                $new_file_name_safe = mysqli_real_escape_string($conn, $new_file_name);

                $update_photo_sql = "UPDATE users
                                     SET profile_image = '$new_file_name_safe'
                                     WHERE id = $user_id";

                if (mysqli_query($conn, $update_photo_sql)) {

                    header("Location: profile.php");
                    exit();

                } else {

                    $upload_error = "Database update failed.";
                }

            } else {

                $upload_error = "Failed to upload image.";
            }
        }

    } else {

        $upload_error = "Please select a photo.";
    }
}


// =====================================================
// GET USER DETAILS
// =====================================================

$user_sql = "SELECT * FROM users WHERE id = $user_id";

$user_result = mysqli_query($conn, $user_sql);

if (!$user_result || mysqli_num_rows($user_result) == 0) {
    die("User not found.");
}

$user = mysqli_fetch_assoc($user_result);


// =====================================================
// GET LATEST ADDRESS
// =====================================================

$address_sql = "SELECT * FROM addresses
                WHERE user_id = $user_id
                ORDER BY id DESC
                LIMIT 1";

$address_result = mysqli_query($conn, $address_sql);

$address_data = [
    'city' => '',
    'state' => '',
    'pincode' => ''
];

if ($address_result && mysqli_num_rows($address_result) > 0) {

    $address_data = mysqli_fetch_assoc($address_result);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile - FreshMart</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

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

        <a href="orders.php">My Orders 📦</a>

        <a href="profile.php" class="active">Profile 👤</a>

    </nav>

</header>


<!-- =====================================================
     PROFILE PAGE
===================================================== -->

<section class="profile-page">

    <div class="profile-card">


        <!-- ================= PROFILE PHOTO ================= -->

        <?php

$profileImage = !empty($user['profile_image'])
    ? $user['profile_image']
    : 'default-profile.png';

$profilePath = 'uploads/profile/' . $profileImage;

if (!file_exists($profilePath)) {
    $profilePath = 'images/default-profile.png';
}

?>

<div class="profile-icon">
    <img
        src="<?php echo htmlspecialchars($profilePath); ?>"
        alt="Profile Photo"
    >
</div>

        <!-- ================= PHOTO UPLOAD ================= -->

        <div class="profile-upload">

            <form method="POST" enctype="multipart/form-data">

                <label for="profile_image" class="choose-photo">
                    📷 Choose Photo
                </label>

                <input
                    type="file"
                    id="profile_image"
                    name="profile_image"
                    accept="image/jpeg,image/png,image/webp"
                    required
                >

                <button
                    type="submit"
                    name="upload_photo"
                    class="upload-photo-btn"
                >
                    Upload Photo
                </button>

            </form>


            <?php if (isset($upload_error)): ?>

                <p class="upload-error">
                    ❌ <?php echo htmlspecialchars($upload_error); ?>
                </p>

            <?php endif; ?>

        </div>


        <!-- ================= TITLE ================= -->

        <h1>My Profile</h1>

        <p class="profile-welcome">
            View and update your personal information.
        </p>


        <!-- ================= SUCCESS MESSAGE ================= -->

        <?php if (isset($update_success)): ?>

            <div class="profile-success">
                ✅ <?php echo htmlspecialchars($update_success); ?>
            </div>

        <?php endif; ?>


        <!-- ================= ERROR MESSAGE ================= -->

        <?php if (isset($update_error)): ?>

            <div class="profile-update-error">
                ❌ <?php echo htmlspecialchars($update_error); ?>
            </div>

        <?php endif; ?>


        <!-- =================================================
             UPDATE PROFILE FORM
        ================================================== -->

        <form method="POST" class="profile-form">


            <!-- Name -->

            <div class="profile-form-group">

                <label for="name">
                    👤 Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($user['name']); ?>"
                    required
                >

            </div>


            <!-- Email -->

            <div class="profile-form-group">

                <label for="email">
                    📧 Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($user['email']); ?>"
                    required
                >

            </div>


            <!-- Mobile -->

            <div class="profile-form-group">

                <label for="phone">
                    📱 Mobile Number
                </label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    placeholder="Enter 10 digit mobile number"
                    required
                >

            </div>


            <!-- Address -->

            <div class="profile-form-group">

                <label for="address">
                    📍 Address
                </label>

                <textarea
                    id="address"
                    name="address"
                    rows="3"
                    required
                ><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>

            </div>


            <!-- City -->

            <div class="profile-form-group">

                <label for="city">
                    🏙️ City
                </label>

                <input
                    type="text"
                    id="city"
                    name="city"
                    value="<?php echo htmlspecialchars($address_data['city'] ?? ''); ?>"
                    required
                >

            </div>


            <!-- State -->

            <div class="profile-form-group">

                <label for="state">
                    🗺️ State
                </label>

                <input
                    type="text"
                    id="state"
                    name="state"
                    value="<?php echo htmlspecialchars($address_data['state'] ?? ''); ?>"
                    required
                >

            </div>


            <!-- Pincode -->

            <div class="profile-form-group">

                <label for="pincode">
                    📮 Pincode
                </label>

                <input
                    type="text"
                    id="pincode"
                    name="pincode"
                    value="<?php echo htmlspecialchars($address_data['pincode'] ?? ''); ?>"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    placeholder="Enter 6 digit pincode"
                    required
                >

            </div>


            <!-- Update Button -->

            <div class="profile-update-button">

                <button
                    type="submit"
                    name="update_profile"
                    class="update-profile-btn"
                >
                    💾 Update Profile
                </button>

            </div>

        </form>


        <!-- ================= PROFILE ACTIONS ================= -->

        <div class="profile-actions">

            <a href="cart.php" class="profile-btn">
                🛒 My Cart
            </a>

            <a href="wishlist.php" class="profile-btn">
                ❤️ My Wishlist
            </a>

            <a href="orders.php" class="profile-btn">
                📦 My Orders
            </a>

            <a href="logout.php" class="logout-btn">
                🚪 Logout
            </a>

        </div>


    </div>

</section>


<!-- =====================================================
     FOOTER
===================================================== -->

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

            <a href="categories.php">Categories</a>

            <a href="products.php">Products</a>

            <a href="cart.php">Cart</a>

            <a href="orders.php">My Orders</a>

            <a href="wishlist.php">Wishlist ❤️</a>

        </div>


        <div class="footer-column">

            <h3>Customer Support</h3>

            <a href="#">Contact Us</a>

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

