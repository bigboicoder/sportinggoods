<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Directory where images will be saved
$uploadDir = '../backend/uploads/productImage/';
$defaultImage = 'defaultProduct.png'; // Default image filename

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Function to generate a unique filename
function generateUniqueFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $uniqueName = uniqid() . '.' . $extension;
    return $uniqueName;
}

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'] ?? 0;

    // Fetch existing images
    $stmt = $conn->prepare("SELECT product_image FROM Product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_images = $result->fetch_assoc()['product_image'] ?? $defaultImage; // Default to defaultProduct.png if no image
    $stmt->close();

    // Convert existing images to an array if there are any
    $existing_images_array = !empty($existing_images) && $existing_images !== $defaultImage 
        ? explode(',', $existing_images) 
        : [];

    // Handle deletion of images
    if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $imageToDelete) {
            if (($key = array_search($imageToDelete, $existing_images_array)) !== false) {
                // Remove the image from the array
                unset($existing_images_array[$key]);

                // Delete the image file from the server
                if (file_exists($uploadDir . $imageToDelete) && $imageToDelete !== $defaultImage) {
                    unlink($uploadDir . $imageToDelete);
                }
            }
        }
    }

    // Process file uploads
$filePaths = [];
if (isset($_FILES['product_image']['name'][0]) && $_FILES['product_image']['name'][0] != '') {
    $files = $_FILES['product_image'];

    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] == UPLOAD_ERR_OK) {
            $tmpName = $files['tmp_name'][$i];
            $fileName = generateUniqueFilename($files['name'][$i]);
            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $targetFilePath)) {
                $filePaths[] = $fileName;
            } else {
                echo "Failed to move uploaded file: " . htmlspecialchars($files['name'][$i]) . "\n";
            }
        } else {
            echo "Error uploading file: " . $files['error'][$i] . " for file " . htmlspecialchars($files['name'][$i]) . "\n";
        }
    }
} else {
    echo "No files uploaded or file upload errors present.\n";
}


    // Merge existing images with newly uploaded images
    $merged_images = array_merge($existing_images_array, $filePaths);

    // If there are no images left after deletion or no new uploads, use the default image
    if (empty($merged_images)) {
        $product_image = $defaultImage;  // Assign defaultProduct.png
    } else {
        $product_image = implode(',', $merged_images);
    }

    // Get other form data
    $product_name = $_POST['product_name'] ?? '';
    $product_description = $_POST['product_description'] ?? '';
    $product_stock = $_POST['product_stock'] ?? 0;
    $product_price = $_POST['product_price'] ?? 0.0;

    // Prepare SQL query for updating product
    $stmt = $conn->prepare("UPDATE Product SET product_name = ?, product_image = ?, product_description = ?, product_stock = ?, product_price = ? WHERE product_id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssidi", $product_name, $product_image, $product_description, $product_stock, $product_price, $product_id);
    // Additional logic for handling subcategory update
    if (isset($_POST['subcategory']) && is_array($_POST['subcategory'])) {
        // Delete existing subcategories for the product
        $deleteSubCategorySql = "DELETE FROM ProductCategory WHERE product_id = ?";
        $deleteStmt = $conn->prepare($deleteSubCategorySql);
        $deleteStmt->bind_param("i", $product_id);
        $deleteStmt->execute();
        $deleteStmt->close();

        // Insert new subcategory selections
        foreach ($_POST['subcategory'] as $subCategoryId) {
            $insertSubCategorySql = "INSERT INTO ProductCategory (product_id, subCategory_id) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertSubCategorySql);
            $insertStmt->bind_param("ii", $product_id, $subCategoryId);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }

    // Execute query
    if ($stmt->execute()) {
        echo "Product updated successfully";
    } else {
        echo "Error executing query: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
