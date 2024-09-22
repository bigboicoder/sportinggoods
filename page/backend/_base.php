<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null)
{
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null)
{
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null)
{
    // If no URL is provided, default to home.php
    $url ??= '/home.php';
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key)
{
    $f = $_FILES[$key] ?? null;

    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Is money?
function is_money($value)
{
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

// Is email?
function is_email($value)
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

// Return local root path
function root($path = '')
{
    return "$_SERVER[DOCUMENT_ROOT]/$path";
}

// Return base url (host + port)
function base($path = '')
{
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}

// Return folder and file url 
function self_php()
{
    return $_SERVER['HTTP_REFERER'];
}

// ============================================================================
// HTML Helpers
// ============================================================================

// Placeholder for TODO
function TODO()
{
    echo '<span>TODO</span>';
}

// Encode HTML special characters
function encode($value)
{
    return htmlentities($value);
}

// Generate <input type='hidden'>
function html_hidden($key, $attr = '', $value = '')
{
    // If a value is provided, use it, otherwise use $GLOBALS[$key]
    $encodedValue = encode($value ?: ($GLOBALS[$key] ?? ''));
    echo "<input type='hidden' id='$key' name='$key' value='$encodedValue' $attr>";
}

// Generate <input type='text'>
function html_text($key, $attr = '', $value = '')
{
    // If a value is provided, use it, otherwise use $GLOBALS[$key]
    $encodedValue = encode($value ?: ($GLOBALS[$key] ?? ''));
    echo "<input type='text' id='$key' name='$key' value='$encodedValue' $attr>";
}

// Generate <input type='password'>
function html_password($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                 min='$min' max='$max' step='$step' $attr>";
}

// Generate <input type='search'>
function html_search($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='date'>
function html_date($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='date' id='$key' name='$key' value='$value' $attr>";
}

// Generate <textarea>
function html_textarea($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
}

// Generate SINGLE <input type='checkbox'>
function html_checkbox($key, $label = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    $status = $value == 1 ? 'checked' : '';
    echo "<label><input type='checkbox' id='$key' name='$key' value='1' $status $attr>$label</label>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false)
{
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $selected = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? $selected);
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null && !in_array($default, $items)) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}


// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '')
{
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

// Generate table headers <th>
function table_headers($fields, $sort, $dir, $href = '')
{
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class

        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
    }
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='error-message'>
function err($key , $attr = '')
{
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='error-message' $attr>$_err[$key]</span>";
    } else {
        echo '<span></span>';
    }
}

// ============================================================================
// Security
// ============================================================================

// Global user object
$_user = $_SESSION['user'] ?? null;

// Login user
function login($user)
{
    $_SESSION['user'] = $user;
}

// Logout user
function logout($url = '/')
{
    unset($_SESSION['user']);
    redirect($url);
}

// Authorization
function auth(...$roles)
{
    global $_user;
    if ($_user) {
        if ($roles) {
            if (in_array($_user->user_role, $roles)) {
                return; // OK
            }
        } else {
            return; // OK
        }
    }

    redirect('/home.php');
}
// ============================================================================
// Email Functions
// ============================================================================

// Demo Accounts:
// --------------
// AACS3173@gmail.com           npsg gzfd pnio aylm
// BAIT2173.email@gmail.com     ytwo bbon lrvw wclr
// liaw.casual@gmail.com        wtpa kjxr dfcb xkhg
// liawcv1@gmail.com            obyj shnv prpa kzvj

// Initialize and return mail object
function get_mail()
{
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'AACS3173@gmail.com';
    $m->Password = 'npsg gzfd pnio aylm';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, 'Admin');

    return $m;
}

// ============================================================================
// Shopping Cart Functions
// ============================================================================

//get shopping cart
function get_cart()
{
    return $_SESSION['cart'] ?? [];
}

//set shopping cart
function set_cart($cart = [])
{
    $_SESSION['cart'] = $cart;
}

//clean shopping cart - checks all carts and remove products with 0 units
function clean_cart()
{
    $cart = get_cart();

    // loop through each product in the cart
    foreach ($cart as $id => $unit) {
        // if the unit is 0, remove it
        if ($unit <= 0) {
            unset($cart[$id]);
        }
    }

    set_cart($cart);
}

//update shopping cart
function update_cart($id, $unit)
{
    $cart = get_cart();

    if (is_exists($id, 'product', 'product_id')) {
        if ($unit <= 0) { // if the product unit is less than 1, remove it
            unset($cart[$id]);
        } else {
            $cart[$id] = $unit;
        }
    } else { // if product does not exist in database, remove it
        unset($cart[$id]);
    }

    clean_cart();

    set_cart($cart);
}

//add shopping cart
function add_cart($id, $unit)
{
    $cart = get_cart();

    if (is_exists($id, 'product', 'product_id')) {
        if ($unit <= 0) { // if the product unit is less than 1, remove it
            unset($cart[$id]);
        } else {
            if (isset($cart[$id])) { // if product already in cart, add to it
                $cart[$id] += $unit;
            } else { // if product not in cart, set new 
                $cart[$id] = $unit;
            }
        }
    } else { // if product does not exist in database, remove it
        unset($cart[$id]);
    }

    clean_cart();

    set_cart($cart);
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

// Global PDO object
$_db = new PDO('mysql:dbname=sporting', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Is unique?
function is_unique($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field, $excludeId = null, $idField = 'id')
{
    global $_db;

    // Build the base SQL query to check if the value exists in the specified field
    $query = "SELECT COUNT(*) FROM $table WHERE $field = ?";

    // Add a condition to exclude a specific record by its id if provided
    if ($excludeId !== null) {
        $query .= " AND $idField != ?";
    }

    // Prepare the query
    $stm = $_db->prepare($query);

    // Execute the query with parameters based on whether an id exclusion is needed
    $params = [$value];
    if ($excludeId !== null) {
        $params[] = $excludeId; // Add id exclusion to parameters
    }
    $stm->execute($params);

    // Return true if the value exists, false otherwise
    return $stm->fetchColumn() > 0;
}

// ============================================================================
// Global Constants and Variables
// ============================================================================
$_users = $_db->query('SELECT * FROM users')->fetchAll();
$_products = $_db->query('SELECT * FROM product')->fetchAll();
$_orders = $_db->query('SELECT * FROM orders')->fetchAll();



// ============================================================================
// New Part Added
// ============================================================================
function convertDateFormat($date)
{
    $dateObject = new DateTime($date);
    return $dateObject->format('d/m/Y');
}

// Pass in order status to return different class name style
function getStatusBadge($status)
{
    $badge = array(
        'class' => 'default', // Default class
        'text'  => 'Unknown'  // Default text
    );

    switch ($status) {
        case 'Pending':
            $badge['class'] = 'danger';
            $badge['text']  = 'Pending';
            break;
        case 'Processing':
            $badge['class'] = 'info';
            $badge['text']  = 'Processing';
            break;
        case 'Completed':
            $badge['class'] = 'success';
            $badge['text']  = 'Completed';
            break;
        case 'Cancelled':
            $badge['class'] = 'warning';
            $badge['text']  = 'Cancelled';
            break;
        case '1':
            $badge['class'] = 'success';
            $badge['text']  = 'Activate';
            break;
        case '0':
            $badge['class'] = 'warning';
            $badge['text']  = 'Block';
            break;
    }

    return $badge;
}

// ============================================================================
// Crop, resize and save photo
// ============================================================================

function save_photo($f, $folder, $width = 200, $height = 200)
{
    $photo = uniqid() . '.jpg';

    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}
