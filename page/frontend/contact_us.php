<?php
include '../../_base.php';
//-----------------------------------------------------------------------------

// Initialize error array
$_err = [];

// Handle form submission
if (is_post()) {
    $name = req('name');
    $email = req('email');
    $subject = req('subject');
    $message = req('message');

    // Validate: Name, Email, Subject, and Message
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    if ($subject == '') {
        $_err['subject'] = 'Required';
    }
    if ($message == '') {
        $_err['message'] = 'Required';
    }

    // If no validation errors, send confirmation email
    if (!$_err) {
        // (1) Send confirmation email
        $m = get_mail();
        $m->addAddress($email, $name);
        $m->isHTML(true);
        $m->Subject = 'Inquiry Received - Thank You!';
        $m->Body = "
        <div style='font-family: Arial, sans-serif; color: #333; text-align: center; padding: 20px; background-color: #f9f9f9;'>
            <div style='background-color: #fff; border-radius: 8px; padding: 30px; max-width: 600px; margin: auto; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);'>
                <h1 style='color: #e74c3c; margin-bottom: 20px;'>Thank You for Your Inquiry</h1>
                <p style='font-size: 16px;'>Dear <strong style='color: #800080;'>$name</strong>,</p>
                <p style='font-size: 16px; margin-bottom: 30px;'>We have successfully received your inquiry with the details below:</p>
                <div style='border: 1px solid #e0e0e0; padding: 20px; border-radius: 5px; background-color: #fdfdfd;'>
                    <p style='margin: 10px 0; font-size: 15px;'><strong>Subject:</strong> $subject</p>
                    <p style='margin: 10px 0; font-size: 15px;'><strong>Message:</strong> $message</p>
                </div>
                <p style='font-size: 16px; margin-top: 30px;'>We will get back to you as soon as possible.</p>
                <p style='margin-top: 50px; font-size: 16px;'>Best Regards,<br><strong>The Support Team</strong></p>
            </div>
        </div>
        ";



        // Send the email
        if ($m->send()) {
            // Display success message
            temp('info', 'Inquiry received. A confirmation email has been sent with your details.');
        } else {
            temp('info', 'Your inquiry was received, but we were unable to send the confirmation email.');
        }
    }
}

// Page title
$_title = 'Contact Us';
include '../../_head2.php';
?>


<!-- Display flash message here -->
<?php if ($msg = temp('info')): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Stylesheet -->
<link rel="stylesheet" href="../../css/frontend/frontend.css">

<div class="contact-us-form-container">
    <form method="post">
        <h1>Contact Us</h1>

        <!-- Name Input -->
        <div class="input-container">
            <label for="name">Name:</label>
            <span class="input-icon"><i class="fas fa-id-card"></i></span>
            <input type="text" id="name" name="name" placeholder="Enter your name" maxlength="255" required>
            <?php if (isset($_err['name'])): ?>
                <div class="error"><?= htmlspecialchars($_err['name']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Email Input -->
        <div class="input-container">
            <label for="email">Email:</label>
            <span class="input-icon"><i class="fas fa-envelope"></i></span>
            <input type="email" id="email" name="email" placeholder="Enter your email" maxlength="255" required>
            <?php if (isset($_err['email'])): ?>
                <div class="error"><?= htmlspecialchars($_err['email']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Subject Input -->
        <div class="input-container">
            <label for="subject">Subject:</label>
            <span class="input-icon"><i class="fas fa-pen"></i></span>
            <input type="text" id="subject" name="subject" placeholder="Enter your subject" maxlength="255" required>
            <?php if (isset($_err['subject'])): ?>
                <div class="error"><?= htmlspecialchars($_err['subject']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Message Input -->
        <div class="input-container">
            <label for="message">Please Drop Us a Message Here:</label>
            <textarea id="message" name="message" placeholder="Enter your message" required></textarea>
            <?php if (isset($_err['message'])): ?>
                <div class="error"><?= htmlspecialchars($_err['message']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn">Send Us A Message</button>
    </form>
</div>

<script>
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
?>