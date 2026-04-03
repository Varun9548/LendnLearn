<?php
require_once("config/dbconnect.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: registration.html");
    exit;
}

$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$city = trim($_POST['city'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($email === '' || $username === '' || $password === '' || $city === '' || $phone === '') {
    header("Location: login.php?msg=" . urlencode("Please fill in all registration fields"));
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM user_master WHERE email_id = ? LIMIT 1");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    header("Location: login.php?msg=" . urlencode("User already exists. Please login."));
    exit;
}

$sql = "INSERT INTO user_master (email_id, user_name, password, user_type, status, create_by, create_on, update_by)
        VALUES (?, ?, ?, 'USER', 1, 'self', ?, 'self')";

$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([$email, $username, $password, date('Y-m-d H:i:s')]);
    header("Location: login.php?msg=" . urlencode("Registration successful. Please login."));
    exit;
} catch (PDOException $e) {
    header("Location: login.php?msg=" . urlencode("Registration failed."));
    exit;
}
?>