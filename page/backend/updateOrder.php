<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$order_sql = "
    SELECT 
        orders.*,
        transactions.payment_method,
        users.user_username,
        users.user_email,
        users.user_contact
    FROM 
        orders
    LEFT JOIN 
        transactions ON orders.order_id = transactions.order_id
    LEFT JOIN 
        users ON orders.user_id = users.user_id
    WHERE 
        orders.order_id = ?
";

$orderItem_sql = "
    SELECT 
        orderItem.*,
        product.product_name,
        product.product_description,
        product.product_price,
        product.product_image
    FROM 
        orderItem
    INNER JOIN 
        product ON orderItem.product_id = product.product_id
    WHERE 
        orderItem.order_id = ?
";

$id = req('id');
$stm = $_db->prepare($order_sql);
$stm->execute([$id]);
$orderDetail = $stm->fetch();

$stm = $_db->prepare($orderItem_sql);
$stm->execute([$id]);
$orderItemDetail = $stm->fetchAll();

if (is_post()) {
    $newStatus = $_POST['updateOrderStatus'];
    if ($orderDetail->order_status !== $newStatus) {

        $update_sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stm = $_db->prepare($update_sql);
        $stm->execute([$newStatus, $id]);

        $orderDetail->order_status = $newStatus;
    }
}

// ----------------------------------------------------------------------------
$_title = 'Update Order';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/orders.php">Orders</a> &gt; <span>Update
            Order</span>
    </div>
    <div class="page-header">
        <h2>Update Order</h2>
    </div>
    <form method="post">
        <div class="order-container">
            <div class="container-heading">
                <div class="sub-heading order-heading-text" style="width:50%;">
                    <h3><i class="fa fa-globe" aria-hidden="true"></i> Order # ORD-<?= $orderDetail->order_id ?></h3>
                    <small>Ordered Date: <?= convertDateFormat($orderDetail->dateOrdered); ?></small>
                </div>
                <div class="order-heading-button" style="width:15%;">
                    <input type="submit" class="button-blue" value="Update">
                </div>
            </div>
            <div class="container-customer">
                <h3 style="margin:0;margin-bottom:10px;">Customer Info</h3>
                <strong>Username: </strong><a href="/page/backend/viewCustomer.php" class="table-data-link"><?= $orderDetail->user_username ?></a><br>
                <strong>Email: </strong><?= $orderDetail->user_email ?><br>
                <strong>Contact Number: </strong>(+60) <?= $orderDetail->user_contact ?><br>
            </div>
            <div class="container-order-table">
                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Product Price</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItemDetail as $productList): ?>
                            <tr>
                                <td><a href="/page/backend/viewProduct.php" class="table-data-link"><?= $productList->product_id ?></a></td>
                                <td><img style="width: 50px;height:50px" src="/images/default-50x50.jpg"></td>
                                <td><?= $productList->product_name ?></td>
                                <td>Plants</td>
                                <td>RM<?= $productList->product_price ?></td>
                                <td><?= $productList->quantity ?></td>
                                <td>RM<?= $productList->product_price * $productList->quantity ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="container-order-footer">
                <div class="payment-method">
                    <p>Payment Methods</p>
                    <input class="default-select" type="text" value="<?= $orderDetail->payment_method ?>" disabled>
                    <p>Order Status</p>
                    <select class="default-select" name="updateOrderStatus">
                        <?php
                        $statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];
                        foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= $orderDetail->order_status === $status ? 'selected' : '' ?>>
                                <?= $status ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="order-total">
                    <table style="border-collapse: collapse;width:100%;">
                        <tr>
                            <th>Subtotal:</th>
                            <td>RM<?= $orderDetail->order_subtotal ?></td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td>RM<?= $orderDetail->order_discount ?></td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td>RM<?= $orderDetail->order_total ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </form>
</main>
<?php
include '../../_foot.php';
