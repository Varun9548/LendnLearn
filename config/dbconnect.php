<?php 
$db_user = 'root';
$db_pass = '';
$db_host = 'localhost';
$db = "book_library_db";
$link1 = mysqli_connect($db_host, $db_user, $db_pass,$db) or die("Unable to connect to MySQL");
/*			##############################	TIME Diffrence US to INDIA		####################*/
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
/*			##############################	TIME Diffrence US to INDIA		####################*/
?>