<?php
include (__DIR__ . '/../contents/types.php');


/* the below seems redundant, also done in ../contents/dtypes.php */ 

$host="localhost";
$username="mmbcgi";
$password="mMB¢9IUser";
$database="lims_project";

$link = mysqli_connect($host, $username, $password, $database)
or die("Error " . mysqli_error($link));

$query = "SELECT email FROM user" or die("Error " . mysqli_error($link));

$result = mysqli_query($link, $query);

while($row = mysqli_fetch_array($result)) {
	echo $row["email"] . "<br>";
}
?>

