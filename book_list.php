<?php
require_once("config/config.php");

$message = $_GET['msg'] ?? '';
$requestStatusMap = [];
$stmt = $pdo->prepare("SELECT book_id, status FROM borrow_requests WHERE requester_email=? ORDER BY request_on DESC");
$stmt->execute([$_SESSION['userid']]);
while ($row_request_status = $stmt->fetch()) {
    $bookId = (int) $row_request_status['book_id'];
    if (!isset($requestStatusMap[$bookId])) {
        $requestStatusMap[$bookId] = $row_request_status['status'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="book-style.css?v=20260331b">
    <link rel="stylesheet" href="styles.css?v=20260331b">
    <title>E-Library</title>
</head>
<body>
    <header>
        <div class="container">
            <h1>E-Library</h1>
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
    <?php if($message !== '') { ?>
        <div class="container status-wrap">
            <div class="status-message"><?=htmlspecialchars($message)?></div>
        </div>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                alert(<?=json_encode($message)?>);
            });
        </script>
    <?php } ?>

    <div class="container" id="book-container">
    	<?php
        $searchQuery = $_POST['searchQuery'] ?? '';
        if ($searchQuery !== '') {
            $sql = "SELECT b.*, u.subscription_tier FROM book_master b LEFT JOIN user_master u ON b.email_id = u.email_id WHERE b.book_title ILIKE ? OR b.book_author ILIKE ? OR b.book_location ILIKE ? ORDER BY (u.subscription_tier = 'PREMIUM') DESC, b.create_on DESC";
            $stmt_bk = $pdo->prepare($sql);
            $stmt_bk->execute(["%$searchQuery%", "%$searchQuery%", "%$searchQuery%"]);
        } else {
            $sql = "SELECT b.*, u.subscription_tier FROM book_master b LEFT JOIN user_master u ON b.email_id = u.email_id ORDER BY (u.subscription_tier = 'PREMIUM') DESC, b.create_on DESC";
            $stmt_bk = $pdo->query($sql);
        }
        $res_bk = $stmt_bk->fetchAll();
        if (count($res_bk) > 0) {
			foreach($res_bk as $row_bk){
                $coverImage = (!empty($row_bk['book_cover_image']) && file_exists(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $row_bk['book_cover_image']))) ? $row_bk['book_cover_image'] : 'cover_img/default-cover.svg';
                $requestStatus = $requestStatusMap[(int) $row_bk['id']] ?? '';
		?>
    	<div class="book-card">
        	<img src="<?=htmlspecialchars($coverImage)?>" alt="Book Cover" onerror="this.src='cover_img/default-cover.svg'">
            <h2><?=htmlspecialchars($row_bk['book_title'])?></h2>
            <p><strong>Author:</strong> <?=htmlspecialchars($row_bk['book_author'])?><?php if (isset($row_bk['subscription_tier']) && $row_bk['subscription_tier'] === 'PREMIUM') echo ' <span class="pro-badge">PRO</span>'; ?></p>
            <p><strong>Location:</strong> <?=htmlspecialchars($row_bk['book_location'] ?: 'Not shared')?></p>
            <p><strong>Added:</strong> <?=htmlspecialchars($row_bk['create_on'])?></p>
            <?php if ($row_bk['email_id'] === $_SESSION['userid']) { ?>
                <button class="borrow-btn" type="button" disabled>Your Book</button>
            <?php } elseif ($requestStatus === 'Pending') { ?>
                <button class="borrow-btn request-sent-btn" type="button" disabled>Request Sent</button>
            <?php } elseif ($requestStatus === 'Approved') { ?>
                <button class="borrow-btn approved-btn" type="button" disabled>Request Approved</button>
            <?php } else { ?>
                <form method="post" action="borrow_request.php" class="inline-request-form">
                    <input type="hidden" name="book_id" value="<?=intval($row_bk['id'])?>">
                    <input type="hidden" name="return_to" value="book_list.php">
                    <button class="borrow-btn" type="submit" name="request_book" value="1">Request Borrow</button>
                </form>
            <?php } ?>
        </div>
        <?php }
        } else { ?>
            <div class="empty-state">
                <p>No books matched your search yet.</p>
            </div>
        <?php } ?>
    </div>
    <!--<script src="book-js.js "></script>-->
</body>
</html>