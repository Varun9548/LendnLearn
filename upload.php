<?php
//////// DB connection
require_once("config/config.php");
////// get file extension
function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; } 
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}
///// save book data
////// if we hit process button
if($_POST){
	if($_POST['upd']){
		$errors=0;
		$filename = "";
		///// if attachment is there
		if($_FILES["cover"]["name"]){
			$file = $_FILES['cover'];
			$file_name = stripslashes($file['name']);
			$extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			$now = date("His");
			$today_tm = date("Ym");
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png")) {
				$error_msg = "Invalid file extension";
				$errors=1;
			}
			else{
				$file_path = $file['tmp_name'];
				$baseName = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($file_name, PATHINFO_FILENAME));
				if ($baseName === '') {
					$baseName = 'book_cover';
				}
				$fileName = $baseName.'_'.$now.'.'.$extension;
				$relativeDir = "cover_img/".$today_tm."/";
				$absoluteDir = __DIR__ . DIRECTORY_SEPARATOR . 'cover_img' . DIRECTORY_SEPARATOR . $today_tm;
				if (!is_dir($absoluteDir)) {
					mkdir($absoluteDir, 0755, true);
				}
				$filename = $relativeDir.$fileName;
				$absolutePath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filename);
				$isUpload = move_uploaded_file($file_path, $absolutePath);
				if($isUpload == true){
					$errors=2;
				}else{
					$errors=1;
					$error_msg = "Upload Error";
				}
			}
		}else{
			$errors=2;
		}
   		if($errors==2){
			/////insert book data
			$ref_no = "EB/".date("y")."/".$now;
            try {
                $stmt = $pdo->prepare("INSERT INTO book_master (ref_no, email_id, book_title, book_author, book_description, book_genre, book_location, book_cover_image, status, create_by, create_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)");
                $stmt->execute([
                    $ref_no, $_SESSION['userid'], $_POST['title'], $_POST['author'], $_POST['description'], $_POST['genre'], $_POST['location'], $filename, $_SESSION['userid'], date("Y-m-d H:i:s")
                ]);
                $msg = "Book is successfully uploaded";
            } catch (PDOException $e) {
                $msg = "Error details1: " . $e->getMessage() . ".";
            }
		}else{
			$msg = "Attachment is not processed properly.".$error_msg;
		}
		///// move to parent page
		header("location:book_list.php?msg=".$msg);
		exit;	
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Book - E-Library</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="styles.css?v=20260331b">
</head>
<body>
    <!-- Header Section -->
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

    <!-- Upload Book Section -->
    <section class="upload-book">
        <div class="container">
            <h2>Upload a New Book</h2>
            <form id="uploadForm" method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Book Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" required></textarea>
                </div>

                <div class="form-group">
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre" required>
                        <option value="" disabled selected>Select a genre</option>
                        <option value="fiction">Fiction</option>
                        <option value="non-fiction">Non-fiction</option>
                        <option value="mystery">Mystery</option>
                        <option value="sci-fi">Sci-Fi</option>
                        <option value="fantasy">Fantasy</option>
                        <option value="romance">Romance</option>
                        <option value="thriller">Thriller</option>
                        <option value="biography">Biography</option>
                        <option value="history">History</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location">Book Location</label>
                    <input type="text" id="location" name="location" placeholder="Area / City where the book is available" required>
                </div>

                <div class="form-group">
                    <label for="cover">Book Cover</label>
                    <input type="file" id="cover" name="cover" accept="image/*" required>
                </div>

                <div class="form-group">
                    <button type="submit" name="upd" value="save">Upload Book</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            <p>&copy; 2024 E-Library. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Link to the external JavaScript file -->
    <!--<script src="upload.js"></script>-->
</body>
</html>
