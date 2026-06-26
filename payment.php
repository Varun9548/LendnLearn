<?php
require_once("config/config.php");

$bookId = intval($_GET['book_id'] ?? $_POST['book_id'] ?? 0);
$returnTo = basename($_GET['return_to'] ?? $_POST['return_to'] ?? 'book_list.php');
$allowedReturns = ['book_list.php', 'home.php', 'search.php', 'my_account.php'];
if (!in_array($returnTo, $allowedReturns, true)) {
    $returnTo = 'book_list.php';
}

if ($bookId <= 0) {
    header("Location: " . $returnTo . "?msg=" . urlencode("Invalid book selection"));
    exit;
}

// Fetch book details
$stmtBook = $pdo->prepare("SELECT * FROM book_master WHERE id=? LIMIT 1");
$stmtBook->execute([$bookId]);
$book = $stmtBook->fetch();

if (!$book) {
    header("Location: " . $returnTo . "?msg=" . urlencode("Book not found"));
    exit;
}

if ($book['email_id'] === $_SESSION['userid']) {
    header("Location: " . $returnTo . "?msg=" . urlencode("You cannot purchase your own uploaded book"));
    exit;
}

// Check if already purchased
$stmtCheck = $pdo->prepare("SELECT id FROM borrow_requests WHERE book_id=? AND requester_email=? AND status='Purchased' LIMIT 1");
$stmtCheck->execute([$bookId, $_SESSION['userid']]);
if ($stmtCheck->fetch()) {
    header("Location: " . $returnTo . "?msg=" . urlencode("You have already purchased this book"));
    exit;
}

// Handle payment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
    $price = floatval($book['price'] ?? 0.00);
    $requesterEmail = $_SESSION['userid'];
    $ownerEmail = $book['email_id'];
    $requestMessage = "Purchased book for $" . number_format($price, 2);
    $requestOn = date("Y-m-d H:i:s");

    try {
        $stmtInsert = $pdo->prepare("INSERT INTO borrow_requests (book_id, requester_email, owner_email, request_message, status, request_on) VALUES (?, ?, ?, ?, 'Purchased', ?)");
        $stmtInsert->execute([$bookId, $requesterEmail, $ownerEmail, $requestMessage, $requestOn]);
        
        header("Location: " . $returnTo . "?msg=" . urlencode("Book '" . $book['book_title'] . "' purchased successfully!"));
        exit;
    } catch (PDOException $e) {
        header("Location: " . $returnTo . "?msg=" . urlencode("Unable to process purchase at this time."));
        exit;
    }
}

$coverImage = (!empty($book['book_cover_image']) && (str_starts_with($book['book_cover_image'], 'http') || file_exists(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $book['book_cover_image'])))) ? $book['book_cover_image'] : 'cover_img/default-cover.svg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - LendnLearn</title>
    <link rel="stylesheet" href="styles.css?v=20260331b">
    <style>
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 30px;
            margin-top: 30px;
            margin-bottom: 50px;
        }
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }
        .book-preview-card {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
            border: 1px solid #eaeaea;
        }
        .book-preview-card img {
            max-width: 180px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            margin-bottom: 20px;
        }
        .price-badge {
            display: inline-block;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-size: 24px;
            font-weight: bold;
            padding: 10px 25px;
            border-radius: 50px;
            margin: 15px 0;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .payment-form-card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #eaeaea;
        }
        /* Animated card graphic */
        .credit-card-graphic {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            border-radius: 12px;
            padding: 20px;
            color: #fff;
            position: relative;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
            margin-bottom: 25px;
            overflow: hidden;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }
        .credit-card-graphic::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .card-chip {
            width: 40px;
            height: 30px;
            background: #f59e0b;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .card-number-display {
            font-size: 20px;
            letter-spacing: 2px;
            font-family: 'Courier New', Courier, monospace;
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
        }
        .card-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-holder-display {
            text-transform: uppercase;
            font-size: 14px;
            max-width: 70%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .card-expiry-display {
            font-size: 14px;
        }
        
        /* Fullscreen animation overlay */
        .payment-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.95);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            color: #fff;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }
        .payment-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid rgba(255, 255, 255, 0.1);
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Animated Checkmark */
        .success-checkmark {
            width: 80px;
            height: 80px;
            position: relative;
            display: none;
            margin-bottom: 20px;
        }
        .success-checkmark .check-icon {
            width: 80px;
            height: 80px;
            position: relative;
            border-radius: 50%;
            box-sizing: content-box;
            border: 4px solid #4caf50;
        }
        .success-checkmark .check-icon::before,
        .success-checkmark .check-icon::after {
            content: '';
            height: 100px;
            position: absolute;
            background: rgba(15, 23, 42, 0.95);
            transform: rotate(-45deg);
        }
        .success-checkmark .check-icon::before {
            width: 30px;
            top: 37px;
            left: -9px;
            transform-origin: 100% 50%;
            border-radius: 100px 0 0 100px;
        }
        .success-checkmark .check-icon::after {
            width: 60px;
            top: 45px;
            left: 30px;
            transform-origin: 0 100%;
            border-radius: 0 100px 100px 0;
            animation: rotatePlaceholder 4.25s ease-in;
        }
        .success-checkmark .check-icon .icon-line {
            height: 5px;
            background-color: #4caf50;
            display: block;
            border-radius: 2px;
            position: absolute;
            z-index: 10;
        }
        .success-checkmark .check-icon .icon-line.line-tip {
            width: 25px;
            left: 14px;
            top: 46px;
            transform: rotate(45deg);
            animation: icon-line-tip 0.75s;
        }
        .success-checkmark .check-icon .icon-line.line-long {
            width: 47px;
            right: 8px;
            top: 38px;
            transform: rotate(-45deg);
            animation: icon-line-long 0.75s;
        }
        .success-checkmark .check-icon .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid rgba(76, 175, 80, 0.5);
            position: absolute;
            left: -4px;
            top: -4px;
            z-index: 10;
            box-sizing: content-box;
        }
        .success-checkmark .check-icon .icon-fix {
            width: 5px;
            height: 85px;
            background-color: rgba(15, 23, 42, 0.95);
            position: absolute;
            left: 28px;
            top: 8px;
            z-index: 1;
            transform: rotate(-45deg);
        }
        
        @keyframes icon-line-tip {
            0% { width: 0; left: 1px; top: 19px; }
            54% { width: 0; left: 1px; top: 19px; }
            70% { width: 50px; left: -8px; top: 37px; }
            84% { width: 17px; left: 21px; top: 48px; }
            100% { width: 25px; left: 14px; top: 46px; }
        }
        @keyframes icon-line-long {
            0% { width: 0; right: 46px; top: 54px; }
            65% { width: 0; right: 46px; top: 54px; }
            84% { width: 55px; right: 0px; top: 35px; }
            100% { width: 47px; right: 8px; top: 38px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>LendnLearn</h1>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="upload.php">Upload Book</a></li>
                    <li><a href="search.php">Search Books</a></li>
                    <li><a href="my_account.php">My Account</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="checkout-section">
        <div class="container">
            <h2 style="text-align: center; margin-top: 30px; font-size: 28px;">Secure Checkout</h2>
            <p style="text-align: center; color: #666; margin-bottom: 20px;">Complete your purchase using our secure simulated payment gateway.</p>
            
            <div class="checkout-container">
                <!-- Book summary card -->
                <div class="book-preview-card fade-in">
                    <h3 style="font-size: 20px; margin-bottom: 15px; color: #333;">Order Summary</h3>
                    <img src="<?=htmlspecialchars($coverImage)?>" alt="<?=htmlspecialchars($book['book_title'])?>" onerror="this.src='cover_img/default-cover.svg'">
                    <h4 style="font-size: 22px; margin-bottom: 5px; color: #111;"><?=htmlspecialchars($book['book_title'])?></h4>
                    <p style="color: #666; font-size: 15px; margin-bottom: 10px;">By <?=htmlspecialchars($book['book_author'])?></p>
                    <p style="color: #888; font-size: 13px;">Genre: <?=htmlspecialchars(ucfirst($book['book_genre']))?></p>
                    
                    <div class="price-badge">
                        $<?=number_format($book['price'] ?? 0.00, 2)?>
                    </div>
                    <p style="color: #999; font-size: 12px; margin-top: 5px;">No extra taxes or transaction fees.</p>
                </div>

                <!-- Payment details card -->
                <div class="payment-form-card fade-in">
                    <h3 style="font-size: 20px; margin-bottom: 20px; color: #333;">Payment Information</h3>
                    
                    <!-- Credit card mockup -->
                    <div class="credit-card-graphic">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div class="card-chip"></div>
                            <span style="font-size: 16px; font-weight: bold; font-style: italic; opacity: 0.8;">VISA</span>
                        </div>
                        <div class="card-number-display" id="cardNumDisplay">•••• •••• •••• ••••</div>
                        <div class="card-bottom">
                            <div>
                                <p style="font-size: 9px; opacity: 0.7; margin-bottom: 2px;">CARDHOLDER</p>
                                <div class="card-holder-display" id="cardHolderDisplay">Your Name</div>
                            </div>
                            <div>
                                <p style="font-size: 9px; opacity: 0.7; margin-bottom: 2px;">EXPIRES</p>
                                <div class="card-expiry-display" id="cardExpiryDisplay">MM/YY</div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form id="paymentForm" method="post" action="payment.php" style="padding:0; box-shadow:none; max-width:100%;">
                        <input type="hidden" name="book_id" value="<?=intval($book['id'])?>">
                        <input type="hidden" name="return_to" value="<?=htmlspecialchars($returnTo)?>">
                        <input type="hidden" name="process_payment" value="1">

                        <div class="form-group">
                            <label for="cardName">Cardholder Name</label>
                            <input type="text" id="cardName" placeholder="John Doe" required autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" id="cardNumber" placeholder="4111 2222 3333 4444" maxlength="19" required autocomplete="off">
                        </div>

                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label for="cardExpiry">Expiration Date</label>
                                <input type="text" id="cardExpiry" placeholder="MM/YY" maxlength="5" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="cardCvv">CVV</label>
                                <input type="password" id="cardCvv" placeholder="123" maxlength="3" required autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 25px;">
                            <button type="submit" id="payBtn" style="background: linear-gradient(135deg, #4f46e5, #4338ca); font-weight: bold; border-radius: 8px;">
                                Pay $<?=number_format($book['price'] ?? 0.00, 2)?> Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Overlay container for processing payment -->
    <div class="payment-overlay" id="paymentOverlay">
        <div class="spinner" id="spinner"></div>
        
        <div class="success-checkmark" id="successCheckmark">
            <div class="check-icon">
                <span class="icon-line line-tip"></span>
                <span class="icon-line line-long"></span>
                <div class="icon-circle"></div>
                <div class="icon-fix"></div>
            </div>
        </div>

        <h3 id="overlayText" style="font-size: 22px; font-weight: 500;">Securing Connection...</h3>
        <p id="overlaySubtext" style="color: #94a3b8; font-size: 14px; margin-top: 8px;">Please do not refresh or click back.</p>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2024 LendnLearn. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cardNameInput = document.getElementById('cardName');
            const cardNumInput = document.getElementById('cardNumber');
            const cardExpiryInput = document.getElementById('cardExpiry');
            const cardCvvInput = document.getElementById('cardCvv');

            const cardHolderDisplay = document.getElementById('cardHolderDisplay');
            const cardNumDisplay = document.getElementById('cardNumDisplay');
            const cardExpiryDisplay = document.getElementById('cardExpiryDisplay');

            const paymentForm = document.getElementById('paymentForm');
            const paymentOverlay = document.getElementById('paymentOverlay');
            const spinner = document.getElementById('spinner');
            const successCheckmark = document.getElementById('successCheckmark');
            const overlayText = document.getElementById('overlayText');
            const overlaySubtext = document.getElementById('overlaySubtext');

            // Format card number
            cardNumInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                let formatted = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) formatted += ' ';
                    formatted += value[i];
                }
                e.target.value = formatted;
                cardNumDisplay.textContent = formatted || '•••• •••• •••• ••••';
            });

            // Format expiry
            cardExpiryInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
                cardExpiryDisplay.textContent = value || 'MM/YY';
            });

            // Sync name
            cardNameInput.addEventListener('input', function (e) {
                cardHolderDisplay.textContent = e.target.value.trim() || 'Your Name';
            });

            // Handle submission animation
            paymentForm.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent instant post

                // Activate overlay
                paymentOverlay.classList.add('active');

                // Step 1: Processing
                setTimeout(() => {
                    overlayText.textContent = "Processing Transaction...";
                }, 1000);

                // Step 2: Approved & Checkmark Animation
                setTimeout(() => {
                    spinner.style.display = 'none';
                    successCheckmark.style.display = 'block';
                    overlayText.textContent = "Payment Approved!";
                    overlaySubtext.textContent = "Updating your library catalog...";
                }, 2500);

                // Step 3: Actual form submission
                setTimeout(() => {
                    paymentForm.submit();
                }, 4000);
            });
        });
    </script>
</body>
</html>
