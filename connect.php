<?php
//include (__DIR__ . '/../contents/types.php');


/* the below seems redundant, also done in ../contents/dtypes.php */ 
include ( 'dtypes.php');

//$host="localhost";
//$username="mmbcgi";
//$password="mMBc9IU5@r";
//$database="lims_project";
$host=webhost;       
$username=webusername  ;
$password=webpassword;    
$database=limsdatabase;     

$link = mysqli_connect($host, $username, $password, $database)
or die("Error " . mysqli_error($link));

$query = "SELECT email FROM user" or die("Error " . mysqli_error($link));

$result = mysqli_query($link, $query);

while($row = mysqli_fetch_array($result)) {
	echo $row["email"] . "<br>";
}
?>

