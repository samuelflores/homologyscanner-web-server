<?php
set_include_path(__DIR__ . '/../contents/phpseclib0.3.10/');
define('flag', TRUE);
include (__DIR__ . '/../contents/dtypes.php');
include('db.php');
require_once('Crypt/RSA.php');
require_once('Net/SSH2.php');
require_once('Net/SCP.php');
	
	
$db = new Database(key9, key10, key11, key12);
$db->connect();
$db->select("results", "batch_directory", "jobName='lims-project-test-non-real-data-no-1'");

$res = $db->getResult();
//$remotef = $res['batch_directory'] . '/last.2.pdb';
$remotef = '/pica/h1/fredrw/private/project/A-37-M/last.2.pdb';
//echo $remotef . "<br>";
$localf = __DIR__ . '/temp/last.2.pdb';
//echo $localf . "<br>";


$ssh_conn = new Net_SSH2(key5);
if(!$ssh_conn->login(key8, key6)){
	echo "SSH: Fail to login.<br>";
}

$scp = new Net_SCP($ssh_conn);
if(!$scp->get($remotef, $localf)){
	echo "SCP: Fail retrieving file.<br>";
}
else {
	echo "Successful File Transfer.<br>Path: " . $localf . "<br>";
}

if($ssh_conn->isConnected()){
	$ssh_conn->disconnect();
}

$db->disconnect();




/*
$ssh_conn = ssh2_connect(key5, key7);
if(!$ssh_conn){
	echo "Can't connect SSH.<br>";
}
if(!ssh2_auth_password($ssh_conn, key8, key6)){
	echo "SSH Connection Failure.<br>";
}

$filetr = ssh2_scp_recv($ssh_conn, $remotef, $localf);

if(!$filetr){
	echo "Error transfering file.<br>";
}

$ssh_conn->disconnect();
*/
?>
