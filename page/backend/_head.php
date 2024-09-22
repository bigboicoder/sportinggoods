<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Link to CSS stylesheets -->
    <link rel="stylesheet" href="/css/backend/base.css">
    <link rel="stylesheet" href="/css/backend/backend.css">
    <link rel="stylesheet" href="/css/backend/form.css">

    <!-- Include jQuery library from a CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Link to JavaScript files -->
    <script src="/js/app.js"></script>
    <script src="/js/script.js"></script>

    <!-- Include Font Awesome icons -->
    <script src="https://kit.fontawesome.com/ddd777effa.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="wrapper">
        <!-- Header -->
        <header class="header">
            <div class="logo-container">
                <a href="/page/backend/dashboard.php">
                    <img src="/images/logo.png" alt="Logo" class="logo">
                </a>
            </div>
            <div class="user-info">
                <div class="notification-icon">
                    <a href="#" class="alert-icon">
                        <i class="fa fa-bell-o fa-3x" aria-hidden="true"></i>
                        <span class="notification-badge">1</span>
                    </a>
                </div>

                <div class="alert-dropdown">
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

                <div class="profile-header" style="display:flex;">
                    <!-- Profile Image -->
                    <?php if (!empty($_user->user_photo)): ?>
                        <img src="/uploads/<?= htmlspecialchars($_user->user_photo) ?>" alt="Profile Image"
                            style="width:50px; height:50px; border-radius:50%; margin-right:10px;">
                    <?php else: ?>
                        <i class="fa fa-user-circle" style="font-size:50px; margin-right:10px;"></i> <!-- Default user icon -->
                    <?php endif; ?>
                    <a class="username table-data-link" href="/page/backend/profile.php"><?= htmlspecialchars($_user->user_name) ?></a>
                </div>

                <div class="logout-button">
                    <a href="/logout.php" class="logout-icon">Logout<i class="fa fa-sign-out" aria-hidden="true" style="margin-left:8px;"></i></a>
                </div>
            </div>
        </header>

        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="/page/backend/dashboard.php">Dashboard</a></li>
                    <li><a href="/page/backend/admin.php">Admin</a></li>
                    <li><a href="/page/backend/products.php">Products</a></li>
                    <li><a href="/page/backend/orders.php">Orders</a></li>
                    <li><a href="/page/backend/coupons.php">Coupons</a></li>
                    <li><a href="/page/backend/customers.php">Customers</a></li>
                    <li>
                        <a class="dropdown-btn">Categories</a>
                        <ul class="dropdown-container">
                            <li><a href="/page/backend/categories.php">Categories</a></li>
                            <li><a href="/page/backend/subcategory.php">Sub Categories</a></li>
                        </ul>
                    </li>
                    <li><a href="/page/backend/report.php">Reports</a></li>
                    <li><a href="/page/backend/profile.php">Profile</a></li>
                </ul>
            </nav>
        </aside>