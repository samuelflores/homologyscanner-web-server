<?php
include_once('includes/connection.inc.php');

if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	$email = $_POST['email'];
}
else {
	echo "Email is not valid";
}

$passw = md5($POST['passwd']);

$query = "SELECT email FROM reg_user WHERE email = ? AND password = ?";
$loginstmt = $conn->prepare($query);
$loginstmt->bind_param('ss', $email, $passw);
$loginstmt->execute();
$loginstmt->store_result();

$numRows = $loginstmt->num_rows;

if($numRows == 1) {
	echo "true";
}
else {
	echo "false";
}

$loginstmt->close();
?>
