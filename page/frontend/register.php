<?php
require '../../_base.php';

$registration_success = false; // Flag for successful registration
$registration_error = false; // Flag for registration error

// ----------------------------------------------------------------------------

if (is_post()) {
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');
    $name     = req('name');
    $username = req('username');
    $contact  = req('contact');

    // Validate: email
    if (!$email) {
        $_err['email'] = 'Required';
    } else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    } else if (!is_unique($email, 'users', 'user_email')) {
        $_err['email'] = 'Duplicated';
    }

    // Validate: password
    if (!$password) {
        $_err['password'] = 'Required';
    } else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Between 5-100 characters';
    }

    // Validate: confirm password
    if (!$confirm) {
        $_err['confirm'] = 'Required';
    } else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5-100 characters';
    } else if ($confirm != $password) {
        $_err['confirm'] = 'Not matched';
    }

    // Validate: name
    if (!$name) {
        $_err['name'] = 'Required';
    } else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    // Validate: username
    if (!$username) {
        $_err['username'] = 'Required';
    } else if (strlen($username) > 100) {
        $_err['username'] = 'Maximum 100 characters';
    } else if (!is_unique($username, 'users', 'user_username')) {
        $_err['username'] = 'Duplicated';
    }

    // Validate: contact
    if (!$contact) {
        $_err['contact'] = 'Required';
    } else if (strlen($contact) > 100) {
        $_err['contact'] = 'Maximum 100 characters';
    }

    // DB operation
    if (!$_err) {
        $stm = $_db->prepare('
            INSERT INTO users (user_name, user_username, user_email, user_contact, user_password, user_role, user_status)
            VALUES (?, ?, ?, ?, SHA1(?), "User", 1)
        ');
        if ($stm->execute([$name, $username, $email, $contact, $password])) {
            $registration_success = true; // Success flag set
        } else {
            $registration_error = true; // Error flag set
        }
    } else {
        $registration_error = true; // Error flag set due to validation
    }
}

// ----------------------------------------------------------------------------

$_title = 'User | Register Member';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?></title>
    <link rel="stylesheet" href="../../css/frontend/frontend.css">
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Add Modal CSS -->

</head>

<body>

    <!-- Back to Homepage link -->
    <div class="back-to-homepage">
        <a href="../../home.php">
            <i class="fas fa-home" aria-hidden="true"></i>
        </a>
    </div>

    <!-- Registration Form -->
    <div class="register-form-container">
        <form class="register-form" method="post" enctype="multipart/form-data">
            <h2>Register</h2>

            <!-- Name Input -->
            <div class="input-container">
                <div class="register-input-group">
                    <label for="name">Name:</label>
                    <?= html_text('name', 'id="name" placeholder="Enter your name" class="register-input"') ?>
                    <?= err('name') ?>
                </div>
            </div>

            <!-- Username Input -->
            <div class="input-container">
                <div class="register-input-group">
                    <label for="username">Username:</label>
                    <?= html_text('username', 'id="username" placeholder="Enter username" class="register-input"') ?>
                    <?= err('username') ?>
                </div>
            </div>

            <!-- Email Input -->
            <div class="input-container">
                <div class="register-input-group">
                    <label for="email">Email Address:</label>
                    <?= html_text('email', 'id="email" placeholder="Enter email address" class="register-input"') ?>
                    <?= err('email') ?>
                </div>
            </div>

            <!-- Contact Input -->
            <div class="input-container">
                <div class="register-input-group">
                    <label for="contact">Contact Number:</label>
                    <?= html_text('contact', 'id="contact" placeholder="Enter contact number" class="register-input"') ?>
                    <?= err('contact') ?>
                </div>
            </div>

            <!-- Password Input -->
            <div class="input-container">
                <div class="register-input-group">
                    <label for="password">Password:</label>
                    <?= html_password('password', 'id="password" placeholder="Enter password" class="register-input"') ?>
                    <?= err('password') ?>
                </div>
            </div>

            <!-- Confirm Password Input -->
            <div class="input-container">
                <div class="register-input-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <?= html_password('confirm', 'id="confirm_password" placeholder="Confirm password" class="register-input"') ?>
                    <?= err('confirm') ?>
                </div>
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn">Register</button>

            <!-- Login Link -->
            <div class="login-link">
                <p>Already have an account? <a href="/page/frontend/login.php">Login Now</a></p>
            </div>
        </form>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <?php if ($registration_success): ?>
                <div class="success">
                    <i class="fas fa-check-circle"></i>
                    <h3>Success</h3>
                    <p>Congratulations, your account has been successfully created.</p>
                    <button onclick="handleContinue()">Continue</button>
                </div>
            <?php elseif ($registration_error): ?>
                <div class="error">
                    <i class="fas fa-times-circle"></i>
                    <h3>Error</h3>
                    <p>Registration failed. Please try again.</p>
                    <button onclick="handleContinue()">Continue</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Script -->
    <script>
        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        // Function to handle the continue button click
        function handleContinue() {
            <?php if ($registration_success): ?>
                window.location.href = '/page/frontend/login.php';
            <?php else: ?>
                closeModal(); // Simply close the modal on error
            <?php endif; ?>
        }

        // Show the modal if registration is successful or failed
        <?php if ($registration_success || $registration_error): ?>
            document.getElementById("myModal").style.display = "flex";
        <?php endif; ?>
    </script>

</body>

</html>