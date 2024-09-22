<?php
require '../../_base.php';

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

// Check if a delete request has been made
if (isset($_GET['product_id'])) {
    $productId = intval($_GET['product_id']);

    // Start a transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Delete the product-category relationships first
        $deleteProductCategorySql = "DELETE FROM ProductCategory WHERE product_id = ?";
        $productCategoryStmt = $conn->prepare($deleteProductCategorySql);
        $productCategoryStmt->bind_param("i", $productId);
        $productCategoryStmt->execute();
        $productCategoryStmt->close();

        // Then delete the product from the Product table
        $deleteProductSql = "DELETE FROM Product WHERE product_id = ?";
        $productStmt = $conn->prepare($deleteProductSql);
        $productStmt->bind_param("i", $productId);

        if ($productStmt->execute()) {
            // Commit the transaction after both deletes succeed
            $conn->commit();
            // Redirect to products page with success message
            header("Location: /page/backend/products.php?message=Product deleted successfully.");
            exit();
        } else {
            // Rollback transaction in case of an error
            $conn->rollback();
            // Redirect to products page with error message
            header("Location: /page/backend/products.php?message=Error deleting product.");
            exit();
        }

        $productStmt->close();
    } catch (Exception $e) {
        // In case of error, rollback the transaction
        $conn->rollback();
        header("Location: /page/backend/products.php?message=Error deleting product or product category.");
        exit();
    }
} else {
    // Redirect to products page if no product_id is set
    header("Location: /page/backend/products.php?message=No product ID specified.");
    exit();
}

$conn->close();
?>
