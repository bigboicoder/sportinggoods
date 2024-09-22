<?php
require '../../_base.php';
// Admin role
auth('Admin');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a category ID is provided
if (!isset($_GET['id'])) {
    header("Location: /page/backend/categories.php?message=No category ID provided.&status=error");
    exit();
}

$categoryId = intval($_GET['id']);

// Fetch category details for the given ID
$categorySql = "SELECT * FROM Category WHERE category_id = ?";
$categoryStmt = $conn->prepare($categorySql);
$categoryStmt->bind_param("i", $categoryId);
$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();
$category = $categoryResult->fetch_assoc();

if (!$category) {
    header("Location: /page/backend/categories.php?message=Category not found.&status=error");
    exit();
}

// Fetch the message from the URL if available
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';

// Page title
$_title = 'Update Category';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/categories.php">Categories</a> &gt; <span>Update Category</span>
    </div>
    <div class="page-header">
        <h2>Update Category</h2>
    </div>

    <?php if (!empty($message)) : ?>
        <div id="message" class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="updateCategoryProcess.php">
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Category Detail</h3>
            </div>

            <!-- Category ID (disabled) -->
            <div class="row-input-container">
                <label>Category ID<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" name="category_id" value="<?php echo $category['category_id']; ?>" disabled>
                    <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                </div>
            </div>

            <!-- Category Name -->
            <div class="row-input-container">
                <label>Category Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" name="category_name" value="<?php echo $category['category_name']; ?>" required>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-submit-button">
                <input type="submit" class="button-blue" value="Update">
            </div>
        </div>
    </form>
</main>

<?php
$conn->close();
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
