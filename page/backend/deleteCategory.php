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
    $categoryId = intval($_GET['id']);

    // Check for dependencies before deletion
    $checkSql = "SELECT COUNT(*) AS subCount FROM SubCategory WHERE category_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $categoryId);
    $checkStmt->execute();
    $checkStmt->bind_result($subCount);
    $checkStmt->fetch();
    $checkStmt->close();

    // Only proceed if there are no subcategories linked to this category
    if ($subCount > 0) {
        header("Location: /page/backend/categories.php?message=Cannot delete category with subcategories.&status=error");
        exit();
    }

    // Delete the category from the database
    $deleteSql = "DELETE FROM Category WHERE category_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $categoryId);

    if ($stmt->execute()) {
        // Redirect to categories page with success message
        header("Location: /page/backend/categories.php?message=Category deleted successfully.&status=success");
    } else {
        // Redirect to categories page with error message
        header("Location: /page/backend/categories.php?message=Error deleting category: " . urlencode($stmt->error) . "&status=error");
    }
    $stmt->close();
} else {
    // Redirect to categories page if no category_id is set
    header("Location: /page/backend/categories.php?message=No category ID specified.&status=error");
}

$conn->close();
