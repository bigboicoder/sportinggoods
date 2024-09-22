<?php
require '../../_base.php';
//-----------------------------------------------------------------------------



// ----------------------------------------------------------------------------
$_title = 'View/Update Product';
include '../../_head.php';

// Create connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from query string
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_stock = $_POST['product_stock'];
    $product_price = $_POST['product_price'];

    // Update product details
    $sql = "UPDATE Product SET product_name = ?, product_description = ?, product_stock = ?, product_price = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssidi', $product_name, $product_description, $product_stock, $product_price, $product_id);

    if ($stmt->execute()) {
        $message = "Product updated successfully.";
    } else {
        $message = "Error updating product: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch product details
$sql = "SELECT product_name, product_image, product_description, product_stock, product_price, dateCreated FROM Product WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if a product was found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    $row = null; // Set $row to null if no results found
}

$stmt->close();
$conn->close();
?>

<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/products.php">Products</a> &gt; <span>View/Update Product</span>
    </div>
    <div class="page-header">
        <h2>View/Update Product</h2>
    </div>

    <!-- Display messages if any -->
    <?php if (isset($message)) : ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Product Detail</h3>
            </div>
            <div class="row-input-container">
                <label>Product Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" name="product_name" value="<?php echo isset($row['product_name']) ? htmlspecialchars($row['product_name']) : ''; ?>" required>
                </div>
            </div>
            <div class="row-input-container">
                <label>Product Image<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?php
                    // Display multiple images
                    if (isset($row['product_image'])) {
                        $images = explode(',', $row['product_image']);
                        echo "<div style='display: flex;'>";
                        foreach ($images as $image) {
                            $imagePath = 'uploads/' . trim($image);
                            if (file_exists($imagePath)) {
                                echo "<img src='" . htmlspecialchars($imagePath) . "' width='150' height='150' style='margin-right:10px;'>";
                            } else {
                                echo "<img src='/images/default-150x150.jpg' width='150' height='150' style='margin-right:10px;' alt='Default Image'>";
                            }
                        }
                        echo "</div>";
                    } else {
                        echo "<img src='/images/default-150x150.jpg' width='150' height='150' alt='Default Image'>";
                    }
                    ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Product Description<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <textarea name="product_description" style="resize:none; height:75px;" required><?php echo isset($row['product_description']) ? htmlspecialchars($row['product_description']) : ''; ?></textarea>
                </div>
            </div>
            <div class="row-input-container">
                <label>Product Stock<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="number" name="product_stock" value="<?php echo isset($row['product_stock']) ? htmlspecialchars($row['product_stock']) : ''; ?>" required>
                </div>
            </div>
            <div class="row-input-container">
                <label>Product Price<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" name="product_price" value="<?php echo isset($row['product_price']) ? htmlspecialchars($row['product_price']) : ''; ?>" required>
                </div>
            </div>
            <div class="row-input-container">
                <label>Date Created<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" value="<?php echo isset($row['dateCreated']) ? htmlspecialchars($row['dateCreated']) : ''; ?>" disabled>
                </div>
            </div>
            <div class="form-submit-button">
                <input type="submit" class="button-blue" value="Update Product">
            </div>
        </div>
    </form>
</main>
<?php
include '../../_foot.php';
?>
