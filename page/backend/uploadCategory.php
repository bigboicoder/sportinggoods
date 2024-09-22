<?php
// Include your base file if needed
require '../../_base.php';

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";  // Variable to store feedback messages

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the category name from the form input
    $category_name = trim($_POST['category_name']);
    
    // Validate form input
    if (empty($category_name)) {
        // If the category name is empty, display an error message
        $message = "Category name is required.";
    } else {
        // Prepare an SQL query to insert the new category
        $stmt = $conn->prepare("INSERT INTO Category (category_name) VALUES (?)");
        $stmt->bind_param("s", $category_name);

        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            // If the insert is successful, set a success message
            $message = "Category added successfully.";
        } else {
            // If there was an error executing the query, set an error message
            $message = "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();

    // Send the message back to the same page
    header("Location: addCategory.php?message=" . urlencode($message));
    exit();
}
?>
