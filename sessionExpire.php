<?php
session_start();
session_destroy();
$msg = 'Session Expired';
header("Location:login.php?msg=".$msg);
?>