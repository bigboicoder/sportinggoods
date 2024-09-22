<?php
// Replace with your actual connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image = $_POST['image'];
    $product_id = intval($_POST['product_id']);
    $defaultImage = 'defaultProduct.png'; // Default image filename

    // Validate input
    if (!empty($image) && $product_id > 0) {
        // Define the upload directory
        $upload_dir = '../backend/uploads/productImage/';
        $file_path = $upload_dir . $image;

        // Attempt to delete the file from the server
        if (file_exists($file_path) && $image !== $defaultImage) {
            // Ensure we don't delete the default image
            if (unlink($file_path)) {
                // Update the database to remove the image reference
                $sql = "SELECT product_image FROM Product WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $stmt->bind_result($product_images);
                $stmt->fetch();
                $stmt->close();

                $images_array = explode(',', $product_images);
                $images_array = array_filter($images_array, function($img) use ($image) {
                    return $img !== $image;
                });

                // If no images are left after deletion, revert to the default image
                if (empty($images_array)) {
                    $new_images_string = $defaultImage;
                } else {
                    $new_images_string = implode(',', $images_array);
                }

                // Update the product's image field in the database
                $sql = "UPDATE Product SET product_image = ? WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('si', $new_images_string, $product_id);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Image deleted successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update database']);
                }
                $stmt->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete image']);
            }
        } else if ($image === $defaultImage) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete default image']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Image not found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
}

$conn->close();
