<?php
require_once '_base.php';
// Include server settings and necessary configurations
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/frontend/base2.css">
    <link rel="stylesheet" href="/css/frontend/frontend.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="/js/script.js"></script>
    <script src="https://kit.fontawesome.com/ddd777effa.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="wrapper">
        <!-- Header -->
        <header class="header">
            <!-- Logo Section -->
            <div class="logo-container">
                <a href="/home.php">
                    <img src="/images/logo.png" alt="Logo" class="logo">
                </a>
            </div>

            <!-- Navigation bar -->
            <nav class="navbar">
                <ul>
                    <li><a href="/home.php">Home</a></li>
                    <li><a href="/page/frontend/productDisplay.php">Products</a></li>
                    <li><a href="/page/frontend/about_us.php">About Us</a></li>
                    <li><a href="/page/frontend/contact_us.php">Contact Us</a></li>
                    <li><a href="/page/frontend/FAQ.php">FAQ</a></li>

                    <!-- Cart only visible when user is logged in -->
                    <?php if (isset($_user) && $_user): 
                        $cart = get_cart();
                        ?>
                        <li class="cart-icon">
                            <a href="/page/frontend/cart.php">Cart</a>
                            <span class="cart-badge"><?= count($cart);?></span> <!-- Dynamic cart count -->
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- User Information Section -->
            <div class="user-info">
                <?php if (isset($_user) && $_user): ?>
                    <!-- Logged in user content -->
                    <div class="notification-icon">
                        <a href="#" class="alert-icon">
                            <i class="fa fa-bell-o fa-3x" aria-hidden="true"></i>
                            <span class="notification-badge">1</span>
                        </a>

                        <!-- Notifications dropdown -->
                        <div id="notification-dropdown" class="alert-dropdown">
                            <ul>
                                <li><a class="table-data-link" href="#">Notification 1</a></li>
                                <li><a class="table-data-link" href="#">Notification 2</a></li>
                                <li><a class="table-data-link" href="#">Notification 3</a></li>
                                <li><a class="table-data-link" href="#">Notification 4</a></li>
                                <li><a class="table-data-link" href="#">Notification 5</a></li>
                                <li><a class="table-data-link" href="#">Notification 6</a></li>
                                <li><a class="table-data-link" href="#">Notification 7</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Profile Section -->
                    <div class="profile-header" style="display:flex; flex-direction: row; align-items: center;">
                        <!-- Profile Image -->
                        <?php if (!empty($_user->user_photo)): ?>
                            <img src="/uploads/<?= htmlspecialchars($_user->user_photo) ?>" alt="Profile Image"
                                style="width:50px; height:50px; border-radius:50%; margin-right:10px;">
                        <?php else: ?>
                            <i class="fa fa-user-circle" style="font-size:50px; margin-right:10px;"></i> <!-- Default user icon -->
                        <?php endif; ?>

                        <!-- Username and Role -->
                        <div style="display:flex; flex-direction: column; align-items: center;">
                            <a class="username table-data-link" href="/page/frontend/userprofile.php"><?= htmlspecialchars($_user->user_name) ?></a>
                            <span class="user-role"><?= htmlspecialchars($_user->user_role) ?></span>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <div class="logout-button">
                        <a href="/page/frontend/logout.php" class="logout-icon">Logout
                            <i class="fa fa-sign-out" aria-hidden="true" style="margin-left:8px;"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- User not logged in, show login/register buttons -->
                    <div class="auth-buttons" style="display: flex; gap: 10px;">
                        <div class="login-button">
                            <a href="/page/frontend/login.php" class="login-icon">Login
                                <i class="fa fa-sign-in" aria-hidden="true" style="margin-left:8px;"></i>
                            </a>
                        </div>
                        <div class="register-button">
                            <a href="/page/frontend/register.php" class="register-icon">Register
                                <i class="fa fa-user-plus" aria-hidden="true" style="margin-left:8px;"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </header>
    </div>
</body>

</html>