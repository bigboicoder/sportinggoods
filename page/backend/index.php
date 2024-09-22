<?php
require '_base.php';
//-----------------------------------------------------------------------------
//Admin role
auth('Admin');



// ----------------------------------------------------------------------------
$_title = 'Index';
include '_head.php';
redirect('/page/backend/dashboard.php');
?>
<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h1>Nothing here</h1>
        <div class="button-add">
        </div>
    </div>
</main>

<?php
include '_foot.php';
