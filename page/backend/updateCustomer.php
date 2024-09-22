<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

$id = req('id');
$stm = $_db->prepare('
    SELECT *
    FROM 
        Users
    WHERE 
        user_id = ?
        ');
$stm->execute([$id]);
$admin = $stm->fetch();
$adminStatus = isset($admin->user_status) ? $admin->user_status : '0';

if (is_post()) {
    // Input
    $name       = req('name');
    $username   = req('username');
    $photo      = get_file('photo');
    $email      = req('email');
    $contactNum = req('contact_number');
    $status     = req('status');

    // Validate name: only alphabet and not more than 50 characters
    if ($name == '') {
        $_err['name'] = 'Name is required';
    } else if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $_err['name'] = 'Name should contain only alphabet characters';
    } else if (strlen($name) > 50) {
        $_err['name'] = 'Name must be less than or equal to 50 characters';
    }

    // Validate username: no spaces and more than 7 characters
    if ($username == '') {
        $_err['username'] = 'Username is required';
    } else if (strpos($username, ' ') !== false) {
        $_err['username'] = 'Username cannot contain spaces';
    } else if (strlen($username) < 7) {
        $_err['username'] = 'Username must be more than 7 characters';
    } else if (is_exists($username, "users", "user_username", $admin->user_id, 'user_id')) {
        $_err['username'] = 'Username already exists';
    }

    // Validate: photo (file)
    if ($photo != null) {
        if (!str_starts_with($photo->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        } else if ($photo->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    }

    // Validate email: must be a valid email format
    if ($email == '') {
        $_err['email'] = 'Email is required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email format';
    } else if (is_exists($email, "users", "user_email", $admin->user_id, 'user_id')) {
        $_err['email'] = 'Email already exists';
    }

    // Validate contact number: must start with country code +60 and followed by numbers
    if ($contactNum == '') {
        $_err['contact_number'] = 'Contact number is required';
    } else if (!preg_match("/^[0-9]{9,10}$/", $contactNum)) {
        $_err['contact_number'] = 'Contact number must start with +60 and contain 9-10 digits';
    }

    // Output
    if (!$_err) {
        $fileName = null;

        if ($photo != null) {
            // Process and save the new photo
            $fileName = uniqid() . '.png';
            require_once '../../lib/SimpleImage.php';
            $img = new SimpleImage();
            $img->fromFile($photo->tmp_name)
                ->thumbnail(200, 200)
                ->toFile("../../uploads/profile/$fileName", 'image/png');

            // Delete old photo if it exists and is not the default photo
            if (file_exists("../../uploads/profile/$admin->user_photo")) {
                unlink("../../uploads/profile/$admin->user_photo");
            }

            $stm = $_db->prepare('UPDATE Users
                SET user_photo = ?
                WHERE user_id = ?
            ');
            $stm->execute([$fileName, $admin->user_id]);
        }

        if (!$_err) {
            // Update user record with new photo filename and other details
            $stm = $_db->prepare('UPDATE Users
            SET user_name = ?, user_username = ?, user_email = ?, user_contact = ?, user_status = ?
            WHERE user_id = ?');
            $stm->execute([$name, $username, $email, $contactNum, $status, $admin->user_id]);

            temp('info', 'Record updated');
            redirect('/page/backend/customers.php');
        }
    }
}

// ----------------------------------------------------------------------------
$_title = 'Update Users';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/customers.php">Customers</a> &gt; <span>Update
            Users</span>
    </div>
    <div class="page-header">
        <h2>Update Users</h2>
    </div>
    <form method="post" enctype="multipart/form-data">
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Users Detail</h3>
            </div>
            <div class="row-input-container">
                <label>Full Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_text('name', '', $admin->user_name) ?>
                    <?= err('name') ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Username<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input" style="position:relative;">
                    <?= html_text('username', 'class=input-with-button style=padding-right:158px; id="username"', $admin->user_username) ?>
                    <button type="button" class="action-btn" id="checkUsernameBtn" data-action="checkUsername">
                        <i class="fa fa-check-circle-o" aria-hidden="true"></i> Check Availability
                    </button>
                    <div style="display:inline-block;">
                        <span id="usernameError" class='error-message' style="color:red;"></span>
                        <span id="usernameSuccess" class='success-message' style="color:green;"></span>
                        <?= err('username', 'id="usernameErrorPHP"') ?>
                    </div>
                </div>
            </div>
            <div class="row-input-container">
                <label>Photo<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <!-- Drag-and-drop zone -->
                    <div id="dropZone">
                        <label class="upload" tabindex="0">
                            <!-- Hidden file input -->
                            <input type="file" name="photo" id="profilePicInput" hidden accept="image/*">
                            <!-- Preview image -->
                            <img id="previewImg" src="/uploads/profile/<?= $admin->user_photo ?>">
                            <p id="fileName">Drag and drop file here</p>
                        </label>
                    </div>
                    <?= err('photo') ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Email<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input" style="position:relative;">
                    <?= html_text('email', 'class=input-with-button style=padding-right:158px; id="email"', $admin->user_email) ?>
                    <button type="button" class="action-btn" id="checkEmailBtn" data-action="checkEmail">
                        <i class="fa fa-check-circle-o" aria-hidden="true"></i> Check Availability
                    </button>
                    <div style="display:inline-block;">
                        <span id="emailError" class='error-message' style="color:red;"></span>
                        <span id="emailSuccess" class='success-message' style="color:green;"></span>
                        <?= err('email', 'id="emailErrorPHP"') ?>
                    </div>
                </div>
            </div>
            <div class="row-input-container">
                <label>Contact Number<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_text('contact_number', '', $admin->user_contact) ?>
                    <?= err('contact_number') ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Status<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_select('status', ['1' => 'Activate', '0' => 'Block'], null, $adminStatus); ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Date Created<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_text('date', 'disabled', $admin->dateCreated) ?>
                </div>
            </div>
            <div class="form-submit-button">
                <input type="submit" class="button-blue" value="Update">
            </div>
        </div>
    </form>
</main>
<script>
    document.addEventListener('contextmenu', function(e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault(); // Disable right-click on images
        }
    });
    document.querySelectorAll('.action-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var action = this.getAttribute('data-action');
            var value;
            var inputId;
            var usernameErrorSpan = document.getElementById('usernameErrorPHP');
            var emailErrorSpan = document.getElementById('emailErrorPHP');

            if (action === 'checkEmail') {
                if (emailErrorSpan) { // Check if emailErrorSpan exists
                    emailErrorSpan.remove();
                }
                value = document.getElementById('email').value;

            } else if (action === 'checkUsername') {
                if (usernameErrorSpan) { // Check if usernameErrorSpan exists
                    usernameErrorSpan.remove();
                }
                value = document.getElementById('username').value;
            }

            // Create a GET request to check availability
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'checkAvailability.php?' + action + '=' + encodeURIComponent(value) + '&user_id=' + <?= $id ?>, true);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse the JSON response from PHP
                    var response = JSON.parse(xhr.responseText);
                    // Update the appropriate error message div based on the `status` and `message`
                    if (action === 'checkEmail') {
                        if (response.status === 'success') {
                            document.getElementById('emailError').innerText = ''; // Clear the error if success
                            document.getElementById('emailSuccess').innerText = response.message;
                        } else {
                            document.getElementById('emailError').innerText = response.message;
                            document.getElementById('emailSuccess').innerText = '';
                        }
                    } else if (action === 'checkUsername') {
                        if (response.status === 'success') {
                            document.getElementById('usernameError').innerText = ''; // Clear the error if success
                            document.getElementById('usernameSuccess').innerText = response.message;
                        } else {
                            document.getElementById('usernameError').innerText = response.message;
                            document.getElementById('usernameSuccess').innerText = '';
                        }
                    }
                }
            };


            xhr.send();
        });
    });
</script>
<?php
include '../../_foot.php';
