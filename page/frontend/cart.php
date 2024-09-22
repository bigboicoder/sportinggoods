<?php
include '../../_head2.php';

if (is_post()) {
    $btn = req('btn');
    $id = req('product_id');

    if ($btn == 'clear') {
        set_cart();
    }

    if ($btn == 'delete') {
        // If the delete button is pressed, remove the item from the cart
        $cart = get_cart();
        unset($cart[$id]);
        set_cart($cart);
    }

    // Update the quantity for the item
    $unit = req('quantity' . $id);
    update_cart($id, $unit);
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/frontend/frontend.css">
    <title>Cart</title>
</head>

<body>
    <div class="cart-container">
        <h1>Shopping Cart</h1>
        <!-- Show cart items container div -->
        <div class="left-cart-container">
            <?php
            $count = 0;
            $total = 0;

            $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
            $cart = get_cart();

            foreach ($cart as $id => $unit):
                $stm->execute([$id]);
                $p = $stm->fetch();

                $subtotal = $p->product_price * $unit;
                $count += $unit;
                $total += $subtotal;
            ?>

                <!-- Cart item div -->
                <div class="cart-item">
                    <div class='cart-buttons'>
                        <!-- Form to handle item deletion -->
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?= $id ?>">
                            <input type="hidden" name="btn" value="delete">
                            <button class="cart-delete-btn" type="submit">
                                <img src="/images/delete-icn.svg" />
                            </button>
                        </form>
                    </div>

                    <div class="cart-image">
                        <img src="../backend/uploads/productImage/<?= htmlspecialchars($p->product_image) ?>" alt="" />
                    </div>

                    <div class="cart-desc">
                        <span><?= htmlspecialchars($p->product_name) ?></span>
                        <span><?= htmlspecialchars($p->product_id) ?></span>
                        <span><?= htmlspecialchars($p->product_price) ?></span>
                    </div>

                    <div class="cart-quantity">
                        <form method="post">
                            <button class="cart-minus-btn" type="button" onclick="decreaseQuantity(<?= $id ?>)">
                                <img src="/images/minus.svg" alt="Decrease" />
                            </button>

                            <input type="text" id="quantity<?= $id ?>" name="quantity<?= $id ?>" value="<?= $unit ?>" min="1" max="<?= htmlspecialchars($p->product_stock) ?>" onchange="validateQuantity(<?= $id ?>, <?= $p->product_stock ?>)">

                            <button class="cart-plus-btn" type="button" onclick="increaseQuantity(<?= $id ?>)">
                                <img src="/images/plus.svg" alt="Increase" />
                            </button>

                            <input type="hidden" name="product_id" value="<?= $id ?>">
                            <input type="submit" style="display:none;" />
                        </form>
                    </div>

                    <div class="cart-subtotal"><?= sprintf('%.2f', $subtotal) ?></div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($cart)) : ?>
                <p>No items in cart!</p>
            <?php endif; ?>
        </div>

        <!-- Checkout details div -->
        <div class="right-cart-container">
            <div class="cart-title">Summary</div>

            <!-- Summary details -->
            <div class="summary-details">
                <p>Total Items: <?= $count ?></p>
                <p>Total Price: RM<?= sprintf('%.2f', $total) ?></p>
            </div>

            <!-- Buttons for Checkout and Clear Cart -->
            <div class="cart-summary-buttons">
                <?php if ($cart): ?>
                    <form method="post">
                        <input type="hidden" name="btn" value="clear">
                        <button class="clear-cart-btn" type="submit">Clear Cart</button>
                    </form>
                    <?php if ($_user?->user_role == 'User'): ?>
                        <form action="checkout.php" method="get">
                            <button class="checkout-btn" type="submit">Proceed to Checkout</button>
                        </form>
                    <?php else: ?>
                        <div class="login-prompt">
                            Please <a href="login.php" class="login-link">login</a> as member to checkout
                        </div>
                    <?php endif ?>
                <?php endif ?>
            </div>

            <?php
            $array = get_cart();

            foreach ($array as $item => $unit) {
                echo "id: $item; unit: $unit\n";
            }
            echo "count: " . count($array);
            ?>
        </div>
    </div>

    <script>
        // Function to increase quantity
        function increaseQuantity(id) {
            let quantity = document.getElementById('quantity' + id);
            let max = parseInt(quantity.max); // Ensure max is an integer
            if (parseInt(quantity.value) < max) {
                quantity.value = parseInt(quantity.value) + 1;
                quantity.form.submit(); // Submit form after quantity update
            } else {
                alert("You cannot add more than the available stock.");
            }
        }

        // Function to decrease quantity
        function decreaseQuantity(id) {
            let quantity = document.getElementById('quantity' + id);
            if (parseInt(quantity.value) > 1) {
                quantity.value = parseInt(quantity.value) - 1;
                quantity.form.submit(); // Submit form after quantity update
            } else {
                alert("You cannot select less than 1 quantity.");
            }
        }

        // Function to validate quantity input
        function validateQuantity(id, maxStock) {
            let quantity = document.getElementById('quantity' + id);
            let value = parseInt(quantity.value);
            let min = parseInt(quantity.min);

            if (value > maxStock) {
                alert("You cannot add more than the available stock.");
                quantity.value = maxStock;
            } else if (value < min || isNaN(value)) {
                alert("You cannot select less than 1 quantity.");
                quantity.value = min;
            }
            quantity.form.submit(); // Automatically submit form after validation
        }
    </script>
</body>

<?php
include '../../_foot.php';
?>