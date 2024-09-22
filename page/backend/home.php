<?php
require_once '_base.php'; // Include server settings and necessary configurations
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Home</title>
    <?php require '_head2.php'; // Include header and navigation bar ?>
</head>

<body>
    <!-- Flash Message -->
    <?php if ($msg = temp('info')): ?>
        <div class="alert alert-info" id="flash-message">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <!-- Main Section -->
    <main class="home-main">
        <div class="main-text">
            <h1 class="main-heading">Buy your Sport Equipment at home</h1>
            <p>Sporting Goods are just a few clicks away</p>
            <a class="main-btn" href="page/frontend/productDisplay.php">Learn More</a>
        </div>
        <div class="main-background-image"></div>
    </main>

    <!-- Section Separator -->
    <div class="section-seperater"></div>

    <!-- Script for Flash Message Fade-out -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Set a timeout to hide the flash message after 3 seconds
            setTimeout(function() {
                var flashMessage = document.getElementById('flash-message');
                if (flashMessage) {
                    flashMessage.classList.add('hide');
                    // Remove the element from the DOM after the fade-out effect
                    setTimeout(function() {
                        flashMessage.remove();
                    }, 500); // Match the transition duration (0.5s)
                }
            }, 3000); // Time before hiding (3 seconds)
        });

        // Hide alert after 3 seconds
        setTimeout(function() {
            var alert = document.querySelector('.alert');
            if (alert) {
                alert.style.display = 'none';
            }
        }, 3000); // 3 seconds
    </script>

    <?php require '_foot.php'; // Include footer ?>
</body>

</html>
