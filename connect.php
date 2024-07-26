<?php
//include (__DIR__ . '/../contents/types.php');
//include ('generalConfig.php'); // This is web accessible so includes safe config parameters. In particular it defines coderootdirectory, which containts dtypes.php, which in turn contains less safe config info.
//include ($coderootdirectory.'/dtypes.php');

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

