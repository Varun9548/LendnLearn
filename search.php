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
    <title>Search Books - E-Library</title>
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

    <!-- Search Books Section -->
    <section class="search-books">
        <div class="container">
            <h2>Search for a Book</h2>
            <form id="searchForm" action="book_list.php" method="POST">
                <div class="form-group">
                    <label for="searchQuery">Enter Book Title or Author</label>
                    <input type="text" id="searchQuery" name="searchQuery" placeholder="Search by title or author..." required>
                </div>

                <div class="form-group">
                    <button type="submit">Search</button>
                </div>
            </form>

            <div id="searchResults" class="search-results">
                <!-- Search results will be displayed here -->
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            <p>&copy; 2024 E-Library. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- <script src="search.js"></script> -->
</body>
</html>
