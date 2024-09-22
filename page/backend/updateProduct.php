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

// Fetch all subcategories
$subCategorySql = "SELECT subCategory_id, subcategory_name FROM SubCategory";
$subCategoryResult = $conn->query($subCategorySql);

// Fetch selected subcategories for this product
$selectedSubCategoriesSql = "SELECT subCategory_id FROM ProductCategory WHERE product_id = ?";
$selectedStmt = $conn->prepare($selectedSubCategoriesSql);
$selectedStmt->bind_param("i", $product_id);
$selectedStmt->execute();
$selectedResult = $selectedStmt->get_result();

$selectedSubCategories = [];
while ($row = $selectedResult->fetch_assoc()) {
    $selectedSubCategories[] = $row['subCategory_id'];
}

$selectedStmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_stock = $_POST['product_stock'];
    $product_price = $_POST['product_price'];

    // Handle image upload
    $new_image_paths = [];
    if (isset($_FILES['product_image'])) {
        $image_files = $_FILES['product_image'];
        $upload_dir = '../backend/uploads/productImage/';
        
        foreach ($image_files['tmp_name'] as $key => $tmp_name) {
            $image_name = basename($image_files['name'][$key]);
            $upload_file = $upload_dir . $image_name;

            if (move_uploaded_file($tmp_name, $upload_file)) {
                $new_image_paths[] = $image_name;
            }
        }
    }

       // Update product details in Product table
       $image_paths_string = implode(',', $new_image_paths);
       $sql = "UPDATE Product SET product_name = ?, product_description = ?, product_stock = ?, product_price = ?" .
              ($image_paths_string ? ", product_image = '$image_paths_string'" : "") .
              " WHERE product_id = ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param('ssidi', $product_name, $product_description, $product_stock, $product_price, $product_id);
   
       if ($stmt->execute()) {
           // Delete existing subcategories for the product
           $deleteSubCategorySql = "DELETE FROM ProductCategory WHERE product_id = ?";
           $deleteStmt = $conn->prepare($deleteSubCategorySql);
           $deleteStmt->bind_param("i", $product_id);
           $deleteStmt->execute();
           $deleteStmt->close();
   
           // Insert new subcategory selections
           if (isset($_POST['subcategory']) && is_array($_POST['subcategory'])) {
               foreach ($_POST['subcategory'] as $subCategoryId) {
                   $insertSubCategorySql = "INSERT INTO ProductCategory (product_id, subCategory_id) VALUES (?, ?)";
                   $insertStmt = $conn->prepare($insertSubCategorySql);
                   $insertStmt->bind_param("ii", $product_id, $subCategoryId);
                   $insertStmt->execute();
                   $insertStmt->close();
               }
           }
   
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

    <form id="product-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
        <div class="detail-container">
            <div class="sub-heading">
            <h3>Product Detail</h3>
            </div>
            <div class="row-input-container">
                <label>Product Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" name="product_name" value="<?php echo isset($row['product_name']) ? htmlspecialchars($row['product_name']) : ''; ?>" required>
                    <div class="error-message" id="error-product-name"></div>
                </div>
            </div>
            <div class="row-input-container">
                <label>Product Description<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <textarea name="product_description" style="resize:none; height:75px;" required><?php echo isset($row['product_description']) ? htmlspecialchars($row['product_description']) : ''; ?></textarea>
                    <div class="error-message" id="error-product-description"></div>
                </div>
            </div>
            <div class="row-input-container">
                <label>Product Stock<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="number" name="product_stock" value="<?php echo isset($row['product_stock']) ? htmlspecialchars($row['product_stock']) : ''; ?>" required>
                    <div class="error-message" id="error-product-stock"></div>
                </div>
            </div>
            <div class="row-input-container">
                <label>Product Price<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" name="product_price" value="<?php echo isset($row['product_price']) ? htmlspecialchars($row['product_price']) : ''; ?>" required>
                    <div class="error-message" id="error-product-price"></div>
                </div>
            </div>

            <!-- Subcategory Checkboxes -->
            <div class="row-input-container">
                <label>Subcategory<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <div class="input-dropdown">
                        <div class="input-dropdown-content">
                            <?php
                            if ($subCategoryResult->num_rows > 0) {
                                while ($subRow = $subCategoryResult->fetch_assoc()) {
                                    $checked = in_array($subRow['subCategory_id'], $selectedSubCategories) ? 'checked' : '';
                                    echo "<label><input type='checkbox' name='subcategory[]' value='{$subRow['subCategory_id']}' {$checked}> {$subRow['subcategory_name']}</label>";
                                }
                            } else {
                                echo "<p>No subcategories available.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Images Section -->
            <div class="row-input-container">
                <label>Product Images<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <div id="existing-images" style="display: flex; flex-wrap: wrap;">
                        <?php
                        if (isset($row['product_image'])) {
                            $images = explode(',', $row['product_image']);
                            foreach ($images as $image) {
                                $imagePath = '../backend/uploads/productImage/' . trim($image);
                                if (file_exists($imagePath)) {
                                    echo "
                                        <div class='image-preview-container' style='position: relative; margin-right: 10px;'>
                                            <img src='" . htmlspecialchars($imagePath) . "' width='150' height='150'>
                                            <button type='button' class='delete-button' data-image='" . htmlspecialchars($image) . "'>X</button>
                                        </div>
                                    ";
                                } else {
                                    echo "
                                        <div class='image-preview-container' style='position: relative; margin-right: 10px;'>
                                            <img src='/images/default-150x150.jpg' width='150' height='150' alt='Default Image'>
                                            <button type='button' class='delete-button' data-image='" . htmlspecialchars($image) . "'>X</button>
                                        </div>
                                    ";
                                }
                            }
                        } else {
                            echo "<img src='/images/default-150x150.jpg' width='150' height='150' alt='Default Image'>";
                        }
                        ?>
                    </div>

                    <button type="button" id="toggle-drag-drop">Add or Change Images</button>
                    <div class="drop-files-container" id="drop-files-container">
                        Drag and drop files here or <button type="button" id="upload-button">Upload Photos</button>
                        <input type="file" id="file-input" name="product_image[]" style="display: none;" accept="image/*" multiple>
                        <div id="image-previews"></div>
                    </div>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var uploadFormData = new FormData(); // Global variable to store files

    // Trigger file input when button is clicked
    document.getElementById('upload-button').addEventListener('click', function() {
        document.getElementById('file-input').click();
    });

    // Handle file selection and display the image
    document.getElementById('file-input').addEventListener('change', function(event) {
        const files = event.target.files;
        if (files.length > 0) {
            handleFileUpload(files);
        }
    });

    // Drag and drop functionality
    const dropContainer = document.getElementById('drop-files-container');
    dropContainer.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropContainer.classList.add('dragover');
    });

    dropContainer.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropContainer.classList.remove('dragover');
    });

    dropContainer.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropContainer.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileUpload(files);
        }
    });

// Toggle visibility of the drag-and-drop area
document.getElementById('toggle-drag-drop').addEventListener('click', function() {
    const dropFilesContainer = document.getElementById('drop-files-container');

    if (dropFilesContainer.classList.contains('hidden')) {
        // Remove the hidden class and add the show class
        dropFilesContainer.classList.remove('hidden');
        dropFilesContainer.classList.add('show');
        this.textContent = 'Hide Images Area'; // Change button text to indicate the area is visible
    } else {
        // Remove the show class and add the hidden class
        dropFilesContainer.classList.remove('show');
        dropFilesContainer.classList.add('hidden');
        this.textContent = 'Add or Change Images'; // Change button text to indicate the area is hidden
    }
});

// Ensure the initial state is correctly applied on page load
window.addEventListener('load', function() {
    const dropFilesContainer = document.getElementById('drop-files-container');
    
    // Ensure the initial state is properly set
    if (dropFilesContainer.classList.contains('show')) {
        dropFilesContainer.classList.remove('hidden');
        dropFilesContainer.classList.add('show');
    } else {
        dropFilesContainer.classList.remove('show');
        dropFilesContainer.classList.add('hidden');
    }
});

    function handleFileUpload(files) {
        const imagePreviews = document.getElementById('image-previews');

        for (let i = 0; i < files.length; i++) {
            uploadFormData.append("files[]", files[i]);

            const file = files[i];
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.classList.add('image-preview-container');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('image-preview');

                    const deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.classList.add('delete-button');
                    deleteButton.textContent = 'X';

                    deleteButton.addEventListener('click', function() {
                        imagePreviews.removeChild(imgContainer);
                        uploadFormData.delete("files[]", file);
                    });

                    imgContainer.appendChild(img);
                    imgContainer.appendChild(deleteButton);
                    imagePreviews.appendChild(imgContainer);
                };
                reader.readAsDataURL(file);
            }
        }
    }

    document.getElementById('product-form').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission.

    clearErrors(); // Clear any previous errors.

    var isValid = true; // Flag to track validation status.
    var formData = new FormData(this); // Get form data.

    // Retrieve form data entries for validation.
    var productName = formData.get('product_name').trim();
    var productDescription = formData.get('product_description').trim();
    var productStock = parseInt(formData.get('product_stock'), 10);
    var productPrice = formData.get('product_price').trim();

    // Validate Product Name
    if (!productName) {
        showError('error-product-name', 'Product name is required.');
        isValid = false;
    }

    // Validate Product Description
    if (!productDescription) {
        showError('error-product-description', 'Product description is required.');
        isValid = false;
    }

    // Validate Product Stock
    if (isNaN(productStock) || productStock < 0) {
        showError('error-product-stock', 'Product stock must be a non-negative number.');
        isValid = false;
    }

    // Validate Product Price using regex to ensure it's a valid number
    if (!productPrice.match(/^\d+(\.\d{1,2})?$/)) {
        showError('error-product-price', 'Product price must be a valid number with up to two decimal places.');
        isValid = false;
    }

    // Proceed with AJAX submission if all validations pass
    if (isValid) {
        $.ajax({
            url: "productUpdate.php",
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                alert("Product updated successfully.");
                window.location.href = "/page/backend/products.php?message=Product updated successfully.";
            },
            error: function(xhr, status, error) {
                alert("Failed to update product: " + error);
            }
        });
    }
});

function showError(elementId, message) {
    var element = document.getElementById(elementId);
    element.textContent = message;
    element.style.display = 'block'; // Show the error message
}

function clearErrors() {
    var errors = document.querySelectorAll('.error-message');
    errors.forEach(function(error) {
        error.style.display = 'none';
        error.textContent = '';
    });
}


    // Existing code...

    // Delete existing image
    document.querySelectorAll('#existing-images .delete-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var imageName = this.getAttribute('data-image');
            var container = this.parentElement;

            if (confirm('Are you sure you want to delete this image?')) {
                $.ajax({
                    url: 'deleteImage.php', // Replace with your actual delete URL
                    type: 'POST',
                    data: { image: imageName, product_id: <?php echo $product_id; ?> },
                    success: function(response) {
                        container.remove();
                    },
                    error: function(xhr, status, error) {
                        console.error("Failed to delete image: " + error);
                        alert("Failed to delete image: " + error);
                    }
                });
            }
        });
    });

    // Align all images, including new ones added
    function handleFileUpload(files) {
        const imagePreviews = document.getElementById('image-previews');

        for (let i = 0; i < files.length; i++) {
            uploadFormData.append("files[]", files[i]);

            const file = files[i];
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.classList.add('image-preview-container');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('image-preview');

                    const deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.classList.add('delete-button');
                    deleteButton.textContent = 'X';

                    deleteButton.addEventListener('click', function() {
                        imagePreviews.removeChild(imgContainer);
                        uploadFormData.delete("files[]", file);
                    });

                    imgContainer.appendChild(img);
                    imgContainer.appendChild(deleteButton);
                    imagePreviews.appendChild(imgContainer);
                };
                reader.readAsDataURL(file);
            }
        }
    }
</script>
