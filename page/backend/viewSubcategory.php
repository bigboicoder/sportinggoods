<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

//Admin role
auth('Admin');

// ----------------------------------------------------------------------------
$_title = 'View Subcategory';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/subcategory.php">Subcategory</a> &gt; <span>View
            Subcategory</span>
    </div>
    <div class="page-header">
        <h2>View Subcategory</h2>
    </div>
    <form>
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Subcategory Detail</h3>
            </div>
            <div class="row-input-container">
                <label>Subcategory ID<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" value="1" disabled>
                </div>
            </div>
            <div class="row-input-container">
                <label>Category<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <div class="input-dropdown">
                        <div class="input-dropdown-content">
                            <label><input type="checkbox" value="Option 1" checked disabled>Option 1</label>
                            <label><input type="checkbox" value="Option 2" disabled>Option 2</label>
                            <label><input type="checkbox" value="Option 3" disabled>Option 3</label>
                            <label><input type="checkbox" value="Option 4" checked disabled>Option 4</label>
                            <label><input type="checkbox" value="Option 5" checked disabled>Option 5</label>
                            <label><input type="checkbox" value="Option 6" checked disabled>Option 6</label>
                            <label><input type="checkbox" value="Option 7" checked disabled>Option 7</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row-input-container">
                <label>Subcategory Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" value="testing" disabled>
                    <span class="error-message">Email is required *</span>
                </div>
            </div>
            <div class="form-submit-button">
                <a href="/page/backend/updateSubcategory.php" class="button-blue">Update Subcategory</a>
            </div>
        </div>
    </form>
</main>

<?php
include '../../_foot.php';
