<?php
include_once('includes/connection.inc.php');

$query="SELECT * FROM purpose";
$result=mysqli_query($conn, $query);
if(!$result){
	echo ("Error " . mysqli_error($conn));
}

$purpose = array();
$purposes = array();

$i = 0;
while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
	//$purpose[$row['id']] = filter_var($row['type'], FILTER_SANITIZE_STRING);
	$purpose[$i]['purposeName'] = filter_var($row['type'], FILTER_SANITIZE_STRING);
	$purpose[$i]['purposeID'] = $row['id'];
	//$purposes[$i++] = $purpose;
	$i++;

}
echo json_encode($purpose);

mysqli_close($conn);
?>