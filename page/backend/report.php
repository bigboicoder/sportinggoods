<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

//Admin role
auth('Admin');

// ----------------------------------------------------------------------------
$_title = 'Report';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">
    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <span>Report</span>
    </div>
    <div class="page-header">
        <h1>Report</h1>
        <div class="button-add">
        </div>
    </div>

    <div class="report-container">
        <div class="report-box">
            <span class="report-text">Total Orders: 1,230</span>
            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
        </div>
        <div class="report-box">
            <span class="report-text">Total Revenue: RM 120,450</span>
            <i class="fas fa-dollar-sign" aria-hidden="true"></i>
        </div>
        <div class="report-box">
            <span class="report-text">Average Order Value: RM 98</span>
            <i class="fas fa-chart-line" aria-hidden="true"></i>
        </div>
        <div class="report-box">
            <span class="report-text">Conversion Rate: 2.5%</span>
            <i class="fas fa-percentage" aria-hidden="true"></i>
        </div>
        <div class="report-box">
            <span class="report-text">Customer Acquisition Cost: RM 45 per customer</span>
            <i class="fas fa-user-plus" aria-hidden="true"></i>
        </div>
        <div class="report-box">
            <span class="report-text">Customer Retention Rate: 75%</span>
            <i class="fas fa-users" aria-hidden="true"></i>
        </div>
    </div>

    <div class="subheading">
        <h3>Topseller Product By Order</h3>
    </div>

    <div class="report-table" style="margin-bottom: 35px;">
        <table>
            <tr style="background-color: #f0f0f0;">
                <th>Rank</th>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total Orders</th>
                <th>Revenue</th>
            </tr>
            <tr>
                <td>1</td>
                <td><img style="width: 50px;height:50px;" src="/images/default-50x50.jpg"></td>
                <td><a class="table-data-link" href="/page/backend/viewProduct.php">DDR4</a></td>
                <td>Monitor</td>
                <td>RM11.11</td>
                <td>123</td>
                <td>123</td>
                <td>RM123123</td>
            </tr>
            <tr>
                <td>1</td>
                <td><img style="width: 50px;height:50px;" src="/images/default-50x50.jpg"></td>
                <td><a class="table-data-link" href="/page/backend/viewProduct.php">DDR4</a></td>
                <td>Monitor</td>
                <td>RM11.11</td>
                <td>123</td>
                <td>123</td>
                <td>RM123123</td>
            </tr><tr>
                <td>1</td>
                <td><img style="width: 50px;height:50px;" src="/images/default-50x50.jpg"></td>
                <td><a class="table-data-link" href="/page/backend/viewProduct.php">DDR4</a></td>
                <td>Monitor</td>
                <td>RM11.11</td>
                <td>123</td>
                <td>123</td>
                <td>RM123123</td>
            </tr><tr>
                <td>1</td>
                <td><img style="width: 50px;height:50px;" src="/images/default-50x50.jpg"></td>
                <td><a class="table-data-link" href="/page/backend/viewProduct.php">DDR4</a></td>
                <td>Monitor</td>
                <td>RM11.11</td>
                <td>123</td>
                <td>123</td>
                <td>RM123123</td>
            </tr><tr>
                <td>1</td>
                <td><img style="width: 50px;height:50px;" src="/images/default-50x50.jpg"></td>
                <td><a class="table-data-link" href="/page/backend/viewProduct.php">DDR4</a></td>
                <td>Monitor</td>
                <td>RM11.11</td>
                <td>123</td>
                <td>123</td>
                <td>RM123123</td>
            </tr><tr>
                <td>1</td>
                <td><img style="width: 50px;height:50px;" src="/images/default-50x50.jpg"></td>
                <td><a class="table-data-link" href="/page/backend/viewProduct.php">DDR4</a></td>
                <td>Monitor</td>
                <td>RM11.11</td>
                <td>123</td>
                <td>123</td>
                <td>RM123123</td>
            </tr><tr>
                <td>1</td>
                <td><img style="width: 50px;height:50px;" src="/images/default-50x50.jpg"></td>
                <td><a class="table-data-link" href="/page/backend/viewProduct.php">DDR4</a></td>
                <td>Monitor</td>
                <td>RM11.11</td>
                <td>123</td>
                <td>123</td>
                <td>RM123123</td>
            </tr><tr>
                <td>1</td>
                <td><img style="width: 50px;height:50px;" src="/images/default-50x50.jpg"></td>
                <td><a class="table-data-link" href="/page/backend/viewProduct.php">DDR4</a></td>
                <td>Monitor</td>
                <td>RM11.11</td>
                <td>123</td>
                <td>123</td>
                <td>RM123123</td>
            </tr><tr>
                <td>1</td>
                <td><img style="width: 50px;height:50px;" src="/images/default-50x50.jpg"></td>
                <td><a class="table-data-link" href="/page/backend/viewProduct.php">DDR4</a></td>
                <td>Monitor</td>
                <td>RM11.11</td>
                <td>123</td>
                <td>123</td>
                <td>RM123123</td>
            </tr>
        </table>
    </div>
</main>

<?php
include '../../_foot.php';
