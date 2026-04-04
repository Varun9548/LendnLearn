<?php
require_once("config/config.php");

$message = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_account'])) {
    $user_name = trim($_POST['user_name'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($user_name === '') {
        $message = 'Username cannot be empty';
    } else {
        $updateSql = "UPDATE user_master SET user_name=?, update_by=?";
        $params = [$user_name, $_SESSION['userid']];

        if ($password !== '') {
            $updateSql .= ", password=?";
            $params[] = $password;
        }

        $updateSql .= " WHERE email_id=?";
        $params[] = $_SESSION['userid'];

        $stmt = $pdo->prepare($updateSql);

        if ($stmt->execute($params)) {
            $_SESSION['uname'] = trim($_POST['user_name']);
            header("Location: my_account.php?msg=" . urlencode("Account updated successfully"));
            exit;
        }

        $message = "Unable to update account right now.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_action'])) {
    $requestId = intval($_POST['request_id'] ?? 0);
    $requestAction = trim($_POST['request_action'] ?? '');
    $allowedActions = ['Approved', 'Rejected', 'Returned'];

    if ($requestId > 0 && in_array($requestAction, $allowedActions, true)) {
        $stmt = $pdo->prepare("UPDATE borrow_requests SET status=? WHERE id=? AND owner_email=?");
        $stmt->execute([$requestAction, $requestId, $_SESSION['userid']]);
        header("Location: my_account.php?msg=" . urlencode("Borrow request updated"));
        exit;
    }
}

$stmt_acc = $pdo->prepare("SELECT * FROM user_master WHERE email_id=? LIMIT 1");
$stmt_acc->execute([$_SESSION['userid']]);
$row_acc = $stmt_acc->fetch();

$stmt_b = $pdo->prepare("SELECT book_title, book_author, book_location, create_on FROM book_master WHERE email_id=? ORDER BY create_on DESC");
$stmt_b->execute([$_SESSION['userid']]);
$res_books = $stmt_b->fetchAll();

$stmt_in = $pdo->prepare("SELECT r.id, r.status, r.request_message, r.request_on, b.book_title, u.user_name AS requester_name FROM borrow_requests r LEFT JOIN book_master b ON b.id = r.book_id LEFT JOIN user_master u ON u.email_id = r.requester_email WHERE r.owner_email=? ORDER BY r.request_on DESC");
$stmt_in->execute([$_SESSION['userid']]);
$res_incoming_requests = $stmt_in->fetchAll();

$stmt_out = $pdo->prepare("SELECT r.status, r.request_message, r.request_on, b.book_title, u.user_name AS owner_name FROM borrow_requests r LEFT JOIN book_master b ON b.id = r.book_id LEFT JOIN user_master u ON u.email_id = r.owner_email WHERE r.requester_email=? ORDER BY r.request_on DESC");
$stmt_out->execute([$_SESSION['userid']]);
$res_outgoing_requests = $stmt_out->fetchAll();

$editMode = isset($_GET['edit']) || (isset($_POST['save_account']) && $message !== '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - LendnLearn</title>
    <link rel="stylesheet" href="styles.css?v=20260331b">
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

    <section class="my-account">
        <div class="container page-stack">
            <h2>My Account</h2>

            <?php if ($message !== '') { ?>
                <div class="status-message"><?=htmlspecialchars($message)?></div>
            <?php } ?>

            <div class="account-details account-card">
                <h3>Account Information</h3>
                <p><strong>Username:</strong> <?=htmlspecialchars($row_acc['user_name'] ?? '')?><?php if (isset($row_acc['subscription_tier']) && $row_acc['subscription_tier'] === 'PREMIUM') echo ' <span class="pro-badge" title="Premium Member">PRO</span>'; ?></p>
                <p><strong>Email:</strong> <?=htmlspecialchars($row_acc['email_id'] ?? '')?></p>
                <p><strong>Member Since:</strong> <?=htmlspecialchars($row_acc['create_on'] ?? '')?></p>
                <p><strong>Plan:</strong> <?=isset($row_acc['subscription_tier']) && $row_acc['subscription_tier'] === 'PREMIUM' ? 'Premium (Unlimited requests)' : 'Free (2 borrow requests/month)'?></p>
                <?php if (!isset($row_acc['subscription_tier']) || $row_acc['subscription_tier'] !== 'PREMIUM') { ?>
                <div style="margin: 15px 0;">
                    <a href="premium.php" class="account-btn" style="background: linear-gradient(135deg, #f59e0b, #d97706); color:#fff; border:none; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);">⭐ Upgrade to Premium</a>
                </div>
                <?php } ?>

                <h3>My Uploaded Books</h3>
                <ul id="uploadedBooks" class="account-book-list">
                    <?php if (count($res_books) > 0) { ?>
                        <?php foreach($res_books as $row_book) { ?>
                            <li>
                                <strong><?=htmlspecialchars($row_book['book_title'])?></strong><br>
                                <span><?=htmlspecialchars($row_book['book_author'])?> · <?=htmlspecialchars($row_book['book_location'] ?: 'Location not shared')?> · <?=htmlspecialchars($row_book['create_on'])?></span>
                            </li>
                        <?php } ?>
                    <?php } else { ?>
                        <li>You have not uploaded any books yet.</li>
                    <?php } ?>
                </ul>
            </div>

            <div class="account-actions">
                <a class="account-btn" href="my_account.php?edit=1">Edit Account</a>
                <a class="account-btn secondary-btn-link" href="logout.php">Logout</a>
            </div>

            <div class="account-details account-card">
                <h3>Incoming Borrow Requests</h3>
                <ul class="account-book-list">
                    <?php if (count($res_incoming_requests) > 0) { ?>
                        <?php foreach($res_incoming_requests as $incoming) { ?>
                            <li>
                                <strong><?=htmlspecialchars($incoming['book_title'] ?: 'Book request')?></strong><br>
                                <span>Requested by <?=htmlspecialchars($incoming['requester_name'] ?: 'Reader')?> · <?=htmlspecialchars($incoming['request_on'])?></span><br>
                                <span>Status: <strong><?=htmlspecialchars($incoming['status'])?></strong></span>
                                <?php if (!empty($incoming['request_message'])) { ?>
                                    <div class="request-note"><?=htmlspecialchars($incoming['request_message'])?></div>
                                <?php } ?>
                                <?php if ($incoming['status'] === 'Pending') { ?>
                                    <div class="request-actions">
                                        <form method="post" action="my_account.php">
                                            <input type="hidden" name="request_id" value="<?=intval($incoming['id'])?>">
                                            <button type="submit" name="request_action" value="Approved" class="borrow-btn small-btn">Approve</button>
                                        </form>
                                        <form method="post" action="my_account.php">
                                            <input type="hidden" name="request_id" value="<?=intval($incoming['id'])?>">
                                            <button type="submit" name="request_action" value="Rejected" class="warning-btn">Reject</button>
                                        </form>
                                    </div>
                                <?php } elseif ($incoming['status'] === 'Approved') { ?>
                                    <div class="request-actions">
                                        <form method="post" action="my_account.php">
                                            <input type="hidden" name="request_id" value="<?=intval($incoming['id'])?>">
                                            <button type="submit" name="request_action" value="Returned" class="secondary-btn-link">Mark Returned</button>
                                        </form>
                                    </div>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    <?php } else { ?>
                        <li>No incoming borrow requests yet.</li>
                    <?php } ?>
                </ul>
            </div>

            <div class="account-details account-card">
                <h3>My Borrow Requests</h3>
                <ul class="account-book-list">
                    <?php if (count($res_outgoing_requests) > 0) { ?>
                        <?php foreach($res_outgoing_requests as $outgoing) { ?>
                            <li>
                                <strong><?=htmlspecialchars($outgoing['book_title'] ?: 'Book request')?></strong><br>
                                <span>Owner: <?=htmlspecialchars($outgoing['owner_name'] ?: 'Reader')?> · <?=htmlspecialchars($outgoing['request_on'])?></span><br>
                                <span>Status: <strong><?=htmlspecialchars($outgoing['status'])?></strong></span>
                            </li>
                        <?php } ?>
                    <?php } else { ?>
                        <li>You have not sent any borrow requests yet.</li>
                    <?php } ?>
                </ul>
            </div>

            <?php if ($editMode) { ?>
                <div class="account-details account-card">
                    <h3>Edit Account</h3>
                    <form method="post" action="my_account.php" class="account-edit-form">
                        <div class="form-group">
                            <label for="user_name">Username</label>
                            <input type="text" id="user_name" name="user_name" value="<?=htmlspecialchars($row_acc['user_name'] ?? '')?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="text" id="password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                        <div class="form-group">
                            <button type="submit" name="save_account" value="1">Save Changes</button>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 LendnLearn. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
