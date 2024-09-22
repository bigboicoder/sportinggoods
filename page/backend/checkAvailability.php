<?php
require '../../_base.php';

$url = self_php();

// Handle AJAX validation for both username and email
if (str_contains($url, "addAdmin")) {
    if (isset($_GET['checkUsername']) || isset($_GET['checkEmail'])) {
        checkFieldAndValidate();
    }
} else {
    if (isset($_GET['checkUsername']) || isset($_GET['checkEmail'])) {
        checkFieldAndValidate();
    }
}

function checkFieldAndValidate()
{
    // Initialize an empty response array
    $response = ['status' => '', 'message' => ''];

    // Identify the field to validate (either username or email)
    $username = req('checkUsername');
    $email = req('checkEmail');
    $userId = req('user_id');

    if ($username) {
        $response = validateUsername($username, $userId);
    } elseif ($email) {
        $response = validateEmail($email, $userId);
    }

    // Output the validation response in JSON and stop further script execution
    echo json_encode($response);
    exit;
}

// Helper function to validate the username
function validateUsername($username, $userId = null)
{
    // Create response array for username validation
    $response = ['status' => 'success', 'message' => 'Username is available'];

    // Perform validation for the username field
    if ($username === '') {
        $response = ['status' => 'error', 'message' => 'Username is required'];
    } elseif (strpos($username, ' ') !== false) {
        $response = ['status' => 'error', 'message' => 'Username cannot contain spaces'];
    } elseif (strlen($username) < 7) {
        $response = ['status' => 'error', 'message' => 'Username must be more than 7 characters'];
    } elseif (is_exists($username, "users", "user_username", $userId, 'user_id')) {
        $response = ['status' => 'error', 'message' => 'Username already exists'];
    }

    return $response;
}

// Helper function to validate the email
function validateEmail($email, $userId = null)
{
    // Create response array for email validation
    $response = ['status' => 'success', 'message' => 'Email is available'];

    // Perform validation for the email field
    if ($email === '') {
        $response = ['status' => 'error', 'message' => 'Email is required'];
    } elseif (!is_email($email)) {
        $response = ['status' => 'error', 'message' => 'Invalid email format'];
    } elseif (is_exists($email, "users", "user_email", $userId, 'user_id')) {
        $response = ['status' => 'error', 'message' => 'Email already exists'];
    }

    return $response;
}
