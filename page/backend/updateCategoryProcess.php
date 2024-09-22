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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_id'])) {
    $categoryId = intval($_POST['category_id']);
    $categoryName = htmlspecialchars(trim($_POST['category_name']));

    // Validate required fields
    if (empty($categoryName)) {
        header("Location: /page/backend/updateCategory.php?id=$categoryId&message=Category name is required.&status=error");
        exit();
    }

    // Update the category in the database
    $updateSql = "UPDATE Category SET category_name = ? WHERE category_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $categoryName, $categoryId);

    if ($stmt->execute()) {
        // Redirect to categories page with success message
        header("Location: /page/backend/categories.php?message=Category updated successfully.&status=success");
    } else {
        // Redirect to the update page with error message
        header("Location: /page/backend/updateCategory.php?id=$categoryId&message=Error updating category: " . urlencode($stmt->error) . "&status=error");
    }
    $stmt->close();
} else {
    // Redirect if no form data is submitted
    header("Location: /page/backend/categories.php?message=Invalid request.&status=error");
}

$conn->close();
