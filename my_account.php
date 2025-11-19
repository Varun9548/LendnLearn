<?php
//////// DB connection
require_once("config/config.php");
///// get account details
$res_acc = mysqli_query($link1,"SELECT * FROM user_master WHERE email_id='".$_SESSION['userid']."'");
$row_acc = mysqli_fetch_assoc($res_acc);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - E-Library</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="container">
            <h1>E-Library</h1>
            <nav>
                <ul>
                    <li><a href="index2.html">Home</a></li>
                    <li><a href="upload.php">Upload Book</a></li>
                    <li><a href="search.php">Search Books</a></li>
                    <li><a href="my_account.php">My Account</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- My Account Section -->
    <section class="my-account">
        <div class="container">
            <h2>My Account</h2>
            <div class="account-details">
                <h3>Account Information</h3>
                <p><strong>Username:</strong> <?=$row_acc['user_name']?></p>
                <p><strong>Email:</strong> <?=$row_acc['email_id']?></p>
                <!--<p><strong>Location (City):</strong> <span id="cityDisplay"></span></p>-->
                <p><strong>Member Since:</strong> <?=$row_acc['create_on']?></p>

                <h3>My Uploaded Books</h3>
                <ul id="uploadedBooks">
                    <!-- List of uploaded books will be generated here -->
                </ul>
            </div>

            <div class="account-actions">
                <button onClick="editAccount()">Edit Account</button>
                <button onClick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            <p>&copy; 2024 E-Library. All Rights Reserved.</p>
        </div>
    </footer>

    <!--<script src="my_account.js"></script>-->
</body>
</html>
