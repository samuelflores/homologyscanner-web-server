<?php
include_once('includes/connection.inc.php');

if(filter_var($_POST['regData']['email'], FILTER_VALIDATE_EMAIL)){
	$email = $_POST['regData']['email'];
}else {
	echo "Email is not valid " . $email;
}

if(filter_var($_POST['regData']['fname'], FILTER_SANITIZE_STRING)){
	$fname = $_POST['regData']['fname'];
}else {
	echo "First name input not allowed.";
}

if(filter_var($_POST['regData']['lname'], FILTER_SANITIZE_STRING)){
	$lname = $_POST['regData']['lname'];
}else {
	echo "Last name input not allowed.";
}

$passw = md5($_POST['regData']['passw']);

if(filter_var($_POST['regData']['use'], FILTER_VALIDATE_INT)){
	$use = $_POST['regData']['use'];
}else {
	echo "Use input not allowed.";
}

$query = "INSERT INTO user(`email`, `first_name`, `surname`, `password`, `use`) VALUES (?, ?, ?, ?, ?)";
if($instmt = $conn->prepare($query)) {
	$instmt->bind_param("ssssi", $email, $fname, $lname, $passw, $use);
	echo "Accepted";
}else{
	echo "Denied";
	/*echo "Error " . mysqli_error($conn);*/
}

$instmt->execute();

/*echo "New records created successfully.";*/

$instmt->close();
$conn->close();
?>
