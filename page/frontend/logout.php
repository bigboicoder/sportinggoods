<?php
include '../../_base.php';

// ----------------------------------------------------------------------------

// Set logout success message
temp('info', 'Logout successfully');

// Perform logout
logout();

// Redirect to login page
header('Location: /login.php');
exit;  // Ensure to exit after the redirect to prevent further script execution

// ----------------------------------------------------------------------------
?>
