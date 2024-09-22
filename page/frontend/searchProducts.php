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

// Get search query, subcategoryId, and categoryId from GET parameters
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';
$subcategoryId = isset($_GET['subcategoryId']) ? (int)$_GET['subcategoryId'] : 0;
$categoryId = isset($_GET['categoryId']) ? (int)$_GET['categoryId'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;  // Display 9 products per page

// Fetch products based on search query, subcategory, or category
function getProducts($conn, $subcategoryId, $categoryId, $searchQuery, $page, $limit) {
    $offset = ($page - 1) * $limit;
    $searchQuery = $conn->real_escape_string($searchQuery); // Sanitize search query

    // If a subcategory is selected and a search query is provided, search within that subcategory
    if ($subcategoryId && !empty($searchQuery)) {
        $query = "SELECT Product.* 
                  FROM Product 
                  JOIN ProductCategory ON Product.product_id = ProductCategory.product_id 
                  WHERE ProductCategory.subCategory_id = $subcategoryId
                  AND (Product.product_name LIKE '%$searchQuery%' 
                  OR Product.product_description LIKE '%$searchQuery%') 
                  LIMIT $limit OFFSET $offset";
    } 
    // If only a subcategory is selected, show products from that subcategory
    elseif ($subcategoryId) {
        $query = "SELECT Product.* 
                  FROM Product 
                  JOIN ProductCategory ON Product.product_id = ProductCategory.product_id 
                  WHERE ProductCategory.subCategory_id = $subcategoryId 
                  LIMIT $limit OFFSET $offset";
    } 
    // If search query is provided without a subcategory, search globally across all products
    elseif (!empty($searchQuery)) {
        $query = "SELECT Product.* 
                  FROM Product 
                  WHERE Product.product_name LIKE '%$searchQuery%' 
                  OR Product.product_description LIKE '%$searchQuery%' 
                  LIMIT $limit OFFSET $offset";
    } 
    // Fetch all products if no search or subcategory is selected
    else {
        $query = "SELECT * FROM Product LIMIT $limit OFFSET $offset";
    }

    $result = $conn->query($query);
    $products = array();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    return $products;
}

// Fetch the products to display in the product grid
$products = getProducts($conn, $subcategoryId, $categoryId, $searchQuery, $page, $limit);

// Output the products grid dynamically (HTML rendering)
if (!empty($products)) {
    foreach ($products as $product) {
        echo '<div class="product-card">';
        echo '<img src="../backend/uploads/productImage/' . htmlspecialchars($product['product_image']) . '" alt="' . htmlspecialchars($product['product_name']) . '">';
        echo '<h3>' . htmlspecialchars($product['product_name']) . '</h3>';
        echo '<p>' . htmlspecialchars($product['product_description']) . '</p>';
        echo '<p><strong>Price:</strong> $' . htmlspecialchars($product['product_price']) . '</p>';
        echo '</div>';
    }
} else {
    echo '<div class="no-products">No products available.</div>';
}

// Close the database connection
$conn->close();
?>
