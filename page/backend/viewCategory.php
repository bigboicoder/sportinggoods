<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$id = req('id');
$stm = $_db->prepare('
    SELECT *
    FROM 
        Users
    WHERE 
        user_id = ?
        ');
$stm->execute([$id]);
$admin = $stm->fetch();
$adminStatus = $admin->user_status == 1 ? 'Activate' : 'Block';
// ----------------------------------------------------------------------------
$_title = 'View Admin';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/customers.php">Customers</a> &gt; <span>View
        Users</span>
    </div>
    <div class="page-header">
        <h2>View Users</h2>
    </div>
    <form>
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Users Detail</h3>
            </div>
            <div class="row-input-container">
                <label>Full Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_text('name', 'disabled', $admin->user_name) ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Username<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_text('username', 'disabled', $admin->user_username) ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Photo<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <img src="/uploads/<?= $admin->user_photo ?>" style="width:150px;height:150px;border:1px solid #ced4da;padding:5px;">
                </div>
            </div>
            <div class="row-input-container">
                <label>Email<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_text('email', 'disabled', $admin->user_email) ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Contact Number<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_text('contactNumber', 'disabled', $admin->user_contact) ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Status<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_select('status', ['1' => 'Activate', '0' => 'Block'], $adminStatus, '','disabled'); ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Date Created<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                <?= html_text('date', 'disabled', $admin->dateCreated) ?>
                </div>
            </div>
            <div class="form-submit-button">
                <button data-get="/page/backend/updateCustomer.php?id=<?= $admin->user_id ?>" class="button-blue">Update Admin</button>
            </div>
        </div>
    </form>
</main>
<?php
include '../../_foot.php';
