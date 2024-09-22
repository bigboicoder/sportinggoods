<?php
include '../../_head2.php'; // Assuming this includes navigation
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sporting";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product details based on product_id from URL
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($product_id > 0) {
    $query = "SELECT * FROM Product WHERE product_id = $product_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $images = explode(',', $product['product_image']); // Split image names by comma
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "Invalid product.";
    exit;
}

$conn->close();

if (is_post()) {
    $id = req('product_id');
    $unit = req('getquantity') ?? 0;
    add_cart($id, $unit);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']) ?> - Product Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 80%;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }

        .product-details {
            display: flex;
            gap: 30px;
        }

        .product-images {
            flex: 1;
        }

        .product-info {
            flex: 1;
        }

        .product-images img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .product-info h2 {
            margin-bottom: 10px;
        }

        .product-info p {
            margin-bottom: 10px;
        }

        .product-info .price {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .product-info .stock {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .buttons {
            margin-top: 20px;
        }

        .button,
        .back-button {
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-right: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .back-button {
            background-color: #f0ad4e;
        }

        .button:hover,
        .back-button:hover {
            background-color: #45a049;
        }

        .button-fav {
            background-color: #FF5733;
        }

        .button-fav:hover {
            background-color: #E74C3C;
        }

        /* Additional styling for image previews */
        .image-preview {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .image-preview img {
            width: 100px;
            height: 100px;
            cursor: pointer;
            object-fit: cover;
            border: 2px solid transparent;
            transition: border 0.3s;
        }

        .image-preview img:hover {
            border: 2px solid #4CAF50;
        }

        /* Quantity selector styles */
        .quantity-selector {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }

        /* Hide HTML5 Up and Down arrows on input of type number */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            appearance: textfield;
            -moz-appearance: textfield;
            /* Firefox */
        }

        /* Adjusted quantity selector styles */
        .quantity-selector button {
            background-color: #ddd;
            border: 1px solid #ccc;
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
        }

        .quantity-selector input {
            text-align: center;
            border: 1px solid #ccc;
            width: 50px;
            padding: 5px;
            margin: 0 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Back Button -->
        <button class="back-button" onclick="window.location.href='productDisplay.php'">Back to Products</button>

        <h1><?= htmlspecialchars($product['product_name']) ?></h1>
        <div class="product-details">
            <!-- Main Product Image -->
            <div class="product-images">
                <img id="mainImage" src="../backend/uploads/productImage/<?= htmlspecialchars($images[0]) ?>" alt="Main Image">
                <!-- Image Previews -->
                <div class="image-preview">
                    <?php foreach ($images as $index => $image): ?>
                        <img src="../backend/uploads/productImage/<?= htmlspecialchars($image) ?>" alt="Preview Image <?= $index + 1 ?>" onclick="changeImage('<?= htmlspecialchars($image) ?>')">
                    <?php endforeach; ?>
                </div>
            </div>

            <?php //retrieve cart, current product id
            $cart = get_cart();
            $id = $product['product_id'];
            $unit = 0;
            ?>

            <!-- Product Information -->
            <div class="product-info">
                <h2>Description</h2>
                <p><?= htmlspecialchars($product['product_description']) ?></p>
                <p class="price">$<?= htmlspecialchars($product['product_price']) ?></p>
                <p class="stock">Available Stock: <?= htmlspecialchars($product['product_stock']) ?></p>

                <!-- Quantity Selector -->
                <div class="quantity-selector">
                    <button onclick="decreaseQuantity()">-</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= htmlspecialchars($product['product_stock']) ?>">
                    <button onclick="increaseQuantity()">+</button>
                </div>

                <!-- Buttons   -->
                <div class="buttons">
                    <form method="POST" onsubmit="return validateAddToCart()">
                        <? html_hidden('product_id') //hidden input to post the product id 
                        ?>
                        <input type='hidden' id='getquantity' name='getquantity' value='1'>
                        <input type="submit" class="button" value="Add to Cart">
                        <button class="button button-fav" onclick="addToFavourite(<?= $product['product_id'] ?>)">Add to Wishlist</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script to change the main image when a preview image is clicked and handle quantity -->
    <script>
        function getQuantity() {
            quantity = document.getElementById('quantity').value;
            document.getElementById('getquantity').value = quantity;
        }
        
        function changeImage(imageSrc) {
            document.getElementById('mainImage').src = '../backend/uploads/productImage/' + imageSrc;
        }

        function validateAddToCart() {
            getQuantity();
            quantity = document.getElementById('getquantity').value;
            if (quantity <= 0) {
                alert("Error adding to cart! Quantity cannot be 0!");
                return false;
            } else {
                alert("Added " + quantity + " of Product " + productId + " to cart!");
            }
            return true;
        }

        function addToFavourite(productId) {
            //if (isset($_user) && $_user) {
            //    alert("Product " + productId + " added to wishlist!");
            //} 
            alert("Please log in to use this feature!");
        }

        // Quantity increment/decrement
        function increaseQuantity() {
            let quantity = document.getElementById('quantity');
            let max = parseInt(quantity.max); // Ensure max is an integer
            if (parseInt(quantity.value) < max) {
                quantity.value = parseInt(quantity.value) + 1;
                getQuantity();
            } else {
                alert("You cannot add more than the available stock.");
            }
        }

        function decreaseQuantity() {
            let quantity = document.getElementById('quantity');
            if (parseInt(quantity.value) > 1) {
                quantity.value = parseInt(quantity.value) - 1;
                getQuantity();
            }
        }

        // Ensure user can't input a number greater than the stock or smaller than 1
        document.getElementById('quantity').addEventListener('input', function() {
            let quantity = document.getElementById('quantity');
            let max = parseInt(quantity.max); // Ensure max is an integer
            if (parseInt(quantity.value) > max) {
                quantity.value = max;
                alert("You cannot select more than the available stock.");
            }
            let min = parseInt(quantity.min);
            if (parseInt(quantity.value) < min) {
                quantity.value = min;
                alert("You cannot select less than 1 quantity.");
            }
        });
    </script>

</body>

</html>