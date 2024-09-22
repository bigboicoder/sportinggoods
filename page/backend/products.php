<?php
require '../../_base.php';
include '../../_head.php';

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

$_title = 'Products';

// Display messages if any
if (isset($_GET['message'])) {
    echo "<p>" . htmlspecialchars($_GET['message']) . "</p>";
}

// Handle filter values from GET request
$filterName = isset($_GET['filter1']) ? $_GET['filter1'] : '';
$filterStock = isset($_GET['filter2']) ? $_GET['filter2'] : '';
$filterPrice = isset($_GET['filter3']) ? $_GET['filter3'] : '';
$filterDate = isset($_GET['filter4']) ? $_GET['filter4'] : '';

// Handle sorting parameters
$sortColumn = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'product_name';
$sortDirection = isset($_GET['sort_direction']) ? $_GET['sort_direction'] : 'ASC';

// Validate sort column and direction
$validSortColumns = ['product_name', 'product_price', 'product_stock'];
if (!in_array($sortColumn, $validSortColumns)) {
    $sortColumn = 'product_name';
}
$sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

// Build the SQL query with filters
$sql = "SELECT product_id, product_name, product_image, product_description, product_stock, product_price, dateCreated FROM Product WHERE 1=1";

// Append filters if set
if (!empty($filterName)) {
    $sql .= " AND product_name LIKE '%" . $conn->real_escape_string($filterName) . "%'";
}
if (!empty($filterStock)) {
    $sql .= " AND product_stock = '" . $conn->real_escape_string($filterStock) . "'";
}
if (!empty($filterPrice)) {
    $sql .= " AND product_price = '" . $conn->real_escape_string($filterPrice) . "'";
}
if (!empty($filterDate)) {
    $sql .= " AND dateCreated = '" . $conn->real_escape_string($filterDate) . "'";
}

// Add sorting to the query
$sql .= " ORDER BY " . $sortColumn . " " . $sortDirection;

$result = $conn->query($sql);

?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">
    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <span>Products</span>
    </div>
    <div class="page-header">
        <h1>Products</h1>
        <div class="button-add">
            <a href="/page/backend/addProduct.php" class="button-normal">Add Product</a>
        </div>
    </div>


    <!-- Sorting Buttons -->
    <div class="filter-container">
        <div class="filters">
            <label for="sort">Sort by:</label>
            <button type="button" class="button-sort" onclick="sortProducts('product_name', 'ASC')">Name (A-Z)</button>
            <button type="button" class="button-sort" onclick="sortProducts('product_name', 'DESC')">Name (Z-A)</button>
            <button type="button" class="button-sort" onclick="sortProducts('product_price', 'ASC')">Price (Low to High)</button>
            <button type="button" class="button-sort" onclick="sortProducts('product_price', 'DESC')">Price (High to Low)</button>
            <button type="button" class="button-sort" onclick="sortProducts('product_stock', 'ASC')">Stock (Low to High)</button>
            <button type="button" class="button-sort" onclick="sortProducts('product_stock', 'DESC')">Stock (High to Low)</button>
        </div>
    </div>

    <!-- Search bar styled like the filters -->
    <form>
        <div class="filter-container">
            <div class="filters">
                <label for="search-bar">Search :</label>
                <input type="text" id="search-bar" name="search" placeholder="Search for products..." onkeyup="searchProduct()">
            </div>
        </div>
    </form>

    <!-- Table to display products -->
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Product Image</th>
                    <th>Description</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Date Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="product-list">
                <?php
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
                                    <button class='dropdown-btn button-normal'>Action ▼</button>
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
                ?>
            </tbody>
        </table>
    </div>
</main>

<?php
include '../../_foot.php';
?>

<!-- AJAX and Search Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Fetch products on page load
    $(document).ready(function() {
        fetchProducts();
    });

    // Real-time search function
    function searchProduct() {
        const query = document.getElementById('search-bar').value;
        fetchProducts(query);
    }

    // Sorting function
    function sortProducts(sortBy, sortOrder) {
        fetchProducts('', sortBy, sortOrder);
    }

    // Function to fetch products using AJAX
    function fetchProducts(query = '', sortBy = 'product_name', sortOrder = 'ASC') {
        $.ajax({
            url: "/page/backend/fetchProducts.php", // This file will handle the search and sorting
            method: "GET",
            data: { query: query, sort_column: sortBy, sort_direction: sortOrder },
            success: function(data) {
                $('#product-list').html(data);
                bindDropdowns(); // Re-bind dropdown events after fetching new content
            }
        });
    }

    // Bind the dropdown toggle functionality
    function bindDropdowns() {
        $('.dropdown-btn').off('click').on('click', function(e) {
            e.preventDefault();
            $(this).next('.dropdown-content').toggle();
        });

        // Close the dropdown if clicked outside
        $(document).click(function(event) {
            if (!$(event.target).closest('.dropdown-btn').length) {
                $('.dropdown-content').hide();
            }
        });
    }

    // Re-bind dropdowns after page load
    $(document).ready(function() {
        bindDropdowns();
    });
</script>
