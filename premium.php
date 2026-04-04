<?php
require_once("config/config.php");

$stmt = $pdo->prepare("SELECT * FROM user_master WHERE email_id=? LIMIT 1");
$stmt->execute([$_SESSION['userid']]);
$row_acc = $stmt->fetch();

if ($row_acc && $row_acc['subscription_tier'] === 'PREMIUM') {
    header("Location: my_account.php?msg=" . urlencode("You are already a Premium member!"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upgrade to Premium - LendnLearn</title>
    <link rel="stylesheet" href="styles.css?v=20260331b">
</head>
<body>
    <header>
        <div class="container">
            <h1>LendnLearn Premium</h1>
            <nav>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="my_account.php">Back to Account</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="premium-section" style="padding: 4rem 1rem; text-align: center;">
        <div class="container page-stack">
            <h2>Upgrade to Premium</h2>
            <p>Get the most out of LendnLearn for just <strong>$4.99/mo</strong>.</p>
            <div class="account-card" style="max-width: 400px; margin: 2rem auto; text-align: left;">
                <ul style="list-style: none; padding: 0; line-height: 2;">
                    <li>✅ <strong>Unlimited Borrow Requests</strong></li>
                    <li>✅ <strong>Priority Search Ranking</strong> for your uploaded books</li>
                    <li>✅ <strong>Exclusive Pro Badge</strong> <span class="pro-badge">PRO</span> next to your name</li>
                    <li>✅ <strong>Ad-Free Experience</strong> built directly into the platform</li>
                </ul>
                <div style="text-align: center; margin-top: 2rem;">
                    <form action="premium_verify.php" method="POST">
                        <script
                            src="https://checkout.razorpay.com/v1/checkout.js"
                            data-key="rzp_test_mockkey12345"
                            data-amount="499" 
                            data-currency="USD"
                            data-name="LendnLearn Premium"
                            data-description="1 Month Subscription"
                            data-image="cover_img/default-cover.svg"
                            data-prefill.name="<?=htmlspecialchars($row_acc['user_name'])?>"
                            data-prefill.email="<?=htmlspecialchars($row_acc['email_id'])?>"
                            data-theme.color="#3b82f6"
                        ></script>
                        <input type="hidden" custom="Hidden Element" name="hidden">
                    </form>
                </div>
            </div>
            <p><small>*This is a mock transaction for testing purposes.</small></p>
        </div>
    </section>
</body>
</html>
