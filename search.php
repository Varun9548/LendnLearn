<?php
//////// DB connection
require_once("config/config.php");
///// get account details
$stmt = $pdo->prepare("SELECT * FROM user_master WHERE email_id=?");
$stmt->execute([$_SESSION['userid']]);
$row_acc = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books - LendnLearn</title>
    <link rel="stylesheet" href="styles.css?v=20260331b">
</head>
<body>
    <!-- Header Section -->
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

    <!-- Search Books Section -->
    <section class="search-books">
        <div class="container">
            <h2>Search for a Book</h2>
            <form id="searchForm" action="book_list.php" method="POST">
                <div class="form-group">
                    <label for="searchQuery">Enter Book Title, Author, or Location</label>
                    <input type="text" id="searchQuery" name="searchQuery" placeholder="Search by title, author, or location..." required>
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
            <p>&copy; 2024 LendnLearn. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- <script src="search.js"></script> -->
</body>
</html>
