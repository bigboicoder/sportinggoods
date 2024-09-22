<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$listOrders = $_db->query('
    SELECT orders.order_id, users.user_username, orders.order_status, orders.order_subtotal,orders.order_total, orders.dateOrdered
    FROM Orders
    JOIN users ON orders.user_id = users.user_id
    ')->fetchAll();

// ----------------------------------------------------------------------------
$_title = 'Orders';
include '../../_head.php';
?>
<title><?= $_title; ?></title>

<!-- Main Content -->
<main class="main-content">
    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <span>Orders</span>
    </div>
    <div class="page-header">
        <h1>Orders</h1>
    </div>

    <form>
        <div class="filter-container">
            <div class="filters">
                <label for="filter1">Filters :</label>
                <input type="text" id="filter1" name="filter1" placeholder="Order ID">
                <input type="text" id="filter1" name="filter1" placeholder="Customer Username">
                <select id="filter2" name="filter2">
                    <option selected disabled>Status</option>
                    <option value="">Pending</option>
                    <option value="">Processing</option>
                    <option value="">Cancel</option>
                    <option value="">Complete</option>
                </select>
                <input type="date" class="test" id="filter1" name="filter1">
            </div>
            <div class="filter-button">
                <div>
                    <input type="button" class="button-blue" value="Submit">
                </div>
                <div>
                    <input type="reset" value="Reset">
                </div>
            </div>
        </div>
    </form>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th><a href="#">No</a></th>
                    <th><a href="#">Order ID</a></th>
                    <th><a href="#">Username</a></th>
                    <th><a href="#">Status</a></th>
                    <th><a href="#">Subtotal</a></th>
                    <th><a href="#">Total</a></th>
                    <th><a href="#">Date Created</a></th>
                    <th><a href="#">Action</a></th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($listOrders as $order): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $order->order_id; ?></td>
                        <td><a href="/page/backend/viewCustomer.php" class="table-data-link"><?= $order->user_username; ?></a></td>
                        <td><?php
                            $badge = getStatusBadge($order->order_status);
                            ?>
                            <span class="badge <?= $badge['class']; ?>">
                                <?= $badge['text']; ?>
                            </span>
                        </td>
                        <td>RM<?= $order->order_subtotal; ?></td>
                        <td>RM<?= $order->order_total; ?></td>
                        <td><?= convertDateFormat($order->dateOrdered); ?></td>
                        <td>
                            
                            <div class="dropdown">
                                <button class="dropdown-btn button-normal">Action</button>
                                <div class="dropdown-content">
                                    <button data-get="/page/backend/viewOrder.php?id=<?= $order->order_id ?>">View Order</button>
                                    <button data-get="/page/backend/updateOrder.php?id=<?= $order->order_id ?>">Update Order</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php
include '../../_foot.php';
