<!DOCTYPE html>
<html>
<head>
	<title>homologyScanner Report Page</title>
    <link rel="stylesheet" href="styles/kendo.common.min.css" />
    <link rel="stylesheet" href="styles/kendo.moonlightMod.css" />
</head>
<body>
<!-- <p> POST data : </p>-->

<table>
<?php 


    foreach ($_POST as $key => $value) {
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
<!-- 
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
    $pdbId = $_GET["pdbId"];
    echo "howdyyy";
    echo $pdbId;
    echo "howdyyy";


?>
</table>
-->
	<div id="home-mid">
		<div id="report_page-div" class="k-header">
			<h2>
				homologyScanner Report
			</h2>
			<div id="report_page-intro">
				<h3>Your Request</h3>
				<table id="report-intro-table" >
					<tr>
						<td>
							Dataset:
						</td>
						<td class='ctxt'>
							<?php echo $_GET['jobName'] ?>
						</td>
					</tr>
					<tr>
						<td>
							Submitted PDB ID:
						</td>
						<td class='ctxt'>
							<?php echo $_GET['pdbId'  ] ?>
						</td>
					</tr>
					<tr>
						<td>
							Complex: <br>
							(subunit 1, subunit 2)
						</td>
						<td class='ctxt'>
							<?php echo $_GET['complexString'] ?>
						</td>
					</tr>
					<tr>
						<td>
							Mutation: <br>
							(PDB numbering)
						</td>
						<td class='ctxt'>
							<?php echo $_GET['mutationString'] ?>
						</td>
					</tr>
				</table>				
				<p>Note that the above mutation was submitted by you using PDB numbering. Elsewhere in our server we use renumbered residues, which may differ.</p>
			</div>
			<div id="report_page-results">
				<h3>Results</h3>
				<table id="numResults"   name="numResults" style=""></table>
				</br>				
				<table id="resultsTable" name="resultsTable" style=""></table>
				</br>
				<table id="averageDDG"   name="averageDDG"   style=""></table>
				<p>You can view the mutated structure for each of the above. Click the "Tool" tab on the homologyScanner server, select the PDB ID, complex, and mutation (using the renumbered mutation string above). Then click the "Load structure" button.</p>
				<p>Mutation strings on this server follow the format: C-NNNI-S.c-nnni-s, where e.g. C,c are chain ID's, NNN and nnn are residue numbers, I and i are insertion codes (if any), and S and s are the mutant residue type in single letter code.  These can be concatenated with '.', for as many as four simultaneous substitutions.</p>
				<p>Click <b><a href="index.php">here</a></b> to be redirected back to our site.</p>
			</div>
		</div>
	</div>
<?php

?>


</body>



<script>

    var pdbId = "<?php echo $_GET['pdbId'] ?>";
    var mutationString = "<?php echo $_GET['mutationString'] ?>";
    var complexString = "<?php echo $_GET['complexString'] ?>";
    var jobName = "<?php echo $_GET['jobName'] ?>";
    populateResultsTable(pdbId , mutationString, complexString, jobName); 
    //populateResultsTable("1A22"  , "A-174-A", "A,B", "homoScan.1");

    function populateResultsTable( $pdbId ,  $mutationStringPrimary, $complexStringPrimary, $jobName){
        var myTable = document.getElementById("resultsTable");
            var xhttp = new XMLHttpRequest(); // May need variant to handle old IE browsers https://www.w3schools.com/js/js_ajax_http.asp
            console.log("check 1  ");

            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    console.log("check 2  ");
                    console.log((this.responseText)) ;
                    console.log("check 2.1  ");
                    console.log(JSON.parse(this.responseText)) ;
                    var myResultsArray = []; // casting these as arrays
                    var myResultsArray = JSON.parse(this.responseText);
                    console.log(myResultsArray);
                    console.log(myResultsArray.length);
                    var averageDDG = 0.0;       
                    for (var i = 0; i< myResultsArray.length; i++){
                        console.log("check 2.2  ");
                        //myResultsArray.push(myTempChainIdArray[i].chainId);  
                        //document.allPdbChains.push(myTempChainIdArray[i].chainId); // trying to make sure the array gets passed by value not reference
                        //document.remainingPdbChains.push(myTempChainIdArray[i].chainId); // trying to make sure the array gets passed by value not reference
                        myRow = myTable.insertRow(0);
                        
                        
                        myCell = myRow.insertCell(0);
                        //myCell.innerHTML = myResultsArray[i].DDGhomolog;             
                        myCell.innerHTML = myResultsArray[i].DDGhomolog.toFixed(3);             
                        myCell = myRow.insertCell(0);
                        myCell.innerHTML = myResultsArray[i].mutationStringHomolog;
                        myCell = myRow.insertCell(0);
                        myCell.innerHTML = myResultsArray[i].complexHomolog; 
                        myCell = myRow.insertCell(0);
                        myCell.innerHTML = myResultsArray[i].pdbHomolog; 
                        averageDDG += myResultsArray[i].DDGhomolog / myResultsArray.length;
                    }
                    //populateRemainingPdbChains();            
                    //console.log(myChainIdArray);
                    //document.usedPdbChains = [];
		    var myRow = myTable.insertRow(0); 
		    var myCell = myRow.insertCell(0);
		    myCell.innerHTML = "FoldX ΔΔG <br>(kcal/mol)"
		    var myCell = myRow.insertCell(0);
		    myCell.innerHTML = "Mutation <br>(renumbered residues)"
		    var myCell = myRow.insertCell(0);
		    myCell.innerHTML = "Chains in <br>subunit 1, subunit 2" ;               
		    var myCell = myRow.insertCell(0);
		    myCell.innerHTML = "PDB ID" ;               
                    
                    var myRow = document.getElementById("numResults").insertRow(0);
                    var myCell = myRow.insertCell(0);
                    myCell.innerHTML = "<span class='ctxt'>results</span>";                     
                    var myCell = myRow.insertCell(0);
                    myCell.innerHTML = "<span class='ctxt'>" + myResultsArray.length + "</span>";                     
                    var myCell = myRow.insertCell(0);
                    myCell.innerHTML = "HomologyScanner has computed:"
                    if (myResultsArray.length > 0){
                        var myRow = document.getElementById("averageDDG").insertRow(0);
                        var myCell = myRow.insertCell(0);
                        myCell.innerHTML = "<span class='ctxt'>" + averageDDG.toFixed(3) + "</span>";                     
                        var myCell = myRow.insertCell(0);
                        myCell.innerHTML = "average DDG (kcal/mol) : ";
                    } else {
                        var myRow = document.getElementById("averageDDG").insertRow(0);
                        var myCell = myRow.insertCell(0);
                        myCell.innerHTML = "No DDG results. Unable to compute average DDG.";
                    }
                    ;
                } else {
                    console.log("check 3  ");
                    console.log(this.status) ;
                }
            }; 
            xhttp.open("POST", "calls.php", true ); // Going with asynchronous as I don't want to move on until the chains are ready. Revisit this decision later.
            console.log(this.status) ;
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // this turns out to be crucial!
            xhttp.send("restcall=getSynopsisTable&arguments[0]=" + $pdbId + "&arguments[1]=" + $mutationStringPrimary  + "&arguments[2]=" + $complexStringPrimary + "&arguments[3]=" + $jobName) ;
            console.log("check 4  ");

    }
</script>
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

		#report_page-div {
			border-radius: 10px 10px 10px 10px;
			border-style: solid;
			border-width: 1px;
			overflow: auto;
			width: 40%;
			height: 80%;
			margin: auto;
			padding: 20px 20px 10px 20px;
			opacity: 0.8;
			position: relative;
			top: 10%;
			text-align: center;
		}
		
		#report_page-intro,#report_page-results {
			margin: 0 auto;
			padding: 10px 0;
			text-align: left;
		}
		
		#resultsTable{
			border: 1px solid white;
			text-align: center;
		}
		
		#resultsTable td {
			border: 1px solid white;
			padding: 0 5px;
		}
		
		td {
			font-weight: bold;
		}
		
		.ctxt {
			color: #F4AF03;
			text-align: center;
		}
	</style>
</html>
