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

// Get the search query, sort column, and sort direction if they're set
$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';
$sortColumn = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'product_name';
$sortDirection = isset($_GET['sort_direction']) ? $_GET['sort_direction'] : 'ASC';

// Validate sort column and direction
$validSortColumns = ['product_name', 'product_price', 'product_stock'];
if (!in_array($sortColumn, $validSortColumns)) {
    $sortColumn = 'product_name';
}
$sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

// Build SQL query based on search and sorting
$sql = "SELECT product_id, product_name, product_image, product_description, product_stock, product_price, dateCreated FROM Product WHERE 1=1";

if (!empty($searchQuery)) {
    $searchQuery = $conn->real_escape_string($searchQuery);
    $sql .= " AND product_name LIKE '%$searchQuery%'";
}

$sql .= " ORDER BY $sortColumn $sortDirection";

// Execute the query
$result = $conn->query($sql);

// Output the product rows dynamically
if ($result->num_rows > 0) {
    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $counter++ . "</td>";
        echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";

        // Display multiple images
        $images = explode(',', $row['product_image']);
        echo "<td>";
        foreach ($images as $image) {
            echo "<img src='../backend/uploads/productImage/" . htmlspecialchars($image) . "' width='50' height='50' style='margin-right:10px;'>";
        }
        echo "</td>";

        echo "<td class='text-skip'>" . htmlspecialchars($row['product_description']) . "</td>";
        echo "<td>" . htmlspecialchars($row['product_stock']) . "</td>";
        echo "<td>RM" . htmlspecialchars($row['product_price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['dateCreated']) . "</td>";

        echo "<td>
                <div class='dropdown'>
                    <button class='dropdown-btn button-normal'>Action</button>
                    <div class='dropdown-content'>
                        <a href='/page/backend/updateProduct.php?product_id=" . htmlspecialchars($row['product_id']) . "'>Update Product</a>
                        <a href='/page/backend/deleteProduct.php?product_id=" . htmlspecialchars($row['product_id']) . "'>Delete Product</a>
                    </div>
                </div>
            </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='9'>No products found</td></tr>";
}

$conn->close();
?>
