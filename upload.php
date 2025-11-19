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
			$filename = stripslashes($_FILES['cover']['name']);
			$extension = getExtension($filename);	
			$extension = strtolower($extension);
			$now=date("His");
			$today_tm=date("Ym");
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") ){
				$error_msg = "Invalid file extension";
				$errors=1;
			}
			else{
				$file = $_FILES['cover'];
				$file_name = $file['name'];
				$file_type = $file['type'];
				$file_size = $file['size'];
				$file_path = $file['tmp_name'];
				$temp = explode(".", $file_name);   
				///add date into image name before extension
				$fileName = $temp[0].'_'.$now.'.'.$temp[1];          
				if (!is_dir("cover_img/".$today_tm."/" )) {
					mkdir("cover_img/".$today_tm."/", 0755, 'R');              
				}              
				$filename = "cover_img/".$today_tm."/".$fileName;
				$isUpload =	move_uploaded_file($file_path, $filename);
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
			$res_inst = mysqli_query($link1,"INSERT INTO book_master SET ref_no='".$ref_no."', email_id ='".$_SESSION['userid']."', book_title='".$_POST['title']."', book_author='".$_POST['author']."', book_description='".$_POST['description']."', book_genre='".$_POST['genre']."', book_cover_image='".$filename."', status='1', create_by='".$_SESSION['userid']."', create_on='".date("Y-m-d H:i:s")."'");
			///// check query execution
			if (!$res_inst) {
				 $msg = "Error details1: " . mysqli_error($link1) . ".";
			}else{
				$msg = "Book is successfully uploaded";
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
