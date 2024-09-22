<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

//Admin role
auth('Admin');

// ----------------------------------------------------------------------------
$_title = 'View Coupon';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/coupons.php">Coupon</a> &gt; <span>View
        Coupon</span>
    </div>
    <div class="page-header">
        <h2>View Coupon</h2>
    </div>
    <form>
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Coupon Detail</h3>
            </div>
            <div class="row-input-container">
                <label>Coupon Code<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text"  value="testing" disabled>
                </div>
            </div>
            <div class="row-input-container">
                <label>Coupon Type<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <select disabled>
                        <option>Percent Amount</option>
                        <option>Amount Discount</option>
                    </select>
                </div>
            </div>
            <div class="row-input-container">
                <label>Coupon Amount<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" value="testing" disabled>
                    <span class="error-message">Email is required *</span>
                </div>
            </div>
            <div class="row-input-container">
                <label>Expiry Date<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="date" disabled>
                </div>
            </div>
            <div class="form-submit-button">
                <a href="/page/backend/updateCoupon.php" class="button-blue">Update Coupon</a>
            </div>
        </div>
    </form>
</main>
<?php
include '../../_foot.php';
