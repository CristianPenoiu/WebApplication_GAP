<?php
session_start();
$_SESSION['logout_message'] = "You have been logged out successfully.";
session_write_close(); // Asigură-te că mesajul este scris în sesiune înainte de a continua
header("Location: ../index.php");
session_destroy();
exit();
?>
