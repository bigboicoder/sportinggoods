<?php
require '../../_base.php';
// Admin role
auth('Admin');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch subcategories and their related categories
$sql = "SELECT s.subCategory_id, s.subcategory_name, s.category_id, c.category_name
        FROM SubCategory s
        JOIN Category c ON s.category_id = c.category_id
        ORDER BY s.subCategory_id ASC";

$result = $conn->query($sql);

// Page title
$_title = 'Subcategory';
include '../../_head.php';

// Display messages if any
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : 'success';  // Defaults to success
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">
    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <span>Subcategory</span>
    </div>
    <div class="page-header">
        <h1>Subcategory</h1>
        <div class="button-add">
            <a href="/page/backend/addSubcategory.php" class="button-normal">Add Subcategory</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div id="message" class="message <?php echo $status; ?>">
            <?php echo $message; ?>
        </div>
        <script>
            setTimeout(function() {
                var msg = document.getElementById('message');
                msg.style.opacity = '0';
                setTimeout(function() {
                    msg.remove();  // Removes the message element entirely after fading out
                }, 500);  // Allow for the opacity transition to finish
            }, 3000);  // Message will fade out after 3000 ms
        </script>
    <?php endif; ?>

    <form>
        <div class="filter-container">
            <div class="filters">
                <label for="filter1">Filters :</label>
                <input type="text" id="filter1" name="filter1" placeholder="Subcategory ID">
                <input type="text" id="filter2" name="filter2" placeholder="Subcategory Name">
                <input type="text" id="filter3" name="filter3" placeholder="Category ID">
                <input type="text" id="filter4" name="filter4" placeholder="Category Name">
                <input type="date" id="filter5" name="filter5">
            </div>
            <div class="filter-button">
                <div>
                    <input type="button" class="button-blue" value="Submit">
                </div>
                <div>
                    <input type="reset" value="Reset">
                </div>
            </div>
        </div>
    </form>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th><a href="#">No</a></th>
                    <th><a href="#">Subcategory ID</a></th>
                    <th><a href="#">Subcategory Name</a></th>
                    <th><a href="#">Category ID</a></th>
                    <th><a href="#">Category Name</a></th>
                    <th><a href="#">Action</a></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$no}</td>";
                        echo "<td>{$row['subCategory_id']}</td>";
                        echo "<td>{$row['subcategory_name']}</td>";
                        echo "<td><a class='table-data-link' href='/page/backend/viewCategory.php?id={$row['category_id']}'>{$row['category_id']}</a></td>";
                        echo "<td>{$row['category_name']}</td>";
                        echo "<td>
                            <div class='dropdown'>
                                <button class='dropdown-btn button-normal'>Action</button>
                                <div class='dropdown-content'>
                                    <a href='/page/backend/updateSubcategory.php?id={$row['subCategory_id']}'>Update Subcategory</a>
                                    <a href='/page/backend/deleteSubcategory.php?id={$row['subCategory_id']}'>Delete Subcategory</a>
                                </div>
                            </div>
                        </td>";
                        echo "</tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='6'>No subcategories found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>

<?php
$conn->close();
include '../../_foot.php';
?>
