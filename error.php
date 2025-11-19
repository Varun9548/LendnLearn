<?php
session_start();
session_destroy();
$msg = 'Error in login';
header("Location:login.php?msg=".$msg);
exit;
?>