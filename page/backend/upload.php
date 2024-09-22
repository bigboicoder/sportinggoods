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
    // Process file uploads
    $filePaths = [];
    if (!empty($_FILES['files']['name'][0])) {
        $files = $_FILES['files'];

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] == UPLOAD_ERR_OK) {
                $tmpName = $files['tmp_name'][$i];
                $fileName = generateUniqueFilename($files['name'][$i]);
                $targetFilePath = $uploadDir . $fileName;

                if (move_uploaded_file($tmpName, $targetFilePath)) {
                    // Store the unique file name for database insertion
                    $filePaths[] = $fileName;
                } else {
                    echo "Failed to move uploaded file: " . htmlspecialchars($files['name'][$i]);
                    exit;
                }
            } else {
                echo "Error uploading file: " . $files['error'][$i];
                exit;
            }
        }
    }

    // Use default image if no files were uploaded
    $product_image = !empty($filePaths) ? implode(',', $filePaths) : $defaultImage;

    // Get other form data
    $product_name = $_POST['product_name'] ?? '';
    $product_description = $_POST['product_description'] ?? '';
    $product_stock = $_POST['product_stock'] ?? 0;
    $product_price = $_POST['product_price'] ?? 0.0;

    // Prepare SQL query for inserting product
    $stmt = $conn->prepare("INSERT INTO Product (product_name, product_image, product_description, product_stock, product_price) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssii", $product_name, $product_image, $product_description, $product_stock, $product_price);

    if ($stmt->execute()) {
        $productId = $stmt->insert_id; // Get the inserted product's ID

        // Handle subcategories
        if (isset($_POST['subcategory']) && is_array($_POST['subcategory'])) {
            foreach ($_POST['subcategory'] as $subCategoryId) {
                $subCategorySql = "INSERT INTO ProductCategory (product_id, subCategory_id) VALUES (?, ?)";
                $subCategoryStmt = $conn->prepare($subCategorySql);
                $subCategoryStmt->bind_param("ii", $productId, $subCategoryId);
                $subCategoryStmt->execute();
                $subCategoryStmt->close();
            }
        }

        echo "Product added successfully.";
    } else {
        echo "Error adding product: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
