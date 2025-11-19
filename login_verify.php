<?php
$today = date("Y-m-j");
//////// DB connection
require_once("config/dbconnect.php");
$post_userid = mysqli_real_escape_string($link1,$_POST['email']);
$post_pwd = mysqli_real_escape_string($link1,$_POST['password']);
//////
$query_aut = "SELECT * FROM user_master WHERE email_id LIKE '".$post_userid."' AND status LIKE '1'";
$result_aut = mysqli_query($link1,$query_aut) or die(mysqli_error($link1));
$arr_res_aut=mysqli_fetch_assoc($result_aut);
if($arr_res_aut['password']==$post_pwd){
	session_start();
	$_SESSION['uname']=$arr_res_aut['user_name'];
	$_SESSION['userid']=$arr_res_aut['email_id'];
	$_SESSION['uname']=$arr_res_aut['name'];
	$_SESSION['utype']=$arr_res_aut['user_type'];
    ///// insert login details
	$sql_ins="insert into login_data set userid='".$post_userid."',ip='".$_SERVER['REMOTE_ADDR']."'";
    mysqli_query($link1,$sql_ins);
	header("Location:my_account.php");
	exit;
}
else{
  header("Location:error.php");
  exit;
}
?>