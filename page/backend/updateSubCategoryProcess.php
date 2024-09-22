<?php
require '../../_base.php';
auth('Admin');  // Ensure only admins can perform this action

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subCategory_id'])) {
    $subCategoryId = intval($_POST['subCategory_id']);
    $categoryId = intval($_POST['category_id']);
    $subcategoryName = htmlspecialchars(trim($_POST['subcategory_name']));

    // Validate required fields
    if (empty($categoryId) || empty($subcategoryName)) {
        header("Location: /page/backend/updateSubcategory.php?id=$subCategoryId&message=All fields are required.&status=error");
        exit();
    }

    // Update the subcategory in the database
    $updateSql = "UPDATE SubCategory SET subcategory_name = ?, category_id = ? WHERE subCategory_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sii", $subcategoryName, $categoryId, $subCategoryId);

    if ($stmt->execute()) {
        // Redirect to subcategories page with success message
        header("Location: /page/backend/subcategory.php?message=Subcategory updated successfully.&status=success");
    } else {
        // Redirect to the update page with error message
        header("Location: /page/backend/updateSubcategory.php?id=$subCategoryId&message=Error updating subcategory: " . urlencode($stmt->error) . "&status=error");
    }
    $stmt->close();
} else {
    // Redirect if no form data is submitted
    header("Location: /page/backend/subcategory.php?message=Invalid request.&status=error");
}

$conn->close();
