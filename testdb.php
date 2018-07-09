<?php
include ('utils.php');
define('flag', TRUE);
include (__DIR__ . '/../contents/dtypes.php');
include('db.php');
include('fs.php');




$db = new Database(key9, key10, key11, key12);
$db->connect();

$res = $db->getJobNames(1);
//print_r($res);

echo json_encode($res) . '<br>';

$job = $res[0]['jobName'];
/*echo "For Project: " . $job . "<br>";

$res = $db->getProjectDir($job);
echo "Path: ";
print_r($res);
echo '<br>';
*/
/*
$res = $db->getMutInfo($job);
echo "Mutation JSON Info: ";
print_r($res);
echo '<br>';
*/
$res = $db->getJobInfo($job);
echo "JobInfo JSON: ";
//print_r($res);
echo json_encode($res) . '<br>';


$res = $db->getEvalList("thanos@gmail.com");
echo "Evaluation List json: ";
//print_r($res);
echo json_encode($res) . '<br>';


$res = $db->getPurp();
echo "Purposes json: ";
//print_r($res);
echo json_encode($res) . '<br>';

$res = $db->userInfo("abc@d.ef");
echo "UserInfo JSON: ";
//print_r($res);
echo json_encode($res) . '<br>';


/*$res = $db->evaluation("abc@d.ef", "a-special-jobname", "a-special-mutation", 2, "This is a special message from me for this mutation.");
echo "Eval: ";
//print_r($res);
echo $res . '<br>';

$res = $db->getEval("abc@d.ef", "a-special-jobname", "a-special-mutation");
echo "getEval: ";
//print_r($res);
echo json_encode($res) . '<br>';
*/

/* TEST getProject ****
const LOCAL_FPATH = __DIR__ . "\\temp\\";

//$db = new Database(key9, key10, key11, key12);
//$db->connect();

//$remotePath = $db->getProjectDir($job);
$remotePath = '/pica/h1/fredrw/private/project/';
echo $remotePath . "<br>";
//$db->disconnect();

if(!$remotePath){
	return false;
}

$remotePath .= 'A-37-M' . '/last.2.pdb';
echo $remotePath . '<br>';

$localFile = LOCAL_FPATH . 'session_unq_id_001';
echo $localFile . "<br>";

if(!file_exists($localFile) || !is_dir($localFile)){
	echo "Folder not exists. Create:<br>";
	if(!mkdir($localFile)){
		return false;
	}
	echo "Folder \"" . $localFile . "\" Successfully created. <br>";
}
clearstatcache();

$localFile .= '\\last.2.pdb';
echo $localFile . "<br>";

if(file_exists($localFile) && !is_dir($localFile)){
	echo "File exists. Delete:<br>";
	if(!Utils::deleteTempPdbFile('session_unq_id_001' . '\\last.2.pdb')){
		return false;
	}
	echo "File \"" . $localFile . "\" Successfully Deleted.<br>";
}
clearstatcache();

$fs = new FileServer(key5, key8, key6);
$fs->ssh_connection();

$aResult['result'] = $fs->scp_bring($remotePath, $localFile);
$fs->disconnect();

echo $aResult['result'] . "<br>";

/*
$res = $db->getMutString($job);
echo "Mutation String: ";
print_r($res);
echo '<br>';

$res = $db->getWTString($job);
echo "WildType String: ";
print_r($res);
echo '<br>';

$res = $db->getProjectDir($job);
echo "Project Directory: ";
print_r($res);
echo '<br>';

$res = $db->getStatus($job);
echo "Status: ";
print_r($res);
echo '<br>';

$res = $db->getDDG($job, $db->getMutString($job)[0]);
echo "DDG: ";
print_r($res);
echo '<br>';
*/

//Utils::deleteTempPDBFile();
/*
$res = $db->userLogin("abc@d.ef", '123');
echo "Login: ";
print_r($res);
echo '<br>';
*/
/*
$res = $db->userReg('opa@lak.ia', '123', null, null,  1);
echo "Reg: ";
print_r($res);
echo '<br>';
*/
//$db->tableExists('reg_user');
/*$db->select("reg_user", "fname, passwd");
//$db->insert("reg_user", array("b@a.f", "poko", "paki", "dumdum", 2), "`email`, `fname`, `lname`, `passwd`, `use`");
//$db->update("reg_user", array('passwd'=>'gumgum'), array("fname='poko'"));
$res = $db->getResult();
print_r($res);
*/

$db->disconnect();
?>

