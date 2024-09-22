<?php
require '../../_base.php';

// Check if the user is logged in
if (!isset($_user)) {
    // Redirect to login page if not logged in
    header('Location: ./login.php');
    exit();
}

// User role authentication
auth('User');

// Initialize error array
$_err = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted values for address, city, state, and postcode
    $address1 = $_POST['address1'] ?? 'N/A';
    $address2 = $_POST['address2'] ?? 'N/A';
    $city = $_POST['city'] ?? 'N/A';
    $state = $_POST['state'] ?? 'N/A';
    $postcode = $_POST['postcode'] ?? 'N/A';

    // Validation for fields
    if (empty($address1)) {
        $_err['address1'] = 'Address 1 is required';
    }
    if (empty($city)) {
        $_err['city'] = 'City is required';
    }
    if (empty($state)) {
        $_err['state'] = 'State is required';
    }
    if (empty($postcode)) {
        $_err['postcode'] = 'Postcode is required';
    } elseif (!ctype_digit($postcode)) {
        $_err['postcode'] = 'Postcode must be digits only';
    }

    // If there are no errors, update the address in the database
    if (!$_err) {
        $stmt = $_db->prepare('
            UPDATE users 
            SET address1 = ?, address2 = ?, city = ?, state = ?, postcode = ?
            WHERE user_id = ?
        ');
        $stmt->execute([$address1, $address2, $city, $state, $postcode, $_user->user_id]);

        // Set a success message and redirect
        temp('info', 'Address updated successfully');
        header('Location: userprofile.php');
        exit();
    }
}

// Set default values for the form, setting 'N/A' if the columns are empty
$address1 = !empty($_user->address1) ? $_user->address1 : 'N/A';
$address2 = !empty($_user->address2) ? $_user->address2 : 'N/A';
$city = !empty($_user->city) ? $_user->city : 'N/A';
$state = !empty($_user->state) ? $_user->state : 'N/A';
$postcode = !empty($_user->postcode) ? $_user->postcode : 'N/A';

// Page title
$_title = 'Update Address';
include '../../_head2.php';
?>

<!-- Display flash message here -->
<?php if ($msg = temp('info')): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="../../home.php">Home</a> &gt; <span>Update Address</span>
    </div>
    <div class="page-header">
        <h2>Update Address</h2>
    </div>
    <div class="detail-container">
        <!-- Side Menu -->
        <div class="profile-container">
            <div class="side-menu">
                <ul>
                    <li><a href="userprofile.php"><i class="fa fa-user"></i> Account Details</a></li>
                    <li><a href="update_address.php" class="active"><i class="fa fa-map-marker"></i> Update Address</a></li>
                    <li><a href="my_order.php"><i class="fa fa-box"></i> My Orders</a></li>
                </ul>
            </div>

            <!-- Profile Form -->
            <div class="profile-form">
                <form method="POST" class="form">
                    <div class="form-group">
                        <label for="address1">Address 1</label>
                        <input class="form-control" type="text" name="address1" value="<?= htmlspecialchars($address1) ?>" required>
                        <?php if (isset($_err['address1'])): ?>
                            <p style="color: red;"><?= htmlspecialchars($_err['address1']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="address2">Address 2</label>
                        <input class="form-control" type="text" name="address2" value="<?= htmlspecialchars($address2) ?>">
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input class="form-control" type="text" name="city" value="<?= htmlspecialchars($city) ?>" required>
                        <?php if (isset($_err['city'])): ?>
                            <p style="color: red;"><?= htmlspecialchars($_err['city']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input class="form-control" type="text" name="state" value="<?= htmlspecialchars($state) ?>" required>
                        <?php if (isset($_err['state'])): ?>
                            <p style="color: red;"><?= htmlspecialchars($_err['state']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="postcode">Postcode</label>
                        <input class="form-control" type="text" name="postcode" value="<?= htmlspecialchars($postcode) ?>" required>
                        <?php if (isset($_err['postcode'])): ?>
                            <p style="color: red;"><?= htmlspecialchars($_err['postcode']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <button class="profile-button-save" type="submit">Update Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php
include '../../_foot.php';
?>