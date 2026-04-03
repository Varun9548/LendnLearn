<?php
require_once("config/config.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['request_book'])) {
    header("Location: search.php");
    exit;
}

$bookId = intval($_POST['book_id'] ?? 0);
$returnTo = basename($_POST['return_to'] ?? 'book_list.php');
$allowedReturns = ['book_list.php', 'home.php', 'search.php', 'my_account.php'];
if (!in_array($returnTo, $allowedReturns, true)) {
    $returnTo = 'book_list.php';
}

if ($bookId <= 0) {
    header("Location: " . $returnTo . "?msg=" . urlencode("Invalid book request"));
    exit;
}

$stmtBook = $pdo->prepare("SELECT id, book_title, email_id FROM book_master WHERE id=? LIMIT 1");
$stmtBook->execute([$bookId]);
$rowBook = $stmtBook->fetch();

if (!$rowBook) {
    header("Location: " . $returnTo . "?msg=" . urlencode("Book not found"));
    exit;
}

if ($rowBook['email_id'] === $_SESSION['userid']) {
    header("Location: " . $returnTo . "?msg=" . urlencode("You cannot request your own book"));
    exit;
}

$requesterEmail = $_SESSION['userid'];
$ownerEmail = $rowBook['email_id'];

$stmtUser = $pdo->prepare("SELECT subscription_tier FROM user_master WHERE email_id=? LIMIT 1");
$stmtUser->execute([$requesterEmail]);
$rowUser = $stmtUser->fetch();

if ($rowUser && $rowUser['subscription_tier'] === 'FREE') {
    $currentMonth = date('Y-m');
    $stmtCount = $pdo->prepare("SELECT COUNT(*) as req_count FROM borrow_requests WHERE requester_email=? AND CAST(request_on AS TEXT) LIKE ?");
    $stmtCount->execute([$requesterEmail, $currentMonth . '%']);
    $rowCount = $stmtCount->fetch();
    if ($rowCount && (int)$rowCount['req_count'] >= 2) {
        header("Location: premium.php?msg=" . urlencode("You've reached your monthly limit of 2 requests. Upgrade to Premium for unlimited requests."));
        exit;
    }
}

$stmtExisting = $pdo->prepare("SELECT id FROM borrow_requests WHERE book_id=? AND requester_email=? AND status='Pending' LIMIT 1");
$stmtExisting->execute([$bookId, $requesterEmail]);
if ($stmtExisting->fetch()) {
    header("Location: " . $returnTo . "?msg=" . urlencode("You already sent a borrow request for this book"));
    exit;
}

$requestMessage = "I'd like to borrow this book.";
$requestOn = date("Y-m-d H:i:s");

$stmtInsert = $pdo->prepare("INSERT INTO borrow_requests (book_id, requester_email, owner_email, request_message, status, request_on) VALUES (?, ?, ?, ?, 'Pending', ?)");

try {
    $stmtInsert->execute([$bookId, $requesterEmail, $ownerEmail, $requestMessage, $requestOn]);
    header("Location: " . $returnTo . "?msg=" . urlencode("Borrow request sent successfully"));
    exit;
} catch (PDOException $e) {
    header("Location: " . $returnTo . "?msg=" . urlencode("Unable to send borrow request right now"));
    exit;
}
?>