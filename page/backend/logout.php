<?php
include '_base.php';

// ----------------------------------------------------------------------------

temp('info', 'Logout successfully');
logout();

// Redirect to login.php
header('Location: /login.php');
exit;  // Make sure to exit after a redirect to stop further script execution

// ----------------------------------------------------------------------------
?>
