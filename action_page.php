<!DOCTYPE html>
<html>
<head>
	<title>homologyScanner Submit Page</title>
    <link rel="stylesheet" href="styles/kendo.common.min.css" />
    <link rel="stylesheet" href="styles/kendo.moonlightMod.css" />
</head>
<body>
<script type="text/javascript">
	history.pushState(null, null, '<?php echo $_SERVER["REQUEST_URI"]; ?>');
	window.addEventListener('popstate', function(event) {
		window.location.href("index.php");
	});
        error_log("check 40");
</script>
<!--
<p> POST data : </p>

<table>
<?php 

    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo ">$value<";
        echo "</td>";
        echo "</tr>";
        error_log("check 41 $key $value");
    }


?>
</table>
<p> GET  data : </p>
<table>
<?php 


    foreach ($_GET  as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";
        error_log("check 42");
    }


?>
</table>
-->
<?php
define('flag', TRUE); //dtypes.php will die if 'flag' is not defined.
include ( 'generalConfig.php');
//error_log("check 43 password should be set to mmbcgi user mysql password: >$password<");
error_log("check 44 webpassword should be set to mmbcgi user mysql password: >".webpassword."<");
//error_log("check 44");
//$myLibPath = "/usr/lib/x86_64-linux-gnu/blas:/home/sam/svn/breeder/build:/usr/local/lib";
$userMailAddress = $_POST["user_email"];
$myLibPathToPass = myLibPath;
error_log("check 44.1 >$myLibPathToPass<");
$commandString = "";
$commandString = "cd /data/runs/" . $_POST["jobName"] . "/" . $_POST["pdbId"] . " ; ";
# Went back to installed homologyScanner:
$commandString = "/usr/local/bin/homologyScanner    -FASTAEXECUTABLE /usr/local//fasta_lwp/fasta.pl -FASTATEMPDIRECTORY /usr/local//fasta_lwp///temp/ -BREEDEREXECUTABLE /usr/local/bin/breeder -BREEDERMAINDIRECTORY /home/sam/svn/breeder -DATABASE mmb -MMBEXECUTABLE /usr/local/bin/MMB -LASTSTAGE 1 -FOLDXSCRIPT /home/sam/svn/breeder/perl/run-foldx.3.pl -FOLDXEXECUTABLE //usr/local//foldx/foldx -SQLSERVER localhost -SQLEXECUTABLE /usr/bin/mysql -SQLPASSWORD ". webpassword ." -USER root -SQLUSER mmbcgi -JOBLIBRARYPATH ". myLibPath ." -REPORTINGINTERVAL 0.000001 -NUMREPORTINGINTERVALS 2 -FLEXIBILITYWINDOWOFFSET 2 -TEMPERATURE 298 -ID "
//$commandString = "/usr/local/bin/homologyScanner    -FASTAEXECUTABLE /usr/local//fasta_lwp/fasta.pl -FASTATEMPDIRECTORY /usr/local//fasta_lwp///temp/ -BREEDEREXECUTABLE /usr/local/bin/breeder -BREEDERMAINDIRECTORY /home/sam/svn/breeder -DATABASE mmb -MMBEXECUTABLE /usr/local/bin/MMB -LASTSTAGE 1 -FOLDXSCRIPT /home/sam/svn/breeder/perl/run-foldx.3.pl -FOLDXEXECUTABLE //usr/local//foldx/foldx -SQLSERVER localhost -SQLEXECUTABLE /usr/bin/mysql -SQLPASSWORD $webpassword -USER root -SQLUSER mmbcgi -JOBLIBRARYPATH $myLibPath -REPORTINGINTERVAL 0.000001 -NUMREPORTINGINTERVALS 2 -FLEXIBILITYWINDOWOFFSET 2 -TEMPERATURE 298 -ID "
. $_POST["jobName"]
. " -EMAILADDRESS "
. $userMailAddress  
. " -ONEMUTANT ";

$mutationString = "";
$i = 0;
$myChain = "chainId" . $i;
error_log("check 46: >$myChain<");
$myResidue = "residueNumber" . $i;
$mySubstituedResidueType = "substitutedResidueType" . $i;
while ($_POST[$myChain]) 
{
    error_log("check 46.5 ");
    if ($i > 0) { $mutationString  .= "."; }
    $mutationString .= $_POST[$myChain] . "-" . rtrim($_POST[$myResidue]) . "-" . $_POST[$mySubstituedResidueType];
    $i++;
    $myChain = "chainId" . $i;
    error_log("check 48: >$myChain<");
    error_log("check 49: >".$_POST[$myChain]."<");

    $myResidue = "residueNumber" . $i;
    $mySubstituedResidueType = "substitutedResidueType" . $i;
}
error_log("check 49.2 ");
$commandString .= $mutationString;
# switched back to explicit working directory, rather than "."
$commandString .= " -WORKINGDIRECTORY /data/runs/" . $_POST["jobName"] . "/" . $_POST["pdbId"];
# We are cd'ing to the working directory, so just use "."
#$commandString .= " -WORKINGDIRECTORY .     " ;#. $_POST["jobName"] . "/" . $_POST["pdbId"];
# We are now cd'ing into our working directory, so we just say "/work" . This because we are mounting to the docker container and it is simpler to just use the current directory rather than specify it explicitly. I tried "./", but this gives us problems when we cd into a directory and then try to give it a file name with the full path.
#$commandString .= " -WORKINGDIRECTORY /work " ;#. $_POST["jobName"] . "/" . $_POST["pdbId"];
$commandString .= "  -CHAINSINCOMPLEX ";

$complexStringForward = "";
$complexStringBackward = ",";

$i = 0;
$myComplex1Chain = "complex1ChainSelector" . $i;
error_log("check 49.3 : >$myComplex1Chain<");
while ($_POST[$myComplex1Chain]) {
    error_log("check 49.4 : >".$_POST[$myComplex1Chain]."<");
    $myComplex1Chain = "complex1ChainSelector" . $i;
    //$commandString .= $_POST[$myComplex1Chain];     
    $complexStringForward .= $_POST[$myComplex1Chain];     
    $complexStringBackward .= $_POST[$myComplex1Chain];     
    $i++;
    $myComplex1Chain = "complex1ChainSelector" . $i;
    error_log("check 49.6 : >$myComplex1Chain<");
}
error_log("check 50.1 : >$myComplex1Chain<");
error_log("check 50.2 : >$complexStringForward<"); // simply "B"
error_log("check 50.3 : >$complexStringBackward <"); // ",B"
    
$complexStringForward .= ",";
error_log("check 50.4 : >$complexStringForward<"); // simply "B,"
//$commandString .= ",";
$i = 0;
$temp = "";
$myComplex2Chain = "complex2ChainSelector" . $i;
while ($_POST[$myComplex2Chain]) {
    $myComplex2Chain = "complex2ChainSelector" . $i;
    //$commandString .= $_POST[$myComplex2Chain];
    $complexStringForward .= $_POST[$myComplex2Chain];
    $temp .= $_POST[$myComplex2Chain];
    $i++;
    $myComplex2Chain = "complex2ChainSelector" . $i;
}
error_log("check 52.0 : >$myComplex2Chain<"); // 
error_log("check 52.1 : >$complexStringBackward<"); // 
$complexStringBackward = $temp.$complexStringBackward;
error_log("check 52.2 : >$complexStringBackward <"); // 
error_log("check 52.4 : >$complexStringForward<"); //

//$complexStringBackward = 

$commandString .= $complexStringForward;
$commandString .= " ";              
error_log("check 52.5 : >$commandString<"); //

$jobCreateTime =  time();
mkdir("/data/runs/" .  $_POST["jobName"] . "/" . $_POST["pdbId"] ); // For new PDBs, this step is necessary.
$homoScanJobFileNamePartial =  $_POST["jobName"] . "/" . $_POST["pdbId"] . "/" . $mutationString ."." . $jobCreateTime . ".job";

$homoScanJobFileName = "/data/runs/" . $homoScanJobFileNamePartial;
//echo "\n $homoScanJobFileName <br> \n";

$homoScanJobHandle = fopen($homoScanJobFileName, 'w') or die('Cannot open file:  '.$homoScanJobFileName); //implicitly creates file
error_log("check 52.6 : >$homoScanJobFileName< opened"); //
$jobLogFilePartial = $_POST["jobName"] . "/" . $_POST["pdbId"] . "/" . $mutationString . "." . $jobCreateTime . ".log";
$jobLogFile = "/data//runs/" . $jobLogFilePartial;         
$commandString .= "  -PDBID " . $_POST["pdbId"] ;
$commandString .= "  -SQLSYSTEM MySQL -ACCOUNT webaccount -MOBILIZERRADIUS 0.0 -PARTITION core  ";
//$commandString .= "  -SQLSYSTEM MySQL -ACCOUNT X -MOBILIZERRADIUS 0.0 -PARTITION core  ";
$commandString .= " &> " . " " . $jobLogFile ; # /data//runs/" . $_POST["jobName"] . "/" . $_POST["pdbId"] . "/" . $mutationString . "." . $jobCreateTime . ".log  \n";
// 1A22 -SQLSYSTEM MySQL -ACCOUNT X -MOBILIZERRADIUS 0.0 -PARTITION core ";
//echo $commandString;
# #SBATCH -P core 
#SBATCH -P webserver 
error_log("check 52.9 >$myLibPathToPass<");
$headerString = <<<EOD
#!/bin/bash -l
#SBATCH -J $mutationString
#SBATCH -A webaccount
#SBATCH -t 48:00:00
#SBATCH --mem 4000
#SBATCH --ntasks=1
#SBATCH --ntasks-per-node=1
#SBATCH --cpus-per-task=1
#SBATCH -N 1                 
#SBATCH -n 1                
#SBATCH --oversubscribe     
#SBATCH -o $jobLogFile.%j.slurm.out 
# This file generated by __FILE__

export LD_LIBRARY_PATH=$myLibPathToPass;

EOD;
#"xdd/usr/lib/x86_64-linux-gnu/blas:/home/sam/svn/breeder/build:/usr/local/lib
$administratorEmail = administratorEmail;
#.",".$administratorEmail;
#$userMailAddress = $_POST["user_email,samuel.flores@scilifelab.se"];
//mail -s "Your job  $mutationString" $userMailAddress
$myJobName =  $_POST["jobName"];
$myPdbId = $_POST["pdbId"];
// actually we should move to $mutationString in the report.php below.  $mutationString already works and is nicely formatted..
$jobStartMailContents = <<<EOD
Dear User,

Thank you for using the homologyScanner web server. Your job has been started. If the mutation is in our database already, we may just need a few seconds to confirm that no additional structures have become available that might further improve precision. If this complex has been encountered, but the particular mutation has not, then the job should be done within a couple of hours. If the complex is completely new to our database, it could take as much as a day to get back to you. Any longer than that, please email $administratorEmail. Tell him/her to look at the log file, $jobLogFilePartial .

Bye
EOD;
$jobStartMailCommandString = "/usr/bin/sendemail -m \" $jobStartMailContents \"  -u \"Your job  $mutationString has been STARTED\" -t $userMailAddress,$administratorEmail -f sam@pe1.scilifelab.se \n";
//$jobStartMailCommandString = "echo \" $jobStartMailContents \" | /usr/bin/mail -s \"Your job  $mutationString has been STARTED\" $userMailAddress,$administratorEmail \n";

/*
If the run was successful, you can examine your results at http://pe1.scilifelab.se/homologyScanner/index.php . Go to the "Tool" tab. You should select :

Project:  $myJobName            
PDB ID:   $myPdbId           
Mutation: $mutationString

.. then hit "Load".

You will also be able to view the mutated structure(s) in the "Tool" tab of the web server. 
*/

$mailContents = <<<EOD
Dear User,

Thank you for using the homologyScanner web server. Your contribution not only provides you with the DDG for your protein of choice, it also provides you with the PDB IDs of structurally related and possibly useful complexes, and lastly translates your mutation to the numbering system of those other complexes. 

If you are getting this message, your job is in process. Some number of potential homologs have been detected, and all possibilities have been queued for investigation.

Any useful homologs are then used for a FoldX calculation. As these complete, they are added to the synopsis table, which you can find here: 

http://pe1.scilifelab.se/report.php?jobName=$myJobName&pdbId=$myPdbId&mutationString=$mutationString&complexString=$complexStringForward

No need to check that quite yet -- but if your job returns results you will get an Update email, and then the table will show resuts.

If there were any problems, ask us to look at the log file, $jobLogFilePartial .

In addition to the utility to you, your submission is a service to the community. The DDG is computed only once, so other users interested in this mutation will be able to access the result without waiting. If this is the first time this family of proteins has been submitted, then the search for structural homologs, which costs some compute time, will be done and not be repeated for future users.  Also by specifying the chains in each of the two parts of your complex, you are telling the community how you think this complex comes together. They don't have to agree, and can make a different choice, of course. But we may choose to automatically compute other mutations in this interface on an automated basis, at some future time.

Please do not reply to this email.


Bye
EOD;
$jobEndMailContents = <<<EOD
Dear User,

Thank you for using the homologyScanner web server. Your contribution not only provides you with the DDG for your protein of choice, it also provides you with the PDB IDs of structurally related and possibly useful complexes, and lastly translates your mutation to the numbering system of those other complexes. 

If you are getting this message, all detected homologs have been submitted for investigation, and DDG calculations have been queued, performed, or attempted for all that fulfilled the similarity criteria. Ho
    wever some or all results may still be pending.

There are two ways to view the results. You can check the synopsis table,  here: 

http://pe1.scilifelab.se/report.php?jobName=$myJobName&pdbId=$myPdbId&mutationString=$mutationString&complexString=$complexStringForward

You can also click the "View" tab, then select your submitted PDB ID, chains in complex, and mutation string. The "View" tool will give you not only the DDGs but also the mutant structure as generated by FoldX.

If there were any problems, ask us to look at the log file, $jobLogFilePartial .

In addition to the utility to you, your submission is a service to the community. The DDG is computed only once, so other users interested in this mutation will be able to access the result without waiting. If this is the first time this family of proteins has been submitted, then the search for structural homologs, which costs some compute time, will be done and not be repeated for future users.  Also by specifying the chains in each of the two parts of your complex, you are telling the community how you think this complex comes together. They don't have to agree, and can make a different choice, of course. But we may choose to automatically compute other mutations in this interface on an automated basis, at some future time.

Please do not reply to this email.


Bye
EOD;
#//$mailCommandString = "echo \" $mailContents \" | /usr/bin/mail -s \"Your job  $mutationString is IN PROCESS\" $userMailAddress,$administratorEmail \n";
$jobStartMailCommandString = "/usr/bin/sendemail -m \"$mailContents\" -u \"Your job  $mutationString is IN PROCESS \"   -t $userMailAddress,$administratorEmail -f sam@pe1.scilifelab.se \n";
$jobEndMailCommandString = "/usr/bin/sendemail -m \"$jobEndMailContents\" -u \"Your job  $mutationString has progressed  \"   -t $userMailAddress,$administratorEmail -f sam@pe1.scilifelab.se \n";

fwrite($homoScanJobHandle, $headerString);
fwrite($homoScanJobHandle, $jobStartMailCommandString );
fwrite($homoScanJobHandle, "\n");
#fwrite($homoScanJobHandle, $commandString);
fwrite($homoScanJobHandle, "\n");
# Running the command a total of 3 times, so we can recover from 3 stalled runs:
#fwrite($homoScanJobHandle, $commandString);
fwrite($homoScanJobHandle, "\n");
fwrite($homoScanJobHandle, $commandString);
fwrite($homoScanJobHandle, "\n");
fwrite($homoScanJobHandle, $jobEndMailCommandString );
fwrite($homoScanJobHandle, "\n");

#system ($commandString);
fclose($homoScanJobHandle);
//system ("echo \"<br> /usr/bin/sbatch  $homoScanJobFileName ... <br>\"" );
//system ("echo \"hellowww                             \" &> /data/runs/homoScan.1/1A22/temp.txt" );
system ("touch                                             /data/runs/homoScan.1/1A22/temp.txt" );
$result_msg = "Dear User, </br>You have requested <b>PDB ID : <span class='ctxt'>$myPdbId</span></b> , <b>complex : <span class='ctxt'>$complexStringForward</span></b> , <b>mutation (in PDB numbering) : <span class='ctxt'>$mutationString</span></b> . <br><br> A job file has been created called: <b>$homoScanJobFileNamePartial</b> . A log will be written to: <b>$jobLogFilePartial</b> . You will be emailed at <b>$userMailAddress</b> when your job is done. This email will also go to the administrator, $administratorEmail.</br>";

?>
	<div id="home-mid">
		<div id="action_page-div" class="k-header">
			<h2>
				Thank you for your submission
			</h2>
			<div id="action_page-content">
				<p class="text"><?php echo $result_msg; system ("/usr/bin/sbatch  $homoScanJobFileName &> /data/runs/homoScan.1/1A22/temp.txt" );?></p>
				<p>Click <b><a href="index.php">here</a></b> to be redirected back to our site.</p>
			</div>
		</div>
	</div>

	<style>
		html {
			font-family: Arial, Helvetica, sans-serif;
		}
		
		html,body {
			height:100%;
			width: 100%;
			margin: 0;
			padding: 0;
		}
		
		h1,h2,h3 {
			color: #F4AF03;
		}
		
		#home-mid {
			margin: 0;
			height: 100%;
			background: url("images/WAL-5.jpg") no-repeat center center fixed;
			background-size: cover;
			text-align:center;
		}

		#action_page-div {
			border-radius: 10px 10px 10px 10px;
			border-style: solid;
			border-width: 1px;
			overflow: auto;
			width: 40%;
			height: 40%;
			margin: auto;
			padding: 20px 20px 10px 20px;
			opacity: 0.8;
			position: relative;
			top: 10%;
			text-align: center;
		}
		
		#action_page-content {
			margin: 0 auto;
			padding: 10px 0;
			text-align: left;
		}
		
		.ctxt {
			color: #F4AF03;
		}
	</style>
</body>
</html>
