<?php
require '../../_base.php';

$login_success = false;  // Flag for successful login
$login_failed = false;   // Flag for login failure
$countdown_time = 5;     // Countdown time in seconds

// ----------------------------------------------------------------------------

if (is_post()) {
    $email = req('email');
    $password = req('password');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }

    // Validate: password
    if ($password == '') {
        $_err['password'] = 'Required';
    }

    // Login user
    if (!$_err) {
        $stm = $_db->prepare('
            SELECT * FROM users
            WHERE user_email = ? AND user_password = SHA1(?)
        ');
        $stm->execute([$email, $password]);
        $u = $stm->fetch();

        if ($u) {
            login($u);  // Store the user in session using the global login function

            $_SESSION['login_success'] = true;  // Set login success flag
            $_SESSION['pending_login'] = $u;    // Store pending login user info

            header("Location: login.php");      // Redirect back to login to display the success message
            exit();
        } else {
            $_SESSION['login_failed'] = true;  // Set session flag for failed login
            header("Location: login.php");  // Redirect to the same page to show modal
            exit();
        }
    }
}

// Handle session flags after redirect
if (isset($_SESSION['login_success'])) {
    $login_success = true;
    $user = $_SESSION['pending_login'];  // Retrieve the pending login user info
    unset($_SESSION['login_success']);
    unset($_SESSION['pending_login']);  // Remove pending login from session

} elseif (isset($_SESSION['login_failed'])) {
    $login_failed = true;
    unset($_SESSION['login_failed']);
}

// ----------------------------------------------------------------------------

$_title = 'Login';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?></title>
    <link rel="stylesheet" href="../../css/frontend/frontend.css">
    <link rel="stylesheet" href="../../css/frontend/modal.css"> <!-- Linking modal CSS -->
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<!-- Display flash message here -->
<?php if ($msg = temp('info')): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<body>

    <!-- Back to Homepage link -->
    <div class="back-to-homepage">
        <a href="../../home.php">
            <i class="fas fa-home" aria-hidden="true"></i>
        </a>
    </div>

    <!-- Login Form -->
    <div class="login-form-container">
        <form class="login-form" method="post">
            <h2>Login</h2>

            <!-- Email input -->
            <div class="input-container">
                <label for="email">Email:</label>
                <?= html_text('email', 'id="email" placeholder="Enter your email" maxlength="100" aria-label="Email"') ?>
                <?= err('email') ?>
            </div>

            <!-- Password input -->
            <div class="input-container">
                <label for="password">Password:</label>
                <div class="password-container">
                    <?= html_password('password', 'id="password" placeholder="Enter your password" maxlength="100" aria-label="Password"') ?>
                    <span class="toggle-password">
                        <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                    </span>
                </div>
                <?= err('password') ?>
            </div>

            <!-- Remember Me and Forgot Password options -->
            <div class="extra-options">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>
                <div class="forgot-password">
                    <a href="/page/frontend/reset.php">Forgot Password?</a>
                </div>
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn">Login</button>

            <!-- Register link -->
            <div class="register-link">
                <p>Don't have an account? <a href="/page/frontend/register.php">Register Now</a></p>
            </div>
        </form>
    </div>

    <!-- Modal for Success and Failure -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <?php if ($login_success): ?>
                <div class="success">
                    <i class="fas fa-check-circle"></i>
                    <h3>Login Successful</h3>
                    <p>You will be redirected to the homepage in <span id="countdown"><?= $countdown_time ?></span> seconds.</p>
                    <button onclick="redirectNow()">Go Now</button>
                </div>
            <?php elseif ($login_failed): ?>
                <div class="error">
                    <i class="fas fa-times-circle"></i>
                    <h3>Login Failed</h3>
                    <p>Invalid email or password. Please try again.</p>
                    <button onclick="closeModal()">Try Again</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Script -->
    <script>
        // Function to close the modal for login failure
        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        // Function to redirect immediately based on user role
        function redirectNow() {
            if (userRole === 'Admin') {
                window.location.href = '../../index.php'; // Redirect to admin dashboard
            } else {
                window.location.href = '../../home.php'; // Redirect to homepage for regular users
            }
        }

        <?php if ($login_success): ?>
            let countdown = <?= $countdown_time ?>;
            const userRole = "<?= $user->user_role ?>"; // Pass the user role to JavaScript
            const countdownElement = document.getElementById('countdown');
            const interval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;
                if (countdown === 0) {
                    clearInterval(interval);
                    redirectNow(); // Call the redirect function after the countdown finishes
                }
            }, 1000);

            document.getElementById("myModal").style.display = "flex";
        <?php elseif ($login_failed): ?>
            // Show the modal for login failure
            document.getElementById("myModal").style.display = "flex";
        <?php endif; ?>


        // Hide alert after 3 seconds
        setTimeout(function() {
            var alert = document.querySelector('.alert');
            if (alert) {
                alert.style.display = 'none';
            }
        }, 3000); // 3 seconds

        // Toggle password visibility
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            // Toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);

            // Toggle the icon
            this.classList.toggle("fa-eye-slash");
        });
    </script>

</body>

</html>