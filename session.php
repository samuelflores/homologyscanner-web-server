<?php
if(!isset($_SESSION['ZEMuser'])){
	session_start();
}

$user_id = session_id();

if(isset($_POST['email'])){
	$user_id = session_regenerate_id();
	$_SESSION['ZEMuser']=$_POST['email'];
	$_SESSION['guest'] = "false"; //might use it later
}
if(!isset($_SESSION['ZEMuser'])){
	$_SESSION['ZEMuser'] = 'Guest';
	$_SESSION['guest'] = "true"; //might use it later
}
?>
