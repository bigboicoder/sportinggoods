<?php
include '../../_base.php';

// ----------------------------------------------------------------------------
// Handle form submission
if (is_post()) {
    $email = req('email');

    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    } else if (!is_exists($email, 'users', 'user_email')) {
        $_err['email'] = 'Not exists';
    }

    // If no validation errors, send reset token
    if (!$_err) {
        // (1) Select user based on email
        $stm = $_db->prepare('SELECT * FROM users WHERE user_email = ?');
        $stm->execute([$email]);
        $u = $stm->fetch();

        // (2) Generate token id
        $id = sha1(uniqid() . rand());

        // (3) Delete old token and insert new token
        $stm = $_db->prepare('
            DELETE FROM token WHERE id = ?;

            INSERT INTO token (token_id, date_expired, id)
            VALUES (?, ADDTIME(NOW(), "00:05"), ?);
        ');
        $stm->execute([$u->user_id, $id, $u->user_id]);

        // (4) Generate reset password URL with the token
        $url = base("page/frontend/token.php?id=$id");

        // (5) Send reset password email
        $m = get_mail();
        $m->addAddress($u->user_email, $u->user_name);

        // Embed profile image if available
        $profileImage = '';
        if (!empty($u->user_photo)) {
            // Attach user profile photo to the email
            $m->addEmbeddedImage("../../uploads/$u->user_photo", 'photo');
            $profileImage = "<img src='cid:photo' style='width: 150px; height: 150px; border-radius: 50%; border: 3px solid #ccc; margin-bottom: 20px;'>";
        }

        $m->isHTML(true);
        $m->Subject = 'Reset Password';
        $m->Body = "
        <div style='font-family: Arial, sans-serif; color: #333; text-align: center; padding: 20px; border: 1px solid #ddd; max-width: 600px; margin: auto;'>
            $profileImage
            <h1 style='color: #e74c3c;'>Reset Your Password</h1>
            <p>Dear <strong>$u->user_name</strong>,</p>
            <p>We received a request to reset your password. If you did not make this request, please ignore this email. Otherwise, you can reset your password using the link below:</p>
            <a href='$url' style='background-color: #6772e5; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px;'>Reset Password</a>
            <p style='margin-top: 30px;'>Thank you,<br>The Admin Team</p>
        </div>
        ";

        // Send the email
        $m->send();

        // Display success message and redirect
        temp('info', 'Email sent');
        redirect('/');
    }
}

// ----------------------------------------------------------------------------
$_title = 'User | Reset Password';

?>

<!-- Stylesheet -->
<link rel="stylesheet" href="../../css/frontend/frontend.css">

<!-- Password Reset Form -->
<div class="reset-form-container">
    <form method="post">
        <h1>Reset your password</h1>
        <p>Enter the email address associated with your account and we'll send you a link to reset your password.</p>

        <!-- Email Input Field -->
        <div class="input-container">
            <label for="email">Email:</label>
            <?= html_text('email', 'id="email" placeholder="Enter your email" maxlength="100" aria-label="Email"') ?>
            <?= err('email') ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn">Continue</button>

        <!-- Sign-up Link -->
        <p>Don't have an account? <a href="./register.php">Sign up</a></p>
    </form>
</div>
