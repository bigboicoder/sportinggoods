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

// Fetch subcategories for the dropdown
$subCategorySql = "SELECT subCategory_id, subcategory_name FROM SubCategory";
$subCategoryResult = $conn->query($subCategorySql);

// Page title
$_title = 'Add Product';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/products.php">Products</a> &gt; <span>Add Product</span>
    </div>
    <div class="page-header">
        <h2>Add Product</h2>
    </div>
    <form id="product-form" enctype="multipart/form-data">
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Product Detail</h3>
            </div>
            <div class="row-input-container">
                <label for="product_name">Product Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" id="product_name" name="product_name" required>
                </div>
            </div>
            <div class="row-input-container">
                <label for="product_description">Product Description<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <textarea id="product_description" name="product_description" style="resize:none; height:75px;" required></textarea>
                </div>
            </div>
            <div class="row-input-container">
                <label for="product_stock">Product Stock<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" id="product_stock" name="product_stock" required>
                </div>
            </div>
            <div class="row-input-container">
                <label for="product_price">Product Price<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <input type="text" id="product_price" name="product_price" required>
                </div>
            </div>
            <div class="row-input-container">
                <label for="product_image">Product Images (optional)</label>
                <div class="row-input">
                    <div class="drop-files-container" id="drop-files-container">
                        Drag and drop files here or <button type="button" id="upload-button">Upload Photos</button>
                        <input type="file" id="file-input" name="product_image[]" style="display: none;" accept="image/*" multiple>
                        <div id="image-previews"></div>
                    </div>
                </div>
            </div>
            <!-- Subcategory Checkbox -->
            <div class="row-input-container">
                <label>Subcategory<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <div class="input-dropdown">
                        <div class="input-dropdown-content">
                            <?php
                            if ($subCategoryResult->num_rows > 0) {
                                while ($row = $subCategoryResult->fetch_assoc()) {
                                    echo "<label><input type='checkbox' value='{$row['subCategory_id']}' name='subcategory[]'>{$row['subcategory_name']}</label>";
                                }
                            } else {
                                echo "<p>No subcategories available.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-submit-button">
                <input type="submit" class="button-blue" value="Create">
            </div>
        </div>
    </form>

</main>

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

    // Always ensure the drop container is visible
    dropContainer.classList.remove('hidden');
    dropContainer.classList.add('show');

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

    function handleFileUpload(files) {
        const imagePreviews = document.getElementById('image-previews');

        // Add files to FormData
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

                    // Remove image from preview and FormData
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

    // Form submit handling with client-side validation
    document.getElementById('product-form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Client-side validation
        if (!validateForm()) {
            return; // Stop form submission if validation fails
        }

        var form = document.getElementById('product-form');
        var formData = new FormData(form);

        // Append existing files from uploadFormData to formData
        uploadFormData.forEach(function(value, key) {
            formData.append(key, value);
        });

        $.ajax({
            url: "upload.php", // Replace with your target upload URL
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(ret) {
                console.log("Product added successfully.");
                alert("Product added successfully.");
                form.reset();
                $("#image-previews").empty();
                uploadFormData = new FormData(); // Reset FormData
            },
            error: function(xhr, status, error) {
                console.error("Upload failed: " + error);
                alert("Upload failed: " + error);
            }
        });
    });

    function validateForm() {
        // Validate product name
        const productName = document.getElementById('product_name').value;
        if (productName.trim() === "") {
            alert("Product Name is required.");
            return false;
        }

        // Validate product description
        const productDescription = document.getElementById('product_description').value;
        if (productDescription.trim() === "") {
            alert("Product Description is required.");
            return false;
        }

        // Validate product stock (must be a number)
        const productStock = document.getElementById('product_stock').value;
        if (!/^\d+$/.test(productStock)) {
            alert("Product Stock must be a valid number.");
            return false;
        }

        // Validate product price (must be a valid number)
        const productPrice = document.getElementById('product_price').value;
        if (!/^\d+(\.\d{1,2})?$/.test(productPrice)) {
            alert("Product Price must be a valid price (e.g., 10 or 10.99).");
            return false;
        }

        // Validate at least one subcategory is selected
        const subcategoryCheckboxes = document.querySelectorAll("input[name='subcategory[]']");
        let subcategorySelected = false;
        for (let checkbox of subcategoryCheckboxes) {
            if (checkbox.checked) {
                subcategorySelected = true;
                break;
            }
        }
        if (!subcategorySelected) {
            alert("At least one subcategory must be selected.");
            return false;
        }

        return true;
    }
</script>

<?php
include '../../_foot.php';
?>
