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

$stmt_acc = $pdo->prepare("SELECT * FROM user_master WHERE email_id=? LIMIT 1");
$stmt_acc->execute([$_SESSION['userid']]);
$row_acc = $stmt_acc->fetch();

$stmt_recent = $pdo->query("SELECT * FROM book_master WHERE status=1 ORDER BY create_on DESC LIMIT 8");
$res_recent = $stmt_recent->fetchAll();

$stmt_total = $pdo->query("SELECT COUNT(*) AS total_books FROM book_master WHERE status=1");
$row_total_books = $stmt_total->fetch() ?: ['total_books' => 0];

$stmt_my_books = $pdo->prepare("SELECT COUNT(*) AS my_books FROM book_master WHERE email_id=?");
$stmt_my_books->execute([$_SESSION['userid']]);
$row_my_books = $stmt_my_books->fetch() ?: ['my_books' => 0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - LendnLearn</title>
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

    <?php if ($message !== '') { ?>
        <div class="container status-wrap">
            <div class="status-message"><?=htmlspecialchars($message)?></div>
        </div>
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                alert(<?=json_encode($message)?>);
            });
        </script>
    <?php } ?>

    <section class="hero logged-in-hero">
        <div class="container hero-panel">
            <div>
                <p class="eyebrow">Welcome back</p>
                <h2>Hello, <?=htmlspecialchars($row_acc['user_name'] ?? 'Reader')?></h2>
                <p>Browse recent books, upload your own collection, and manage everything from one place.</p>
                <div class="page-actions">
                    <a class="primary-btn hero-action-btn" href="search.php">📚 Browse Books</a>
                    <a class="secondary-btn hero-action-btn" href="upload.php">⬆ Upload a Book</a>
                </div>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <span><?=intval($row_total_books['total_books'] ?? 0)?></span>
                    <p>Books available</p>
                </div>
                <div class="stat-card">
                    <span><?=intval($row_my_books['my_books'] ?? 0)?></span>
                    <p>Your uploads</p>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-books">
        <div class="container section-shell">
            <div class="section-heading">
                <h2>Recently Added Books</h2>
                <p>These books are shown in a clean row layout for logged-in users.</p>
            </div>

            <div class="books-grid modern-grid">
                <?php if (count($res_recent) > 0) { ?>
                    <?php foreach($res_recent as $row_bk) { ?>
                        <?php
                            $coverImage = (!empty($row_bk['book_cover_image']) && file_exists(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $row_bk['book_cover_image']))) ? $row_bk['book_cover_image'] : 'cover_img/default-cover.svg';
                            $requestStatus = $requestStatusMap[(int) $row_bk['id']] ?? '';
                        ?>
                        <div class="book-item modern-card">
                            <img src="<?=htmlspecialchars($coverImage)?>" alt="<?=htmlspecialchars($row_bk['book_title'])?>" onerror="this.src='cover_img/default-cover.svg'">
                            <h3><?=htmlspecialchars($row_bk['book_title'])?></h3>
                            <p><strong>Author:</strong> <?=htmlspecialchars($row_bk['book_author'])?></p>
                            <p><strong>Genre:</strong> <?=htmlspecialchars($row_bk['book_genre'])?></p>
                            <p><strong>Location:</strong> <?=htmlspecialchars($row_bk['book_location'] ?: 'Not shared')?></p>
                            <?php if ($row_bk['email_id'] === $_SESSION['userid']) { ?>
                                <button class="borrow-btn" type="button" disabled>Your Book</button>
                            <?php } elseif ($requestStatus === 'Pending') { ?>
                                <button class="borrow-btn request-sent-btn" type="button" disabled>Request Sent</button>
                            <?php } elseif ($requestStatus === 'Approved') { ?>
                                <button class="borrow-btn approved-btn" type="button" disabled>Request Approved</button>
                            <?php } else { ?>
                                <form method="post" action="borrow_request.php" class="inline-request-form">
                                    <input type="hidden" name="book_id" value="<?=intval($row_bk['id'])?>">
                                    <input type="hidden" name="return_to" value="home.php">
                                    <button class="borrow-btn" type="submit" name="request_book" value="1">Request Borrow</button>
                                </form>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="empty-state">
                        <p>No books have been uploaded yet. Be the first to share one.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 LendnLearn. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
