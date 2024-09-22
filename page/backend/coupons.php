<?php
require '../../_base.php';
//-----------------------------------------------------------------------------
//Admin role
auth('Admin');


// ----------------------------------------------------------------------------
$_title = 'Coupon';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">
    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <span>Coupons</span>
    </div>
    <div class="page-header">
        <h1>Coupons</h1>
        <div class="button-add">
            <a href="/page/backend/addCoupon.php" class="button-normal">Add Coupon</a>
        </div>
    </div>


    <form>
        <div class="filter-container">
            <div class="filters">
                <label for="filter1">Filters :</label>
                <input type="text" id="filter1" name="filter1" placeholder="Coupon ID">
                <input type="text" id="filter1" name="filter1" placeholder="Coupon Code">
                <select id="filter2" name="filter2">
                    <option value="">Percent Amount</option>
                    <option value="">Amount Discount</option>
                </select>
                <input type="text" id="filter1" name="filter1" placeholder="Coupon Amount">
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
                    <th><a href="#">Coupon ID</a></th>
                    <th><a href="#">Coupon Code</a></th>
                    <th><a href="#">Coupon Type</a></th>
                    <th><a href="#">Coupon Amount</a></th>
                    <th><a href="#">Expiry Date</a></th>
                    <th><a href="#">Date Created</a></th>
                    <th><a href="#">Action</a></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>1001</td>
                    <td>QQWEFD</td>
                    <td>Percent Amount</td>
                    <td>23</td>
                    <td>2017-09-29</td>
                    <td>2017-09-29 01:22</td>
                    <td>
                        <div class="dropdown">
                            <button class="dropdown-btn button-normal">Action</button>
                            <div class="dropdown-content">
                                <a href="/page/backend/viewCoupon.php">View Coupon</a>
                                <a href="/page/backend/updateCoupon.php">Update Coupon</a>
                                <a href="#">Delete Admin</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>1001</td>
                    <td>QQWEFD</td>
                    <td>Amount Discount</td>
                    <td>23</td>
                    <td>2017-09-29</td>
                    <td>2017-09-29 01:22</td>
                    <td>
                        <div class="dropdown">
                            <button class="dropdown-btn button-normal">Action</button>
                            <div class="dropdown-content">
                                <a href="/page/backend/viewCoupon.php">View Coupon</a>
                                <a href="/page/backend/updateCoupon.php">Update Coupon</a>
                                <a href="#">Delete Admin</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>1001</td>
                    <td>QQWEFD</td>
                    <td>Amount Discount</td>
                    <td>23</td>
                    <td>2017-09-29</td>
                    <td>2017-09-29 01:22</td>
                    <td>
                        <div class="dropdown">
                            <button class="dropdown-btn button-normal">Action</button>
                            <div class="dropdown-content">
                                <a href="/page/backend/viewCoupon.php">View Coupon</a>
                                <a href="/page/backend/updateCoupon.php">Update Coupon</a>
                                <a href="#">Delete Admin</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>1001</td>
                    <td>QQWEFD</td>
                    <td>Amount Discount</td>
                    <td>23</td>
                    <td>2017-09-29</td>
                    <td>2017-09-29 01:22</td>
                    <td>
                        <div class="dropdown">
                            <button class="dropdown-btn button-normal">Action</button>
                            <div class="dropdown-content">
                                <a href="/page/backend/viewCoupon.php">View Coupon</a>
                                <a href="/page/backend/updateCoupon.php">Update Coupon</a>
                                <a href="#">Delete Admin</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>1001</td>
                    <td>QQWEFD</td>
                    <td>Percent Amount</td>
                    <td>23</td>
                    <td>2017-09-29</td>
                    <td>2017-09-29 01:22</td>
                    <td>
                        <div class="dropdown">
                            <button class="dropdown-btn button-normal">Action</button>
                            <div class="dropdown-content">
                                <a href="/page/backend/viewCoupon.php">View Coupon</a>
                                <a href="/page/backend/updateCoupon.php">Update Coupon</a>
                                <a href="#">Delete Admin</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>1001</td>
                    <td>QQWEFD</td>
                    <td>Percent Amount</td>
                    <td>23</td>
                    <td>2017-09-29</td>
                    <td>2017-09-29 01:22</td>
                    <td>
                        <div class="dropdown">
                            <button class="dropdown-btn button-normal">Action</button>
                            <div class="dropdown-content">
                                <a href="/page/backend/viewCoupon.php">View Coupon</a>
                                <a href="/page/backend/updateCoupon.php">Update Coupon</a>
                                <a href="#">Delete Admin</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>1001</td>
                    <td>QQWEFD</td>
                    <td>Percent Amount</td>
                    <td>23</td>
                    <td>2017-09-29</td>
                    <td>2017-09-29 01:22</td>
                    <td>
                        <div class="dropdown">
                            <button class="dropdown-btn button-normal">Action</button>
                            <div class="dropdown-content">
                                <a href="/page/backend/viewCoupon.php">View Coupon</a>
                                <a href="/page/backend/updateCoupon.php">Update Coupon</a>
                                <a href="#">Delete Admin</a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<?php
include '../../_foot.php';
