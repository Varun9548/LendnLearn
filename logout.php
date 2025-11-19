<?php
session_start();
session_destroy();
$msg='You have successfully logout';
header("Location:login.php?msg=".$msg);
?>