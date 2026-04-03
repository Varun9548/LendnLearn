<?php
$today = date("Y-m-j");
require_once("config/dbconnect.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location:login.php?msg=" . urlencode("Please login first"));
    exit;
}

$login_type = trim($_POST['login_type'] ?? 'user');
$failure_page = ($login_type === 'admin') ? 'admin_login.php' : 'login.php';

$post_userid = trim($_POST['email'] ?? '');
$post_pwd = trim($_POST['password'] ?? '');

if ($post_userid === '' || $post_pwd === '') {
    header("Location:".$failure_page."?msg=" . urlencode("Please enter email and password"));
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM user_master WHERE (LOWER(email_id) = LOWER(?) OR LOWER(user_name) = LOWER(?)) AND status = 1 LIMIT 1");
$stmt->execute([$post_userid, $post_userid]);
$arr_res_aut = $stmt->fetch();

if ($arr_res_aut && trim($arr_res_aut['password']) === $post_pwd) {
    $isAdmin = strtoupper(trim($arr_res_aut['user_type'])) === 'ADMIN';

    if ($login_type === 'admin' && !$isAdmin) {
        header("Location:admin_login.php?msg=" . urlencode("Please use an admin account"));
        exit;
    }

	session_start();
	$_SESSION['uname'] = $arr_res_aut['user_name'];
	$_SESSION['userid'] = $arr_res_aut['email_id'];
	$_SESSION['utype'] = $arr_res_aut['user_type'];
	
    $stmt_ins = $pdo->prepare("INSERT INTO login_data (userid, ip) VALUES (?, ?)");
    $stmt_ins->execute([$arr_res_aut['email_id'], $_SERVER['REMOTE_ADDR']]);

    if ($isAdmin) {
        header("Location:admin_dashboard.php");
        exit;
    }

	header("Location:home.php");
	exit;
}

header("Location:".$failure_page."?msg=" . urlencode("Invalid email/username or password"));
exit;
?>