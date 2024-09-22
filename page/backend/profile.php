<?php
require '../../_base.php';
//-----------------------------------------------------------------------------

// Check if the user is logged in by checking if $_user is set
if (!isset($_user)) {
    // Redirect to the login page if the user is not logged in
    header('Location: ./login.php');
    exit();
}

//Admin role
auth('Admin');

// ----------------------------------------------------------------------------

// Initialize error array
$_err = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted values for name, username, email, contact, and passwords
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $f = get_file('photo'); // Handle photo file

    // Password fields for change password functionality
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation for fields
    if (empty($name)) {
        $_err['name'] = 'Name is required';
    }

    if (empty($username)) {
        $_err['username'] = 'Username is required';
    }

    if (empty($email)) {
        $_err['email'] = 'Email is required';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = 'Invalid email format';
    }

    if (empty($contact)) {
        $_err['contact'] = 'Contact number is required';
    }

    // Validate photo (if provided)
    if ($f != null) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be an image';
        } else if ($f->size > 1 * 1024 * 1024) {  // Limit to 1MB
            $_err['photo'] = 'Maximum file size is 1MB';
        }
    }

    // Password validation
    if (!empty($current_password)) {
        // Validate current password
        $stmt = $_db->prepare('
            SELECT COUNT(*) FROM users
            WHERE user_password = SHA1(?) AND user_id = ?
        ');
        $stmt->execute([$current_password, $_user->user_id]);

        if ($stmt->fetchColumn() == 0) {
            $_err['current_password'] = 'Current password is incorrect';
        } elseif ($new_password == '') {
            $_err['new_password'] = 'New password is required';
        } elseif (strlen($new_password) < 5 || strlen($new_password) > 100) {
            $_err['new_password'] = 'Password must be between 5 and 100 characters';
        } elseif ($new_password != $confirm_password) {
            $_err['confirm_password'] = 'Passwords do not match';
        }
    }

    // Check if there are no errors before proceeding
    if (!$_err) {
        // Check if email is unique (not taken by other users)
        $stmt = $_db->prepare('SELECT COUNT(*) FROM users WHERE user_email = ? AND user_id != ?');
        $stmt->execute([$email, $_user->user_id]);

        if ($stmt->fetchColumn() > 0) {
            $_err['email'] = 'Email is already taken';
        } else {
            // Handle photo upload if provided
            $photo = $_user->user_photo; // Default to existing photo
            if ($f != null) {
                // Generate a unique name for the new photo
                $photo = uniqid() . '.jpg';
                $target_dir = "../../uploads/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true); // Create directory if not exists
                }

                // Resize and save photo using SimpleImage library
                try {
                    require_once '../../lib/SimpleImage.php';
                    $img = new SimpleImage();
                    $img->fromFile($f->tmp_name)
                        ->thumbnail(200, 200)
                        ->toFile($target_dir . $photo, 'image/jpeg');

                    // Optionally, delete the old photo if it exists
                    if ($_user->user_photo && file_exists($target_dir . $_user->user_photo)) {
                        unlink($target_dir . $_user->user_photo);
                    }
                } catch (Exception $e) {
                    echo 'Error: ' . $e->getMessage();
                }
            }

            // Update the user's profile in the database
            $stmt = $_db->prepare('
                UPDATE users 
                SET user_name = ?, user_username = ?, user_email = ?, user_contact = ?, user_photo = ?
                WHERE user_id = ?
            ');
            $stmt->execute([$name, $username, $email, $contact, $photo, $_user->user_id]);

            // Update session data to reflect changes
            $_user->user_name = $name;
            $_user->user_username = $username;
            $_user->user_email = $email;
            $_user->user_contact = $contact;
            $_user->user_photo = $photo;  // Store only the filename in the session

            // If the current password is provided and validated, update the password
            if (!empty($current_password)) {
                $stmt = $_db->prepare('
                    UPDATE users
                    SET user_password = SHA1(?)
                    WHERE user_id = ?
                ');
                $stmt->execute([$new_password, $_user->user_id]);
            }

            // Set a success message and redirect
            temp('info', 'Profile updated successfully');
        }
    }
}

// ----------------------------------------------------------------------------
$_title = 'Profile';
include '../../_head.php';
?>
<title><?php echo $_title; ?></title>

<!-- Display flash message here -->
<?php if ($msg = temp('info')): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Main Content -->
<main class="main-content">

    <div class="breadcrumbs">
        <a href="/page/backend/dashboard.php">Dashboard</a> &gt; <span>Profile</span>
    </div>
    <div class="page-header">
        <h2>Profile</h2>
    </div>
    <form method="POST" enctype="multipart/form-data" class="form">
        <div class="detail-container">
            <div class="sub-heading">
                <h3>Profile Detail</h3>
            </div>
            <div class="profile-container">
                <div class="profile-row">
                    <div class="profile-col">
                        <div class="profile-card">
                            <div class="profile-card-body">
                                <div class="e-profile">
                                    <div class="profile-row">
                                        <div class="profile-col-auto mb-3">
                                            <div class="profile-image-container">
                                                <div class="profile-image">
                                                    <!-- Display user's current profile photo -->
                                                    <?php if (!empty($_user->user_photo)): ?>
                                                        <img id="profile-image" alt="Profile Image" style="width: 140px; height:140px;" src="/uploads/<?= htmlspecialchars($_user->user_photo) ?>">
                                                    <?php else: ?>
                                                        <img id="profile-image" alt="Profile Image" style="width: 140px; height:140px;" src="/images/default-50x50.jpg">
                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="profile-info">
                                            <div class="profile-info-left">
                                                <h4 class="profile-name"><?= htmlspecialchars($_user->user_name) ?></h4>
                                                <p class="profile-username">@<?= htmlspecialchars($_user->user_username) ?></p>
                                                <div class="profile-photo-container">
                                                    <button class="profile-button" type="button" id="upload-button">
                                                        <i class="fa fa-fw fa-camera"></i>
                                                        <span>Change Photo</span>
                                                    </button>
                                                    <!-- Hidden file input for uploading a photo -->
                                                    <input type="file" name="photo" id="file-input" style="display: none;" accept="image/*">
                                                    <?php if (isset($_err['photo'])): ?>
                                                        <p style="color: red;"><?= htmlspecialchars($_err['photo']) ?></p>
                                                    <?php endif; ?>

                                                </div>
                                            </div>
                                            <div class="profile-info-right">
                                                <span class="badge secondary"><?= htmlspecialchars($_user->user_role) ?></span>
                                                <div class="profile-joined-date">
                                                    <small><?= date('d M Y', strtotime($_user->dateCreated)) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="nav-tabs">
                                        <li class="nav-item"><a href="#" class="active nav-link">Settings</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active">
                                            <form class="form" novalidate="">
                                                <div class="profile-row">
                                                    <div class="profile-col">
                                                        <div class="form-group">
                                                            <label>Full Name</label>
                                                            <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($_user->user_name) ?>">
                                                            <?php if (isset($_err['name'])): ?>
                                                                <p style="color: red;"><?= htmlspecialchars($_err['name']) ?></p>
                                                            <?php endif; ?>

                                                        </div>
                                                        <div class="form-group">
                                                            <label>Username</label>
                                                            <input class="form-control" type="text" name="username" value="<?= htmlspecialchars($_user->user_username) ?>">
                                                            <?php if (isset($_err['username'])): ?>
                                                                <p style="color: red;"><?= htmlspecialchars($_err['username']) ?></p>
                                                            <?php endif; ?>

                                                        </div>
                                                        <div class="form-group">
                                                            <label>Email</label>
                                                            <input class="form-control" type="text" name="email" value="<?= htmlspecialchars($_user->user_email) ?>">
                                                            <?php if (isset($_err['email'])): ?>
                                                                <p style="color: red;"><?= htmlspecialchars($_err['email']) ?></p>
                                                            <?php endif; ?>

                                                        </div>


                                                        <div class="form-group">
                                                            <label>Contact</label>
                                                            <input class="form-control" type="text" name="contact" value="<?= htmlspecialchars($_user->user_contact) ?>">
                                                            <?php if (isset($_err['contact'])): ?>
                                                                <p style="color: red;"><?= htmlspecialchars($_err['contact']) ?></p>
                                                            <?php endif; ?>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="profile-row">
                                                    <div class="profile-col-half">
                                                        <div style="margin-bottom:10px;margin-top:15px;"><b>Change Password</b></div>
                                                        <div class="form-group">
                                                            <label>Current Password</label>
                                                            <input id="current_password" class="form-control" type="password" name="current_password">
                                                            <?php if (isset($_err['current_password'])): ?>
                                                                <p style="color: red;"><?= htmlspecialchars($_err['current_password']) ?></p>
                                                            <?php endif; ?>

                                                        </div>
                                                        <div class="form-group">
                                                            <label>New Password</label>
                                                            <input id="new_password" class="form-control" type="password" name="new_password">
                                                            <?php if (isset($_err['new_password'])): ?>
                                                                <p style="color: red;"><?= htmlspecialchars($_err['new_password']) ?></p>
                                                            <?php endif; ?>

                                                        </div>
                                                        <div class="form-group">
                                                            <label>Confirm Password</label>
                                                            <input id="confirm_password" class="form-control" type="password" name="confirm_password">
                                                            <?php if (isset($_err['confirm_password'])): ?>
                                                                <p style="color: red;"><?= htmlspecialchars($_err['confirm_password']) ?></p>
                                                            <?php endif; ?>
                                                        </div>

                                                        <!-- Add checkbox for password visibility -->
                                                        <div class="form-group">
                                                            <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()"> Show Password
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="profile-row">
                                                    <div class="profile-col d-flex justify-content-end">
                                                        <button class="profile-button-save" type="submit">Save Changes</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</main>

<script>
    // Function to toggle password visibility
    function togglePasswordVisibility() {
        const currentPassword = document.getElementById('current_password');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');
        const type = currentPassword.type === 'password' ? 'text' : 'password';

        currentPassword.type = type;
        newPassword.type = type;
        confirmPassword.type = type;
    }

    // Trigger file input when button is clicked
    document.getElementById('upload-button').addEventListener('click', function() {
        document.getElementById('file-input').click();
    });

    // Handle file selection and display the image
    document.getElementById('file-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const profileImage = document.getElementById('profile-image');
                profileImage.src = e.target.result;
                profileImage.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Hide alert after 3 seconds
    setTimeout(function() {
        var alert = document.querySelector('.alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 3000); // 3 seconds
</script>



<?php
include '../../_foot.php';
