<?php
require '../../_base.php';
auth('Admin');  // Ensure only admins can perform this action

// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories and count their subcategories
$sql = "SELECT c.category_id, c.category_name, COUNT(s.subCategory_id) AS total_subcategories
        FROM Category c
        LEFT JOIN SubCategory s ON c.category_id = s.category_id
        GROUP BY c.category_id, c.category_name
        ORDER BY c.category_id ASC";
$result = $conn->query($sql);

// Page title
$_title = 'Categories';
include '../../_head.php';

// Display messages if any
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : 'success';  // Defaults to success
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $_title; ?></title>
</head>
<body>
    <!-- Main Content -->
    <main class="main-content">
        <div class="breadcrumbs">
            <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <span>Categories</span>
        </div>
        <div class="page-header">
            <h1>Categories</h1>
            <div class="button-add">
                <a href="/page/backend/addCategory.php" class="button-normal">Add Category</a>
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
                    <input type="text" id="filter1" name="filter1" placeholder="Category ID">
                    <input type="text" id="filter2" name="filter2" placeholder="Category Name">
                    <input type="text" id="filter3" name="filter3" placeholder="Total Subcategory">
                    <input type="date" id="filter4" name="filter4">
                </div>
                <div class="filter-button">
                    <input type="button" class="button-blue" value="Submit">
                    <input type="reset" value="Reset">
                </div>
            </div>
        </form>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Total Subcategory</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$no}</td>";
                            echo "<td>{$row['category_id']}</td>";
                            echo "<td>{$row['category_name']}</td>";
                            echo "<td>{$row['total_subcategories']}</td>";
                            echo "<td>
                                  <div class='dropdown'>
                                      <button class='dropdown-btn button-normal'>Action</button>
                                      <div class='dropdown-content'>                                          
                                          <a href='/page/backend/updateCategory.php?id={$row['category_id']}'>Update Category</a>
                                          <a href='/page/backend/deleteCategory.php?id={$row['category_id']}'>Delete Category</a>
                                      </div>
                                  </div>
                              </td>";
                            echo "</tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='5'>No categories found</td></tr>";
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

</body>
</html>
