<?php
session_start();

// Destroy all session data (like clearing cookies in JavaScript)
session_destroy();

// Redirect to login page
header("Location: masuk.php");
exit();
?>
