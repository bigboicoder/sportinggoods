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

if (isset($_GET['id'])) {
    $subCategoryId = intval($_GET['id']);

    // Check if the subcategory exists
    $checkSql = "SELECT COUNT(*) AS count FROM SubCategory WHERE subCategory_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $subCategoryId);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count == 0) {
        // Redirect if subcategory doesn't exist
        header("Location: /page/backend/subcategory.php?message=Subcategory does not exist.&status=error");
        exit();
    }

    // Delete the subcategory from the database
    $deleteSql = "DELETE FROM SubCategory WHERE subCategory_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $subCategoryId);

    if ($stmt->execute()) {
        // Redirect to subcategories page with success message
        header("Location: /page/backend/subcategory.php?message=Subcategory deleted successfully.&status=success");
    } else {
        // Redirect to subcategories page with error message
        header("Location: /page/backend/subcategory.php?message=Error deleting subcategory: " . urlencode($stmt->error) . "&status=error");
    }
    $stmt->close();
} else {
    // Redirect to subcategories page if no subcategory_id is set
    header("Location: /page/backend/subcategory.php?message=No subcategory ID specified.&status=error");
}

$conn->close();
