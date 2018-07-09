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
    }


?>
</table>
-->
<?php
$commandString = "";
$commandString = "/home/sflores/svn/homologyScanner/build/homologyScanner -FASTAEXECUTABLE /usr/local//fasta_lwp/fasta_lwp.pl -FASTATEMPDIRECTORY /usr/local//fasta_lwp///temp/ -BREEDEREXECUTABLE /home/mmb/svn/breeder/build/breeder -BREEDERMAINDIRECTORY /home/mmb/svn/breeder -DATABASE mmb -MMBEXECUTABLE /home/mmb/svn/RNAToolbox/trunk/build/MMB -LASTSTAGE 1 -FOLDXSCRIPT /home/mmb/svn/breeder/perl/run-foldx.3.pl -FOLDXEXECUTABLE //usr/local//foldx/foldx -SQLSERVER localhost -SQLEXECUTABLE /usr/bin/mysql -SQLPASSWORD m1sQ1P@ssw0rd -USER root -SQLUSER root -JOBLIBRARYPATH /usr/local/SimTK/lib:/usr/local/SimTK/lib64:/home/mmb/svn/RNAToolbox/trunk/build/:/home/mmb/svn/breeder/build -REPORTINGINTERVAL 0.000001 -NUMREPORTINGINTERVALS 2 -FLEXIBILITYWINDOWOFFSET 2 -TEMPERATURE 298 -ID "
. $_POST["jobName"]
. " -ONEMUTANT ";

$mutationString = "";
$i = 0;
$myChain = "chainId" . $i;
$myResidue = "residueNumber" . $i;
$mySubstituedResidueType = "substitutedResidueType" . $i;
while ($_POST[$myChain]) 
{
    if ($i > 0) { $mutationString  .= "."; }
    $mutationString .= $_POST[$myChain] . "-" . rtrim($_POST[$myResidue]) . "-" . $_POST[$mySubstituedResidueType];
    $i++;
    $myChain = "chainId" . $i;
    $myResidue = "residueNumber" . $i;
    $mySubstituedResidueType = "substitutedResidueType" . $i;
}
$commandString .= $mutationString;
$commandString .= " -WORKINGDIRECTORY /data//runs/" . $_POST["jobName"] . "/" . $_POST["pdbId"];
$commandString .= "  -CHAINSINCOMPLEX ";

$complexString = "";

$i = 0;
$myComplex1Chain = "complex1ChainSelector" . $i;
while ($_POST[$myComplex1Chain]) {
    $myComplex1Chain = "complex1ChainSelector" . $i;
    //$commandString .= $_POST[$myComplex1Chain];     
    $complexString .= $_POST[$myComplex1Chain];     
    $i++;
    $myComplex1Chain = "complex1ChainSelector" . $i;
}
    
$complexString .= ",";
//$commandString .= ",";
$i = 0;
$myComplex2Chain = "complex2ChainSelector" . $i;
while ($_POST[$myComplex2Chain]) {
    $myComplex2Chain = "complex2ChainSelector" . $i;
    //$commandString .= $_POST[$myComplex2Chain];
    $complexString .= $_POST[$myComplex2Chain];
    $i++;
    $myComplex2Chain = "complex2ChainSelector" . $i;
}

$commandString .= $complexString;
$commandString .= " ";              

$jobCreateTime =  time();
$homoScanJobFileNamePartial =  $_POST["jobName"] . "/" . $_POST["pdbId"] . "/" . $mutationString ."." . $jobCreateTime . ".job";
$homoScanJobFileName = "/data//runs/" . $homoScanJobFileNamePartial;
//echo "\n $homoScanJobFileName <br> \n";

$homoScanJobHandle = fopen($homoScanJobFileName, 'w') or die('Cannot open file:  '.$homoScanJobFileName); //implicitly creates file
$jobLogFilePartial = $_POST["jobName"] . "/" . $_POST["pdbId"] . "/" . $mutationString . "." . $jobCreateTime . ".log";
$jobLogFile = "/data//runs/" . $jobLogFilePartial;         
$commandString .= "  -PDBID " . $_POST["pdbId"] ;
$commandString .= "  -SQLSYSTEM MySQL -ACCOUNT X -MOBILIZERRADIUS 0.0 -PARTITION core  ";
$commandString .= " &> " . " " . $jobLogFile ; # /data//runs/" . $_POST["jobName"] . "/" . $_POST["pdbId"] . "/" . $mutationString . "." . $jobCreateTime . ".log  \n";
// 1A22 -SQLSYSTEM MySQL -ACCOUNT X -MOBILIZERRADIUS 0.0 -PARTITION core ";
//echo $commandString;
$headerString = <<<EOD
#!/bin/bash -l
#SBATCH -J $mutationString
#SBATCH -A X
# #SBATCH -P core 
#SBATCH -t 48:00:00
#SBATCH --mem 4000
#SBATCH --ntasks=1
#SBATCH --ntasks-per-node=1
#SBATCH --cpus-per-task=1
#SBATCH -N 1                 
#SBATCH -n 1                 
# This file generated by __FILE__

export LD_LIBRARY_PATH=/usr/local/SimTK/lib:/usr/local/SimTK/lib64:/home/sflores/svn/RNAToolbox/trunk/build/:/home/sflores/svn/breeder/build

EOD;

$userMailAddress = $_POST["user_email"];
#$userMailAddress = $_POST["user_email,samuel.flores@scilifelab.se"];
//mail -s "Your job  $mutationString" $userMailAddress
$myJobName =  $_POST["jobName"];
$myPdbId = $_POST["pdbId"];
// actually we should move to $mutationString in the report.php below.  $mutationString already works and is nicely formatted..
$jobStartMailContents = <<<EOD
Dear User,

Thank you for using the homologyScanner web server. Your job has been started. If the mutation is in our database already, we may just need a few seconds to confirm that no additional structures have become available that might further improve precision. If this complex has been encountered, but the particular mutation has not, then the job should be done within a couple of hours. If the complex is completely new to our database, it could take as much as a day to get back to you. Any longer than that, please email Samuel Flores. Tell him to look at the log file, $jobLogFilePartial .

Bye
EOD;
$jobStartMailCommandString = "echo \" $jobStartMailContents \" | /usr/bin/mail -s \"Your job  $mutationString has been STARTED\" $userMailAddress \n";

/*
If the run was successful, you can examine your results at http://ae15a.dyn.scilifelab.se/homologyScanner/index.php . Go to the "Tool" tab. You should select :

Project:  $myJobName            
PDB ID:   $myPdbId           
Mutation: $mutationString

.. then hit "Load".

You will also be able to view the mutated structure(s) in the "Tool" tab of the web server. 
*/

$mailContents = <<<EOD
Dear User,

Thank you for using the homologyScanner web server. Your contribution not only provides you with the DDG for your protein of choice, it also provides you with the PDB IDs of structurally related and possibly useful complexes, and lastly translates your mutation to the numbering system of those other complexes. 

If you are getting this message, your job has completed. 

You can see a synopsis table of all results at:

http://ae15a.dyn.scilifelab.se/homologyScanner/report.php?jobName=$myJobName&pdbId=$myPdbId&mutationString=$mutationString&complexString=$complexString

If there were any problems, ask us to look at the log file, $jobLogFilePartial .

In addition to the utility to you, your submission is a service to the community. The DDG is computed only once, so other users interested in this mutation will be able to access the result without waiting. If this is the first time this family of proteins has been submitted, then the search for structural homologs, which costs some compute time, will be done and not be repeated for future users.  Also by specifying the chains in each of the two parts of your complex, you are telling the community how you think this complex comes together. They don't have to agree, and can make a different choice, of course. But we may choose to automatically compute other mutations in this interface on an automated basis, at some future time.


Bye
EOD;
$mailCommandString = "echo \" $mailContents \" | /usr/bin/mail -s \"Your job  $mutationString is COMPLETED\" $userMailAddress \n";

fwrite($homoScanJobHandle, $headerString);
fwrite($homoScanJobHandle, $jobStartMailCommandString );
fwrite($homoScanJobHandle, "\n");
fwrite($homoScanJobHandle, $commandString);
fwrite($homoScanJobHandle, "\n");
fwrite($homoScanJobHandle, $mailCommandString );
fwrite($homoScanJobHandle, "\n");

#system ($commandString);
fclose($homoScanJobHandle);
//system ("echo \"<br> /usr/bin/sbatch  $homoScanJobFileName ... <br>\"" );
//system ("echo \"hellowww                             \" &> /data/runs/homoScan.1/1A22/temp.txt" );
system ("touch                                             /data/runs/homoScan.1/1A22/temp.txt" );
$result_msg = "Dear User, </br>You have requested <b>PDB ID : <span class='ctxt'>$myPdbId</span></b> , <b>complex : <span class='ctxt'>$complexString</span></b> , <b>mutation (in PDB numbering) : <span class='ctxt'>$mutationString</span></b> . <br><br> A job file has been created called: <b>$homoScanJobFileNamePartial</b> . A log will be written to: <b>$jobLogFilePartial</b> . You will be emailed at <b>$userMailAddress</b> when your job is done.</br>";

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
