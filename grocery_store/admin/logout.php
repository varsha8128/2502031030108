<?php

session_start();

// Destroy admin session
session_unset();
session_destroy();

// Redirect to admin login
header("Location: admin_login.php");
exit();

?>