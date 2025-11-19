<?php
//////// DB connection
require_once("config/config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="booK-style.css">
    <link rel="stylesheet" href="styles.css">
    <title>E-Library</title>
</head>
<body>
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
	<!-- <div align="center"><?=$_REQUEST['msg']?></div> -->
    <div class="container" id="book-container">
    	<?php
        $searchQuery = $_POST['searchQuery'];
		$res_bk = mysqli_query($link1,"SELECT * FROM book_master WHERE book_title LIKE '%".$searchQuery."%'");
		while($row_bk = mysqli_fetch_assoc($res_bk)){
		?>
    	<div class="book-card">
        	<img src="<?=$row_bk['book_cover_image']?>" alt="Book Cover">
            <h2><?=$row_bk['book_title']?></h2>
            <p><?=$row_bk['book_author']?></p>
            <p><?=$row_bk['email_id']?></p>
            <p><?=$row_bk['create_on']?></p>
            <button class="borrow-btn">Borrow</button>
        </div>
        <?php }?>
    </div>
    <!--<script src="book-js.js "></script>-->
</body>
</html>