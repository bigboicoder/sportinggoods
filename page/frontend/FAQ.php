<?php
include '../../_base.php';
//-----------------------------------------------------------------------------



// Page title
$_title = 'Contact Us';
include '../../_head2.php';
?>

<!-- Stylesheet -->
<link rel="stylesheet" href="../../css/frontend/frontend.css">

<body>

    <main class="content">
        <div class="container-faq">

            <div class="container-heading">
                <h3>Frequently Asked Questions</h3>
            </div>
            <div class="container-text">
                <div class="faq-item">
                    <div class="question" style="border-bottom: 1px solid grey;">
                        <a class="question-hyperlink" href="#">1. How do I find the right size for sports equipment?
                            <span class="show-answer">show answer <i class="fa fa-plus-circle"
                                    aria-hidden="true"></i></span>
                            <span class="hide-answer" style="display: none;">hide answer <i class="fa fa-minus-circle"
                                    aria-hidden="true"></i></span>
                        </a>
                    </div>

                    <div class="answer" style="display: none; border-bottom: 1px solid grey;">We provide detailed size charts for most of our products. You can find them on each product's page. Simply match your measurements to the chart to determine the best size for you.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="question" style="border-bottom: 1px solid grey;">
                        <a class="question-hyperlink" href="#">2. What is your return policy on sports equipment?
                            <span class="show-answer">show answer <i class="fa fa-plus-circle"
                                    aria-hidden="true"></i></span>
                            <span class="hide-answer" style="display: none;">hide answer <i class="fa fa-minus-circle"
                                    aria-hidden="true"></i></span>
                        </a>
                    </div>

                    <div class="answer" style="display: none; border-bottom: 1px solid grey;">We accept returns within 30 days of delivery for most sports equipment, as long as the items are unused and in their original packaging. Visit our Returns & Exchanges page for more details.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="question" style="border-bottom: 1px solid grey;">
                        <a class="question-hyperlink" href="#">3. Can I track my order after it has been shipped?
                            <span class="show-answer">show answer <i class="fa fa-plus-circle"
                                    aria-hidden="true"></i></span>
                            <span class="hide-answer" style="display: none;">hide answer <i class="fa fa-minus-circle"
                                    aria-hidden="true"></i></span>
                        </a>
                    </div>

                    <div class="answer" style="display: none; border-bottom: 1px solid grey;">Yes, once your order is shipped, you'll receive a tracking number via email. You can use this number to track your order on our tracking page.

                    </div>
                </div>
                <div class="faq-item">
                    <div class="question" style="border-bottom: 1px solid grey;">
                        <a class="question-hyperlink" href="#">4. Do you offer free shipping on orders?

                            <span class="show-answer">show answer <i class="fa fa-plus-circle"
                                    aria-hidden="true"></i></span>
                            <span class="hide-answer" style="display: none;">hide answer <i class="fa fa-minus-circle"
                                    aria-hidden="true"></i></span>
                        </a>
                    </div>

                    <div class="answer" style="display: none; border-bottom: 1px solid grey;">Yes, we offer free shipping for orders over RM500. For orders below this amount, standard shipping rates apply. Check our Shipping Policy for more information.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="question" style="border-bottom: 1px solid grey;">
                        <a class="question-hyperlink" href="#">5. What payment methods do you accept?
                            <span class="show-answer">show answer <i class="fa fa-plus-circle"
                                    aria-hidden="true"></i></span>
                            <span class="hide-answer" style="display: none;">hide answer <i class="fa fa-minus-circle"
                                    aria-hidden="true"></i></span>
                        </a>
                    </div>

                    <div class="answer" style="display: none; border-bottom: 1px solid grey;">We accept various payment methods, including credit cards, PayPal, and online bank transfers. You can select your preferred payment method during checkout.
                    </div>
                </div>
            </div>
            <!-- Add more FAQ items as needed -->
        </div>
    </main>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.question-hyperlink').forEach(question => {
                question.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default hyperlink behavior

                    // Getting the next sibling element which is the answer div
                    const answer = this.parentNode.nextElementSibling;
                    const questionDiv = this.parentNode; // The .question div
                    const answerDiv = questionDiv.nextElementSibling; // The .answer div
                    const showAnswer = this.querySelector('.show-answer');
                    const hideAnswer = this.querySelector('.hide-answer');

                    // Toggle the display of the answer and the show/hide indicators
                    if (answer.style.display === 'none') {
                        answer.style.display = 'block';
                        answerDiv.style.borderBottom = '1px solid grey';
                        questionDiv.style.borderBottom = 'none';
                        showAnswer.style.display = 'none';
                        hideAnswer.style.display = 'inline'; // Show the 'hide answer' and hide 'show answer'
                    } else {
                        answer.style.display = 'none';
                        answerDiv.style.borderBottom = 'none';
                        questionDiv.style.borderBottom = '1px solid grey';
                        showAnswer.style.display = 'inline';
                        hideAnswer.style.display = 'none'; // Hide the 'hide answer' and show 'show answer'
                    }
                });
            });
        });
    </script>


    <?php
    include '../../_foot.php';
