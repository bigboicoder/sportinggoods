<?php
require '../../_base.php';

//-----------------------------------------------------------------------------
if (is_post()) {
    // Input
    $name       = req('name');
    $username   = req('username');
    $photo      = get_file('photo');
    $email      = req('email');
    $contactNum = req('contact_number');
    $password   = req('password');
    $status     = req('status');

    $defaultImage = '../../images/default-user-profile.png';

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
    } else if (is_exists($username, "users", "user_username")) {
        $_err['username'] = 'Username already exists';
    }

    // Validate: photo (file)
    if ($photo == null) {
    } else if (!str_starts_with($photo->type, 'image/')) {
        $_err['photo'] = 'Must be image';
    } else if ($photo->size > 1 * 1024 * 1024) {
        $_err['photo'] = 'Maximum 1MB';
    }

    // Validate email: must be a valid email format
    if ($email == '') {
        $_err['email'] = 'Email is required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email format';
    } else if (is_exists($email, "users", "user_email")) {
        $_err['email'] = 'Email already exists';
    }

    // Validate contact number: must start with country code +60 and followed by numbers
    if ($contactNum == '') {
        $_err['contact_number'] = 'Contact number is required';
    } else if (!preg_match("/^[0-9]{9,10}$/", $contactNum)) {
        $_err['contact_number'] = 'Contact number must start with +60 and contain 9-10 digits';
    }

    // Validate password: minimum 8 characters, at least one lowercase letter, one uppercase letter
    if ($password == '') {
        $_err['password'] = 'Password is required';
    } else if (strlen($password) < 8) {
        $_err['password'] = 'Password must be at least 8 characters long';
    } else if (!preg_match("/[a-z]/", $password)) {
        $_err['password'] = 'Password must contain at least one lowercase letter';
    } else if (!preg_match("/[A-Z]/", $password)) {
        $_err['password'] = 'Password must contain at least one uppercase letter';
    }

    if ($status == '') {
        $_err['status'] = 'Status is required';
    }

    // Output
    if (!$_err) {
        if ($photo) {
            $fileName = uniqid() . '.png';
            require_once '../../lib/SimpleImage.php';
            $img = new SimpleImage();
            $img->fromFile($photo->tmp_name)
                ->thumbnail(200, 200)
                ->toFile("../../uploads/profile/$fileName", 'image/png');
        } else {
            $fileName = uniqid() . '.png';
            $defaultPhotoPath = '../../uploads/profile/' . $fileName;
            if (!copy($defaultImage, $defaultPhotoPath)) {
                $_err['photo'] = 'Failed to copy default image';
            }
        }

        $stm = $_db->prepare('INSERT INTO Users
        (user_name, user_username, user_photo,user_email, user_role, user_contact, user_password, user_status)
        VALUES(?, ?, ?, ?, ?, ?, SHA1(?), ?)');
        $stm->execute([$name, $username, $fileName, $email, "Admin", $contactNum, $password, $status]);

        temp('info', 'Record inserted');
        redirect('/page/backend/admin.php');
    }
}

// ----------------------------------------------------------------------------
$_title = 'Add Admin';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <a href="/page/backend/admin.php">Admin</a> &gt; <span>Create
            Admin</span>
    </div>
    <div class="page-header">
        <h2>Create Admin</h2>
    </div>
    <form method="post" enctype="multipart/form-data">
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Admin Detail</h3>
            </div>
            <div class="row-input-container">
                <label>Full Name<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_text('name') ?>
                    <?= err('name') ?>
                </div>
            </div>
            <div class="row-input-container">
                <label>Username<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input" style="position:relative;">
                    <?= html_text('username', 'class=input-with-button style=padding-right:158px; id="username"') ?>
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
                <label class="upload">Photo</label>
                <div class="row-input">
                    <!-- Drag-and-drop zone -->
                    <div id="dropZone">
                        <label class="upload" tabindex="0">
                            <!-- Hidden file input -->

                            <input type="file" name="photo" id="profilePicInput" hidden accept="image/*">

                            <!-- Preview image -->
                            <img id="previewImg" src="/images/default-user-profile.png">
                            <p id="fileName">default-profile.jpg</p>
                        </label>
                    </div>
                    <?= err('photo') ?>
                </div>
            </div>

            <div class="row-input-container">
                <label>Email<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input" style="position:relative;">
                    <?= html_text('email', 'class=input-with-button style=padding-right:158px; id="email"') ?>
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
                    <?= html_text('contact_number') ?>
                    <?= err('contact_number') ?>
                </div>
            </div>

            <div class="row-input-container">
                <label>Password<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <div class="password-container">
                        <?= html_password('password') ?>
                        <i class="fa fa-eye" id="togglePassword"></i>
                    </div>
                    <?= err('password') ?>
                </div>
            </div>
            
            <div class="row-input-container">
                <label>Status<span class="star-icon" style="color:red;"> *</span></label>
                <div class="row-input">
                    <?= html_select('status', ['1' => 'Activate', '0' => 'Block']); ?>
                    <?= err('status') ?>
                </div>
            </div>

            <div class="form-submit-button">
                <input type="submit" class="button-blue" value="Create">
            </div>
        </div>
    </form>
</main>
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle the eye icon
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

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
            xhr.open('GET', 'checkAvailability.php?' + action + '=' + encodeURIComponent(value), true);

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
