<?php
include '../../_base.php';

// ----------------------------------------------------------------------------
// (1) Delete expired tokens
$_db->query('DELETE FROM token WHERE date_expired < NOW()');

$id = req('id');

// (2) Is token id valid?
if (!is_exists($id, 'token', 'token_id')) {
    temp('info', 'Invalid token. Try again');
    redirect('/');
}

// Handle form submission
if (is_post()) {
    $password = req('password');
    $confirm  = req('confirm');

    // Validation: password
    if ($password == '') {
        $_err['password'] = 'Required';
    } else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Between 5-100 characters';
    }

    // Validation: confirm
    if ($confirm == '') {
        $_err['confirm'] = 'Required';
    } else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5-100 characters';
    } else if ($confirm != $password) {
        $_err['confirm'] = 'Not matched';
    }

    // Database operation: If no errors, update user password and delete token
    if (!$_err) {
        $stm = $_db->prepare('
            UPDATE users
            SET user_password = SHA1(?)
            WHERE user_id = (SELECT id FROM token WHERE token_id = ?);

            DELETE FROM token WHERE token_id = ?;
        ');
        $stm->execute([$password, $id, $id]);

        temp('info', 'Password updated');
        redirect('../frontend/login.php');
    }
}

// ----------------------------------------------------------------------------
$_title = 'User | Reset Password';
?>

<!-- Stylesheet -->
<link rel="stylesheet" href="../../css/frontend/frontend.css">

<!-- Password Reset Form -->
<div class="reset-form-container">
    <form method="post" class="form">
        <h1>Reset your password</h1>
        <p>Enter and confirm your new password below.</p>

        <!-- New Password Input -->
        <div class="input-container">
            <label for="password">New Password:</label>
            <?= html_password('password', 'id="password" placeholder="Enter your new password" maxlength="100" aria-label="New Password"') ?>
            <?= err('password') ?>
        </div>

        <!-- Confirm New Password Input -->
        <div class="input-container">
            <label for="confirm">Confirm New Password:</label>
            <?= html_password('confirm', 'id="confirm" placeholder="Confirm your new password" maxlength="100" aria-label="Confirm New Password"') ?>
            <?= err('confirm') ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn">Continue</button>

        <!-- Login Link -->
        <p>Don't have an account? <a href="./register.php">Sign up</a></p>
    </form>
</div>
