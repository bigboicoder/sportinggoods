<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

// (1) Sorting
$fields = [
    'no' => 'No',
    'user_id' => 'Users ID',
    'user_name' => 'Full Name',
    'user_username' => 'Username',
    'user_photo' => 'Photo',
    'user_email' => 'Email',
    'user_contact' => 'Contact Number',
    'user_status' => 'Status',
    'dateCreated' => 'Date Created',
    'action' => 'Action',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'user_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

$noLinkColumns = ['no', 'user_photo', 'action'];

// (2) Searching
$id = req('id');
$name = req('name');
$username = req('username');
$email = req('email');
$contact = req('contact');
$status = req('status');
$fromDate = req('fromDate');
$toDate = req('toDate');

$query = "SELECT * FROM Users WHERE user_role = 'User' AND is_delete = 0";
$params = [];

if ($id) {
    $query .= " AND user_id = ?";
    $params[] = $id;
}
if ($name) {
    $query .= " AND user_name LIKE ?";
    $params[] = "%$name%";
}
if ($username) {
    $query .= " AND user_username LIKE ?";
    $params[] = "%$username%";
}
if ($email) {
    $query .= " AND user_email LIKE ?";
    $params[] = "%$email%";
}
if ($contact) {
    $query .= " AND user_contact LIKE ?";
    $params[] = "%$contact%";
}
if ($status !== '') {
    $query .= " AND user_status = ?";
    $params[] = $status;
}
if (!empty($fromDate)) {
    $query .= " AND dateCreated >= ?";
    $params[] = $fromDate;
}
if (!empty($toDate)) {
    $query .= " AND dateCreated <= ?";
    $params[] = $toDate;
}

$query .= " ORDER BY $sort $dir";

// (3) Paging
$page = req('page', 1);
require_once '../../lib/SimplePager.php';
$p = new SimplePager($query, $params, 2, $page);
$listAdmin = $p->result;

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10; // Number of items per page
$startNumber = ($currentPage - 1) * $itemsPerPage + 1;

// ----------------------------------------------------------------------------
$_title = 'Customers';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Flash Message -->
<?php if ($msg = temp('info')): ?>
    <div id="info">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Main Content -->
<main class="main-content">
    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <span>Customers</span>
    </div>
    <div class="page-header">
        <h1>Customers</h1>
        <div class="button-add">
            <a href="/page/backend/addCustomer.php" class="button-normal">Add Customers</a>
        </div>
    </div>

    <form>
        <div class="filter-container">
            <div class="filters">
                <label for="filter1">Filters :</label>
                <?= html_text('id', 'class="filter1" placeholder="Users ID"') ?>
                <?= html_text('name', 'class="filter1" placeholder="Full Name"') ?>
                <?= html_text('username', 'class="filter1" placeholder="Username"') ?>
                <?= html_text('email', 'class="filter1" placeholder="Email"') ?>
                <?= html_text('contact', 'class="filter1" placeholder="Contact Number"') ?>
                <?= html_select('status', ['1' => 'Activate', '0' => 'Block']); ?>
                <!-- Date Filter Section -->
                <div class="date-container">
                    <label>From :</label>
                    <?= html_date('fromDate', 'class="filter1 date-input"'); ?>
                    <label>to</label>
                    <?= html_date('toDate', 'class="filter1 date-input"'); ?>
                </div>
            </div>
            <div class="filter-button">
                <div>
                    <input type="submit" class="button-blue" value="Submit">
                </div>
                <div>
                    <a href="customers.php" class="button-grey">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <?= table_headers($fields, $sort, $dir, '', $noLinkColumns) ?>
                </tr>
            </thead>
            <tbody>
                <?php $no = $startNumber; ?>
                <?php foreach ($listAdmin as $admin): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $admin->user_id; ?></td>
                        <td><?= $admin->user_name; ?></td>
                        <td><?= $admin->user_username; ?></td>
                        <td><img style="width: 50px;height:50px" src="/uploads/profile/<?= $admin->user_photo ?>"></td>
                        <td><?= $admin->user_email; ?></td>
                        <td><?= $admin->user_contact; ?></td>
                        <td> <?php
                                $badge = getStatusBadge($admin->user_status);
                                ?>
                            <span class="badge <?= $badge['class']; ?>">
                                <?= $badge['text']; ?>
                            </span>
                        </td>
                        <td><?= convertDateFormat($admin->dateCreated); ?></td>
                        <td>
                            <div class="dropdown">
                                <button class="dropdown-btn button-normal">Action</button>
                                <div class="dropdown-content">
                                    <button data-get="/page/backend/viewCustomer.php?id=<?= $admin->user_id ?>">View Customers</button>
                                    <button data-get="/page/backend/updateCustomer.php?id=<?= $admin->user_id ?>">Update Customers</button>
                                    <form method="POST" action="/page/backend/deleteUser.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $admin->user_id ?>">
                                        <button type="submit" name="action" value="delete">Delete Customers</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?= $p->html("sort=$sort&dir=$dir") ?>
</main>

<?php
include '../../_foot.php';
