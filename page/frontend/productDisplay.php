<?php
include '../../_head2.php'; // Assuming this includes navigation

// Database connection
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

// Fetch categories
function getCategories($conn) {
    $query = "SELECT * FROM Category";
    $result = $conn->query($query);
    $categories = array();

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }

    return $categories;
}

// Fetch subcategories based on category ID
function getSubcategories($conn, $categoryId) {
    $query = "SELECT SubCategory.subCategory_id, SubCategory.category_id, SubCategory.subCategory_name
              FROM SubCategory 
              WHERE SubCategory.category_id = $categoryId";
    $result = $conn->query($query);
    $subcategories = array();

    while ($row = $result->fetch_assoc()) {
        $subcategories[] = $row;
    }

    return $subcategories;
}

// Fetch all products or filtered products based on search, subcategory, or category
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

// Get total number of products for pagination
function getTotalProducts($conn, $subcategoryId, $categoryId, $searchQuery) {
    $searchQuery = $conn->real_escape_string($searchQuery); // Sanitize search query

    if (!empty($searchQuery)) {
        // Count products by search query
        $query = "SELECT COUNT(*) as total 
                  FROM Product 
                  WHERE Product.product_name LIKE '%$searchQuery%' 
                  OR Product.product_description LIKE '%$searchQuery%'";
    } elseif ($subcategoryId) {
        // Count products by subcategory
        $query = "SELECT COUNT(*) as total 
                  FROM Product 
                  JOIN ProductCategory ON Product.product_id = ProductCategory.product_id 
                  WHERE ProductCategory.subCategory_id = $subcategoryId";
    } elseif ($categoryId) {
        // Count products by category
        $query = "SELECT COUNT(*) as total 
                  FROM Product 
                  JOIN ProductCategory ON Product.product_id = ProductCategory.product_id 
                  JOIN SubCategory ON ProductCategory.subCategory_id = SubCategory.subCategory_id 
                  WHERE SubCategory.category_id = $categoryId";
    } else {
        // Count all products if no category or subcategory is selected
        $query = "SELECT COUNT(*) as total FROM Product";
    }

    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Get search query from URL (if present)
$searchQuery = isset($_GET['q']) ? $_GET['q'] : ''; // Defaults to empty string if no query is provided

// Handle pagination and fetching data
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;  // Display 9 products per page
$subcategoryId = isset($_GET['subcategoryId']) ? (int)$_GET['subcategoryId'] : 0;
$categoryId = isset($_GET['categoryId']) ? (int)$_GET['categoryId'] : 0;

// Fetch products based on search query, subcategory, category, or all products
$products = getProducts($conn, $subcategoryId, $categoryId, $searchQuery, $page, $limit);
$categories = getCategories($conn);
$totalProducts = getTotalProducts($conn, $subcategoryId, $categoryId, $searchQuery);
$totalPages = ceil($totalProducts / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            margin: 20px;
            min-height: 600px;
            flex-grow: 1;
        }
        .left-panel {
            width: 25%;
            margin-right: 20px;
            flex-shrink: 0;
        }
        .right-panel {
            width: 75%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            min-height: 500px;
            flex-grow: 1;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            min-height: 300px;
        }
        .no-products {
            grid-column: 1 / -1;
            font-size: 1.5em;
            color: #777;
            padding: 50px 0;
            text-align: center;
        }
        .product-card {
            background-color: #fff; /* Set background to white */
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            border-radius: 8px; /* Rounded corners for a smoother look */
        }

        .product-card:hover {
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2); /* Slightly stronger shadow on hover */
            transform: translateY(-5px); /* Slight lift effect on hover */
        }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px; /* Slight rounding for the image corners */
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .button-28.active {
            background-color: #1A1A1A;
            color: #fff;
            cursor: default;
        }

        .button-28 {
            appearance: none;
            background-color: transparent;
            border: 2px solid #1A1A1A;
            border-radius: 15px;
            box-sizing: border-box;
            color: #3B3B3B;
            cursor: pointer;
            display: inline-block;
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: 600;
            padding: 10px 24px;
            text-align: center;
            text-decoration: none;
            transition: all 300ms cubic-bezier(.23, 1, 0.32, 1);
        }

        .button-28:hover {
            color: #fff;
            background-color: #1A1A1A;
            box-shadow: rgba(0, 0, 0, 0.25) 0 8px 15px;
        }

        .button-28:active {
            box-shadow: none;
            transform: translateY(0);
        }

        /* Categories Accordion Style */
        .category {
            cursor: pointer;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            padding: 10px;
            position: relative;
            font-weight: bold;
        }

        .category::after {
            content: '→';
            position: absolute;
            right: 10px;
            transition: transform 0.2s;
        }

        .category.active::after {
            transform: rotate(90deg); /* Rotate the arrow when active */
        }

        .subcategory {
            display: none;
            padding-left: 20px;
            margin-top: 5px;
        }

        .subcategory button {
            display: block;
            background-color: #e7e7e7;
            border: none;
            margin-bottom: 5px;
            padding: 8px;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-weight: normal;
        }

        .subcategory button:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Left Panel: Categories and Subcategories -->
    <div class="left-panel">
        <h2>Categories</h2>
        <div id="categories">
            <?php foreach ($categories as $category): ?>
                <div class="category" onclick="toggleSubcategories(<?= $category['category_id'] ?>)">
                    <?= htmlspecialchars($category['category_name']) ?>
                </div>
                <div id="subcategory-<?= $category['category_id'] ?>" class="subcategory">
                    <?php
                    $subcategories = getSubcategories($conn, $category['category_id']);
                    foreach ($subcategories as $subcategory):
                    ?>
                        <button role="button" onclick="window.location.href='?subcategoryId=<?= $subcategory['subCategory_id'] ?>&categoryId=<?= $category['category_id'] ?>'">
                            <?= htmlspecialchars($subcategory['subCategory_name']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Panel: Products and Search -->
    <div class="right-panel">
        <h2>Products</h2>
        
        <!-- Search Bar and Reset Button -->
        <div style="display: flex; gap: 10px; align-items: center;">
            <input type="text" id="search" placeholder="Search for products..." onkeyup="searchProducts()" style="width: 100%; padding: 10px;">
            <button class="button-28" onclick="resetFilters()" style="padding: 10px 20px;">Reset</button>
        </div>

        <!-- Products Grid -->
        <div id="products" class="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <?php
                    // Split the product_image field by commas
                    $images = explode(',', $product['product_image']);
                    $firstImage = $images[0]; // Use the first image
                    ?>
                    <div class="product-card" onclick="window.location.href='product-details.php?product_id=<?= $product['product_id'] ?>'">
                        <img src="../backend/uploads/productImage/<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                        <h3><?= htmlspecialchars($product['product_name']) ?></h3>
                        <p><?= htmlspecialchars($product['product_description']) ?></p>
                        <p><strong>Price:</strong> $<?= htmlspecialchars($product['product_price']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">No products available.</div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <button class="button-28" role="button" onclick="window.location.href='?subcategoryId=<?= $subcategoryId ?>&categoryId=<?= $categoryId ?>&page=<?= $page - 1 ?>'">Previous</button>
            <?php endif; ?>

            <?php
            $totalPages = ceil($totalProducts / $limit);
            $maxPagesToShow = 5; // Number of page links to show
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);

            // Adjust if the range is smaller than max pages to show
            if ($endPage - $startPage < $maxPagesToShow - 1) {
                $startPage = max(1, $endPage - $maxPagesToShow + 1);
            }

            if ($startPage > 1): ?>
                <button class="button-28" role="button" onclick="window.location.href='?subcategoryId=<?= $subcategoryId ?>&categoryId=<?= $categoryId ?>&page=1'">1</button>
                <span>...</span> <!-- Ellipsis before the first visible page -->
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <button class="button-28 <?= $i == $page ? 'active' : '' ?>" role="button"
                        onclick="window.location.href='?subcategoryId=<?= $subcategoryId ?>&categoryId=<?= $categoryId ?>&page=<?= $i ?>'">
                    <?= $i ?>
                </button>
            <?php endfor; ?>

            <?php if ($endPage < $totalPages): ?>
                <span>...</span> <!-- Ellipsis after the last visible page -->
                <button class="button-28" role="button" onclick="window.location.href='?subcategoryId=<?= $subcategoryId ?>&categoryId=<?= $categoryId ?>&page=<?= $totalPages ?>'"><?= $totalPages ?></button>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <button class="button-28" role="button" onclick="window.location.href='?subcategoryId=<?= $subcategoryId ?>&categoryId=<?= $categoryId ?>&page=<?= $page + 1 ?>'">Next</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    function searchProducts() {
        const searchQuery = document.getElementById('search').value;

        // Get current category and subcategory from URL (if any)
        const urlParams = new URLSearchParams(window.location.search);
        const subcategoryId = urlParams.get('subcategoryId');
        const categoryId = urlParams.get('categoryId');

        // Prepare the AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "searchProducts.php?q=" + encodeURIComponent(searchQuery) +
            (subcategoryId ? "&subcategoryId=" + subcategoryId : "") +
            (categoryId ? "&categoryId=" + categoryId : ""), true);
        
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Update the product grid with the search results
                document.getElementById('products').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    }

    function resetFilters() {
        // Get the base URL (without query parameters)
        const baseUrl = window.location.href.split('?')[0];

        // Redirect to the base URL to clear filters
        window.location.href = baseUrl;
    }

    function toggleSubcategories(categoryId) {
        const categoryDiv = document.querySelector(`[onclick="toggleSubcategories(${categoryId})"]`);
        const subcategoryDiv = document.getElementById('subcategory-' + categoryId);

        // Toggle the 'active' class to rotate the arrow
        categoryDiv.classList.toggle('active');

        // Toggle the display of the subcategory div
        if (subcategoryDiv.style.display === 'none' || subcategoryDiv.style.display === '') {
            subcategoryDiv.style.display = 'block';
        } else {
            subcategoryDiv.style.display = 'none';
        }
    }
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
