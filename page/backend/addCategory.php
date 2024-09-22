<?php
// Include your base file if needed
require '../../_base.php';

// Admin role
auth('Admin');

// Page title
$_title = 'Add Category';
include '../../_head.php';

// Fetch the message from the URL if available
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/categories.php">Categories</a> &gt; <span>Add Category</span>
    </div>
    <div class="page-header">
        <h2>Add Category</h2>
    </div>

    <?php if (!empty($message)) : ?>
        <div id="message" class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="uploadCategory.php">
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Category Detail</h3>
            </div>
            <div class="row-input-container">
                <label>Category Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" name="category_name" value="<?php echo isset($_POST['category_name']) ? htmlspecialchars($_POST['category_name']) : ''; ?>">
                    <?php if (!empty($message) && strpos($message, 'required') !== false) : ?>
                        <span class="error-message">Category name is required *</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-submit-button">
                <input type="submit" class="button-blue" value="Create">
            </div>
        </div>
    </form>

</main>

<?php
include '../../_foot.php';
?>

<script>
    // Check if the message element exists
    var messageElement = document.getElementById('message');
    if (messageElement) {
        // Set a timeout to remove the message after 3 seconds
        setTimeout(function() {
            messageElement.style.opacity = 0;
            setTimeout(function() {
                messageElement.style.display = 'none';
            }, 500); // Match this time with the opacity transition
        }, 3000); // 3 seconds
    }
</script>
