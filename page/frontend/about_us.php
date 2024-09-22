<?php
require '../../_base.php';
include '../../_head2.php'; // Assuming this includes navigation
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Sport Depot</title>
</head>

<body>
    <section class="about-us-section">
        <div class="container">
            <h1>About Us</h1>

            <!-- Our Story Section -->
            <div class="about-story">
                <div class="about-image">
                    <img src="/photos/our_story.jpg" alt="Our Story Image">
                </div>
                <div class="about-content">
                    <h6 class="about-subheading">Our Story</h6>
                    <h2 class="about-heading">The first Sport Depot store started with a passion for active living.</h2>
                    <p>
                        Founded in 2024, Sport Depot began as a small, independent store focused on providing high-quality sports equipment to local athletes and fitness enthusiasts.
                        As our community grew, so did our vision. Today, we’ve expanded into an online platform, bringing the best in sports gear to customers nationwide. Our goal has always been to inspire individuals to embrace an active lifestyle, and we’re proud to support athletes of all levels, from beginners to professionals.
                        Whether it’s gear for training, competition, or recovery, Sport Depot is dedicated to helping you reach your peak performance.
                    </p>
                    <a href="./productDisplay.php" class="cta-button">Explore Our Products</a> <!-- Call to Action Button -->
                </div>
            </div>

            <!-- Our Mission Section -->
            <div class="about-mission">
                <h6 class="about-subheading">Our Mission</h6>
                <h2 class="about-heading">Empowering every athlete to achieve their best.</h2>
                <p>
                    At Sport Depot, our mission is to offer premium sports equipment that meets the diverse needs of athletes. We believe that access to the right gear can make all the difference in performance, safety, and enjoyment of sports.
                    Our commitment is to provide not only the latest technology and innovations in sports equipment but also to ensure that our products are affordable and accessible to everyone. We aim to inspire people to lead healthier, more active lives by equipping them with the tools they need to succeed.
                </p>
                <a href="./contact_us.php" class="cta-button">Contact Us</a> <!-- Call to Action Button -->
            </div>

            <!-- Social Impact Section -->
            <div class="about-impact">
                <div class="impact-content">
                    <h6 class="about-subheading">Our Team and Our Social Impact</h6>
                    <h2 class="about-heading">Supporting communities and making a difference.</h2>
                    <p>
                        Our team at Sport Depot is made up of dedicated sports enthusiasts who believe in the transformative power of physical activity.
                        We are committed to helping individuals and communities thrive through sport, and we actively participate in social initiatives to make sports accessible to all.
                        Through partnerships with local schools, charities, and sports organizations, we focus on promoting active living and providing underprivileged communities with sports equipment and resources.
                        Our efforts aim to break down barriers to sports participation, fostering a culture of inclusion and teamwork.
                    </p>
                </div>
                <div class="impact-image">
                    <img src="/photos/team.jpeg" alt="Our Team Image">
                </div>
            </div>

            <!-- Store Location Section with Google Maps -->
            <div class="store-location">
                <h6 class="about-subheading">Our Store Location</h6>
                <h2 class="about-heading">Visit Us at Our Physical Store</h2>
                <div class="map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.5378121733747!2d101.7239821747306!3d3.2152551967599114!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc3843bfb6a031%3A0x2dc5e067aae3ab84!2sTunku%20Abdul%20Rahman%20University%20of%20Management%20and%20Technology%20(TAR%20UMT)!5e0!3m2!1sen!2smy!4v1726579898091!5m2!1sen!2smy" 
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

            <!-- Optional: Stats Section -->
            <section class="stats-section">
                <div class="container">
                    <div class="stat-item">
                        <h3>10,000+</h3>
                        <p>Happy Customers</p>
                    </div>
                    <div class="stat-item">
                        <h3>500+</h3>
                        <p>Products Available</p>
                    </div>
                    <div class="stat-item">
                        <h3>50+</h3>
                        <p>Community Events Sponsored</p>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <?php include '../../_foot.php'; // Assuming this includes the footer ?>
</body>

</html>
