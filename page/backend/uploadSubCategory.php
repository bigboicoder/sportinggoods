<?php
require '../../_base.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";  // Variable to store feedback messages

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form inputs
    $category_id = intval($_POST['category_id']);
    $subcategory_name = trim($_POST['subcategory_name']);
    
    // Validate the inputs
    if (empty($category_id) || empty($subcategory_name)) {
        // If category or subcategory name is empty, display an error message
        $message = "All fields are required.";
    } else {
        // Prepare an SQL query to insert the new subcategory
        $stmt = $conn->prepare("INSERT INTO SubCategory (category_id, subcategory_name) VALUES (?, ?)");
        $stmt->bind_param("is", $category_id, $subcategory_name);

        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            // If the insert is successful, set a success message
            $message = "Subcategory added successfully.";
        } else {
            // If there was an error executing the query, set an error message
            $message = "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();

    // Redirect back to the form with the message
    header("Location: addSubCategory.php?message=" . urlencode($message));
    exit();
}
?>
