<?php
ob_start();
session_start();
require_once("config/dbconnect.php");

if (empty($_SESSION['userid']) || empty($_SESSION['utype']) || strtoupper($_SESSION['utype']) !== 'ADMIN') {
    header("Location: admin_login.php?msg=" . urlencode("Please login with the separate admin account"));
    exit;
}

$message = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_book'])) {
        $bookId = intval($_POST['book_id'] ?? 0);
        if ($bookId > 0) {
            try {
                $stmtDel = $pdo->prepare("DELETE FROM book_master WHERE id=?");
                $stmtDel->execute([$bookId]);
                $deleted = $stmtDel->rowCount();
                $msg = $deleted > 0 ? 'Book deleted successfully' : 'Book not found or already deleted';
            } catch (PDOException $e) {
                $msg = 'Delete error: ' . $e->getMessage();
            }
        } else {
            $msg = 'Invalid book ID received: ' . ($_POST['book_id'] ?? 'none');
        }
        // JS redirect avoids any headers-already-sent issue
        echo '<script>window.location="admin_dashboard.php?msg=' . urlencode($msg) . '";</script>';
        exit;
    }

    if (isset($_POST['toggle_user'])) {
        $userEmail = trim($_POST['user_email'] ?? '');

        if ($userEmail === $_SESSION['userid']) {
            header("Location: admin_dashboard.php?msg=" . urlencode("You cannot change your own admin status"));
            exit;
        }

        if ($userEmail !== '') {
            $stmt = $pdo->prepare("UPDATE user_master SET status = CASE WHEN status = 1 THEN 0 ELSE 1 END, update_by=? WHERE email_id=?");
            $stmt->execute([$_SESSION['userid'], $userEmail]);
            header("Location: admin_dashboard.php?msg=" . urlencode("User status updated"));
            exit;
        }
    }
}

$row_total_users = $pdo->query("SELECT COUNT(*) AS total_users FROM user_master WHERE status=1")->fetch();
$row_total_books = $pdo->query("SELECT COUNT(*) AS total_books FROM book_master WHERE status=1")->fetch();
$row_total_logins = $pdo->query("SELECT COUNT(*) AS total_logins FROM login_data")->fetch();
$row_total_admins = $pdo->query("SELECT COUNT(*) AS total_admins FROM user_master WHERE UPPER(user_type)='ADMIN' AND status=1")->fetch();
$row_total_requests = $pdo->query("SELECT COUNT(*) AS total_requests FROM borrow_requests")->fetch();

$res_recent_books = $pdo->query("SELECT id, book_title, book_author, email_id, book_genre, book_location, create_on FROM book_master ORDER BY create_on DESC LIMIT 15")->fetchAll();
$res_recent_logins = $pdo->query("SELECT userid, ip, log_date AS login_on FROM login_data ORDER BY log_date DESC LIMIT 10")->fetchAll();
$res_users = $pdo->query("SELECT user_name, email_id, user_type, status, create_on FROM user_master ORDER BY create_on DESC LIMIT 15")->fetchAll();
$res_top_uploaders = $pdo->query("SELECT email_id, COUNT(*) AS total_books FROM book_master GROUP BY email_id ORDER BY total_books DESC, email_id ASC LIMIT 5")->fetchAll();
$res_recent_requests = $pdo->query("SELECT r.status, r.request_on, b.book_title, u.user_name AS requester_name, o.user_name AS owner_name FROM borrow_requests r LEFT JOIN book_master b ON b.id = r.book_id LEFT JOIN user_master u ON u.email_id = r.requester_email LEFT JOIN user_master o ON o.email_id = r.owner_email ORDER BY r.request_on DESC LIMIT 8")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LendnLearn</title>
    <link rel="stylesheet" href="styles.css?v=20260331b">
</head>
<body>
    <header>
        <div class="container">
            <h1>LendnLearn Admin</h1>
            <nav>
                <ul>
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="#books">Books</a></li>
                    <li><a href="#users">Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="my-account admin-shell">
        <div class="container page-stack">
            <h2>Admin Dashboard</h2>
            <div class="section-heading">
                <p>Separate admin control panel for platform activity, moderation, and quick actions.</p>
            </div>

            <?php if ($message !== '') { ?>
                <div class="status-message"><?=htmlspecialchars($message)?></div>
            <?php } ?>

            <div class="stats-grid admin-stats-grid">
                <div class="stat-card">
                    <span><?=intval($row_total_users['total_users'] ?? 0)?></span>
                    <p>Active users</p>
                </div>
                <div class="stat-card">
                    <span><?=intval($row_total_books['total_books'] ?? 0)?></span>
                    <p>Live books</p>
                </div>
                <div class="stat-card">
                    <span><?=intval($row_total_logins['total_logins'] ?? 0)?></span>
                    <p>Total logins</p>
                </div>
                <div class="stat-card">
                    <span><?=intval($row_total_admins['total_admins'] ?? 0)?></span>
                    <p>Admin accounts</p>
                </div>
                <div class="stat-card">
                    <span><?=intval($row_total_requests['total_requests'] ?? 0)?></span>
                    <p>Borrow requests</p>
                </div>
            </div>

            <div class="admin-grid">
                <div class="account-details account-card">
                    <h3>Quick Insights</h3>
                    <ul class="account-book-list insight-list">
                        <li><strong>Health Check:</strong> Site is live and accepting uploads.</li>
                        <li><strong>Moderation:</strong> Remove books instantly from the books table below.</li>
                        <li><strong>User Control:</strong> Activate or pause accounts from the users table.</li>
                    </ul>
                </div>

                <div class="account-details account-card">
                    <h3>Top Uploaders</h3>
                    <ul class="account-book-list">
                        <?php if (count($res_top_uploaders) > 0) { ?>
                            <?php foreach ($res_top_uploaders as $uploader) { ?>
                                <li>
                                    <strong><?=htmlspecialchars($uploader['email_id'])?></strong><br>
                                    <span><?=intval($uploader['total_books'])?> uploaded book(s)</span>
                                </li>
                            <?php } ?>
                        <?php } else { ?>
                            <li>No upload activity yet.</li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="admin-grid">
                <div class="account-details account-card">
                    <h3>Recent Logins</h3>
                    <ul class="account-book-list">
                        <?php if (count($res_recent_logins) > 0) { ?>
                            <?php foreach ($res_recent_logins as $login) { ?>
                                <li>
                                    <strong><?=htmlspecialchars($login['userid'])?></strong><br>
                                    <span>IP: <?=htmlspecialchars($login['ip'])?> · <?=htmlspecialchars($login['login_on'])?></span>
                                </li>
                            <?php } ?>
                        <?php } else { ?>
                            <li>No login activity yet.</li>
                        <?php } ?>
                    </ul>
                </div>

                <div class="account-details account-card">
                    <h3>Recent Borrow Requests</h3>
                    <ul class="account-book-list insight-list">
                        <?php if (count($res_recent_requests) > 0) { ?>
                            <?php foreach ($res_recent_requests as $request) { ?>
                                <li>
                                    <strong><?=htmlspecialchars($request['book_title'] ?: 'Book request')?></strong><br>
                                    <span><?=htmlspecialchars($request['requester_name'] ?: 'Reader')?> → <?=htmlspecialchars($request['owner_name'] ?: 'Owner')?> · <?=htmlspecialchars($request['status'])?> · <?=htmlspecialchars($request['request_on'])?></span>
                                </li>
                            <?php } ?>
                        <?php } else { ?>
                            <li>No borrow requests yet.</li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div class="account-details account-card" id="books">
                <h3>Manage Uploaded Books</h3>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Owner</th>
                                <th>Genre</th>
                                <th>Location</th>
                                <th>Uploaded</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($res_recent_books) > 0) { ?>
                                <?php foreach ($res_recent_books as $book) { ?>
                                    <tr>
                                        <td><?=htmlspecialchars($book['book_title'])?></td>
                                        <td><?=htmlspecialchars($book['book_author'])?></td>
                                        <td><?=htmlspecialchars($book['email_id'])?></td>
                                        <td><?=htmlspecialchars($book['book_genre'])?></td>
                                        <td><?=htmlspecialchars($book['book_location'] ?: 'Not shared')?></td>
                                        <td><?=htmlspecialchars($book['create_on'])?></td>
                                        <td>
                                            <form method="post" action="admin_dashboard.php" class="delete-form">
                                                <input type="hidden" name="book_id" value="<?=intval($book['id'])?>">  
                                                <button type="submit" name="delete_book" value="1" class="danger-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="7">No books found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="account-details account-card" id="users">
                <h3>Manage Users</h3>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($res_users) > 0) { ?>
                                <?php foreach ($res_users as $user) { ?>
                                    <tr>
                                        <td><?=htmlspecialchars($user['user_name'])?></td>
                                        <td><?=htmlspecialchars($user['email_id'])?></td>
                                        <td><span class="pill"><?=htmlspecialchars($user['user_type'])?></span></td>
                                        <td><?=intval($user['status']) === 1 ? 'Active' : 'Inactive'?></td>
                                        <td><?=htmlspecialchars($user['create_on'])?></td>
                                        <td>
                                            <?php if ($user['email_id'] === $_SESSION['userid']) { ?>
                                                <span class="muted-text">Current admin</span>
                                            <?php } else { ?>
                                                <form method="post" action="admin_dashboard.php">
                                                    <input type="hidden" name="user_email" value="<?=htmlspecialchars($user['email_id'])?>">
                                                    <button type="submit" name="toggle_user" value="1" class="warning-btn">
                                                        <?=intval($user['status']) === 1 ? 'Disable' : 'Enable'?>
                                                    </button>
                                                </form>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="6">No users found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
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
