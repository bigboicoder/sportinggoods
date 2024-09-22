<?php
require '../../_base.php';
//-----------------------------------------------------------------------------
auth('Admin'); 

$countUsers = $_db->query("SELECT COUNT(*) FROM Users WHERE user_role = 'User'")->fetchColumn();
$countProduct = $_db->query("SELECT COUNT(*) FROM Product")->fetchColumn();
$countOrder = $_db->query("SELECT COUNT(*) FROM Orders")->fetchColumn();
$totalOrderSum = $_db->query("SELECT SUM(order_total) FROM Orders")->fetchColumn();

$latestUsers = $_db->query("
    SELECT * 
    FROM Users 
    WHERE user_role = 'User' 
    ORDER BY dateCreated DESC 
    LIMIT 5
")->fetchAll();

$latestProducts = $_db->query('SELECT * FROM product ORDER BY dateCreated DESC LIMIT 3')->fetchAll();
$latestOrders = $_db->query('
    SELECT orders.order_id, users.user_username, orders.order_status, orders.order_total, orders.dateOrdered 
    FROM Orders 
    JOIN users ON orders.user_id = users.user_id 
    ORDER BY orders.dateOrdered DESC 
    LIMIT 3
')->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Dashboard';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">
    <div class="breadcrumbs">
        <span>Dashboard</span>
    </div>
    <div class="page-header">
        <h1>Dashboard</h1>
        <div class="button-add">
        </div>
    </div>

    <div class="total-container">
        <div>
            <div class="icon"><i class="fa fa-user" aria-hidden="true"></i></div>
            <div class="text">
                <p><?= $countUsers ?></p>
                <p>Customers</p>
            </div>
        </div>
        <div>
            <div class="icon"><i class="fa fa-cubes" aria-hidden="true"></i></div>
            <div class="text">
                <p><?= $countProduct ?></p>
                <p>Products</p>
            </div>
        </div>
        <div>
            <div class="icon"><i class="fa fa-book" aria-hidden="true"></i></div>
            <div class="text">
                <p><?= $countOrder ?></p>
                <p>Orders</p>
            </div>
        </div>
        <div>
            <div class="icon"><i class="fa fa-usd" aria-hidden="true"></i></div>
            <div class="text">
                <p><?= $totalOrderSum ?></p>
                <p>Sales</p>
            </div>
        </div>
    </div>

    <div class="dashboard-table-container" style="min-width:1112px;">
        <h3>Latest Orders</h3>
        <hr class="horizontal-line">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Total Price</th>
                    <th>Date Ordered</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($latestOrders as $order): ?>
                    <tr>
                        <td><a href="/page/backend/viewOrder.php" class="table-data-link"><?=  $order->order_id; ?></a></td>
                        <td><a href="/page/backend/viewCustomer.php" class="table-data-link"><?=  $order->user_username; ?></a></td>
                        <td><?php
                            $badge = getStatusBadge($order->order_status);
                            ?>
                            <span class="badge <?= $badge['class']; ?>">
                                <?=  $badge['text']; ?>
                            </span>
                        </td>
                        <td>RM <?=  $order->order_total; ?></td>
                        <td> <?=  convertDateFormat($order->dateOrdered); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="/page/backend/orders.php" class="table-data-link" style="margin-top:10px;text-align: center; display:block;">View All Order</a>
    </div>

    <div class="flex">
        <div class="dashboard-table-container" style="width:45%">
            <h3>Latest Customers</h3>
            <hr class="horizontal-line">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Contact Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestUsers as $userList): ?>
                        <tr>
                            <td><a href="/page/backend/viewCustomer.php" class="table-data-link"><?= $userList->user_id; ?></a></td>
                            <td><?= $userList->user_username; ?></td>
                            <td><?= $userList->user_email; ?></td>
                            <td><?= $userList->user_contact; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="/page/backend/customers.php" class="table-data-link" style="margin-top:10px;text-align: center; display:block;">View All Customer</a>
        </div>

        <div class="dashboard-table-container" style="width:45%">
            <h3>Recently Added Products</h3>
            <hr class="horizontal-line">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Product Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestProducts as $productList): ?>
                        <tr>
                            <td><a href="/page/backend/viewCustomer.php" class="table-data-link"><?= $productList->product_id; ?></a></td>
                            <td><img style="width:50px;height:50px;color:white;" src="/images/default-50x50.jpg"></td>
                            <td><?= $productList->product_name; ?></td>
                            <td>RM<?= $productList->product_price; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="/page/backend/products.php" class="table-data-link" style="margin-top:10px;text-align: center; display:block;">View All Customer</a>
        </div>
    </div>
</main>

<?php
include '../../_foot.php';
