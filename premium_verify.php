<?php
require_once("config/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentId = $_POST['razorpay_payment_id'] ?? 'mock_payment_123'; 

    $stmt = $pdo->prepare("UPDATE user_master SET subscription_tier='PREMIUM' WHERE email_id=?");
    try {
        $stmt->execute([$_SESSION['userid']]);
        header("Location: my_account.php?msg=" . urlencode("Successfully upgraded to Premium! Welcome to the Pro tier."));
        exit;
    } catch (PDOException $e) {
        header("Location: premium.php?msg=" . urlencode("There was an error upgrading your account. Please contact support."));
        exit;
    }
}

header("Location: premium.php");
exit;
?>
