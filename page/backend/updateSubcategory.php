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

// Check if a subcategory ID is provided
if (!isset($_GET['id'])) {
    header("Location: /page/backend/subcategory.php?message=No subcategory ID provided.&status=error");
    exit();
}

$subCategoryId = intval($_GET['id']);

// Fetch subcategory details for the given ID
$subCategorySql = "SELECT * FROM SubCategory WHERE subCategory_id = ?";
$subCategoryStmt = $conn->prepare($subCategorySql);
$subCategoryStmt->bind_param("i", $subCategoryId);
$subCategoryStmt->execute();
$subCategoryResult = $subCategoryStmt->get_result();
$subCategory = $subCategoryResult->fetch_assoc();

if (!$subCategory) {
    header("Location: /page/backend/subcategory.php?message=Subcategory not found.&status=error");
    exit();
}

// Fetch categories for the dropdown
$sql = "SELECT category_id, category_name FROM Category";
$result = $conn->query($sql);

// Fetch the message from the URL if available
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';

// Page title
$_title = 'Update Subcategory';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">
    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/subcategory.php">Subcategory</a> &gt; <span>Update Subcategory</span>
    </div>
    <div class="page-header">
        <h2>Update Subcategory</h2>
    </div>

    <?php if (!empty($message)) : ?>
        <div id="message" class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="updateSubCategoryProcess.php">
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Subcategory Detail</h3>
            </div>

            <!-- Subcategory ID (disabled) -->
            <div class="row-input-container">
                <label>Subcategory ID<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" name="subCategory_id" value="<?php echo $subCategory['subCategory_id']; ?>" disabled>
                    <input type="hidden" name="subCategory_id" value="<?php echo $subCategory['subCategory_id']; ?>">
                </div>
            </div>

            <!-- Category Dropdown -->
            <div class="row-input-container">
                <label>Category<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $selected = $row['category_id'] == $subCategory['category_id'] ? 'selected' : '';
                                echo "<option value='".$row['category_id']."' $selected>".$row['category_name']."</option>";
                            }
                        } else {
                            echo "<option value=''>No categories available</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Subcategory Name -->
            <div class="row-input-container">
                <label>Subcategory Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" name="subcategory_name" value="<?php echo $subCategory['subcategory_name']; ?>" required>
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
