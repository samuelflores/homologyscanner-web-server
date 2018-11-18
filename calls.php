<?php
header('Content-Type: application/json');
chdir(__DIR__);
include ('db.php');
include ('fs.php');
include ('utils.php');
define('flag', TRUE);
include ('dtypes.php');

$aResult = array();
const LOCAL_FPATH = webrootdirectory . '/temp/';

//$_POST['restcall'] = parse_url($_SERVER["_POST"]);
//$_POST['arguments'] = parse_url($_SERVER["_ARGS"]);
if(!isset($_POST['restcall'])){
	$aResult['error'] = 'No function name!';
}

if(!isset($_POST['arguments'])){
	$aResult['error'] = 'No function arguments!';
}

if(!isset($aResult['error'])){
        //console.log("check 2.5");
        //echo "<script>console.log( 'Check 2.5' );</script>";
	switch($_POST['restcall']){
		/*case 'getRemotePdbFile':
			// Arguments sould be remote and local file paths
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 2)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$fs = new FileServer(key5, key8, key6);
				$fs->ssh_connection();
				$remotef = $_POST['arguments'][0];
				$localf = $_POST['arguments'][1];
				$aResult['result'] = $fs->scp_bring($remotef, $localf);
				echo $aResult['result'];
			}
			$fs->disconnect();
			break;
			*/
		
/* [START] DATABASE CASES BELOW ------------------------------------------------------- */

/* [START] Results                    --------------------------------------------- */

        /**
         * Description:Returns the results for all applicable homolog jobs, based on PRIMARY job. Meaning that the input is pdbId, mutationStringPrimary (PDB numbered!),   complexStringPrimary, and  jobName, and output is primary and homology pdbId, complexString, mutationString, and DDG, plus a few other results. This does NOT average over DDG's. That could be a post processing or separate command.
         * Label: 'getSynopsisTable'
         * Args: pdbId, mutationStringPrimary, complexStringPrimary, jobName
         */
                case 'getSynopsisTable':


                        if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 4)){
                                $aResult['error'] = 'Error in arguments!';
                                echo $aResult['error'];
                        }
                        else {
                                $db = new Database(webhost, webusername, webpassword, mmbdatabase);
                                $db->connect();
                                $aResult['result'] = $db->getSynopsisTable($_POST['arguments'][0],$_POST['arguments'][1], $_POST['arguments'][2], $_POST['arguments'][3] );
                                $db->disconnect();
                                echo json_encode($aResult['result']);
                        }
                        break;
 
        /**
         * Description:Returns the results for all applicable homolog jobs, based on PRIMARY job. Meaning that the input is pdbId, mutationStringPrimary (PDB numbered!),   complexStringPrimary, and  jobName, and output is primary and homology pdbId, complexString, mutationString, and DDG, plus a few other results. This does NOT average over DDG's. That could be a post processing or separate command.
         * Label: 'getSynopsisTableFromHomolog'
         * Args: pdbId, mutationStringPrimary, complexStringPrimary, jobName
         */
                case 'getSynopsisTableFromHomolog':


                        if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 4)){
                                $aResult['error'] = 'Error in arguments!';
                                echo $aResult['error'];
                        }
                        else {
                                $db = new Database(webhost, webusername, webpassword, mmbdatabase);
                                $db->connect();
                                $aResult['result'] = $db->getSynopsisTableFromHomolog($_POST['arguments'][0],$_POST['arguments'][1], $_POST['arguments'][2], $_POST['arguments'][3] );
                                $db->disconnect();
                                echo json_encode($aResult['result']);
                        }
                        break;

        /**
         * Description:Returns the results for all applicable homolog jobs, based on HOMOLOG job. Meaning that the input is pdbId, mutationStringHomolog (renumbered!) , (need to add complexStringHomolog,) and  jobName, and output is primary and homology pdbId, complexString, mutationString, and DDG, plus a few other results. This does NOT average over DDG's. That could be a post processing or separate command.
         * Label: 'getSynopsisTableFromHomologOld'
         * Args: pdbHomolog, mutationStringHomolog (, complexStringHomolog)
         */
                case 'getSynopsisTableFromHomologOld':

                        //console.log("check 5.0");
                        if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 2)){
                                $aResult['error'] = 'Error in arguments!';
                                echo $aResult['error'];
                        }
                        else {
                                $db = new Database(webhost, webusername, webpassword, mmbdatabase);
                                $db->connect();
                                //console.log("check 6.0");
                                //console.log($_POST['arguments'][0]);
                                //console.log($_POST['arguments'][1]);
                                //$aResult['result'] = $db->getSynopsisTable($_POST['arguments'][0], "A-174-A","A,B", "homoScan.1" ); // This call works fine
                                $aResult['result'] = $db->getSynopsisTableFromHomolog($_POST['arguments'][0],"A-163-A"                  );
                                ////$aResult['result'] = $db->getSynopsisTableFromHomolog($_POST['arguments'][0],$_POST['arguments'][1] );
                                $db->disconnect();
                                echo json_encode($aResult['result']);
                        }
                        break;

/* [START] Evaluation/Notes Processes --------------------------------------------- */
	/**
	 * Description:	A user submits his evaluation for a particular project mutation.
	 * Label: 		'submitEval'
	 * Args:		sessionUserName(email), jobName, mutation, status, note
	 */
		case 'submitEval':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 5)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else{
				$email = $_POST['arguments'][0];
				$jobName = $_POST['arguments'][1];
				$mutation = $_POST['arguments'][2];
				$status = $_POST['arguments'][3];
				
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();
				
				$aResult['result'] = $db->evaluation($email, $jobName, $mutation, $status, $_POST['arguments'][4]);
				unset($_POST['arguments'][4]);
				echo $aResult['result'];
			}
			$db->disconnect();
			break;

	/**
	 * Description:	Returns the evaluation (if exists) for a particular project mutation.
	 * Label: 		'getEval'
	 * Args:		sessionUserName(email), jobName, mutation
	 */
		case 'getEval':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 4)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else{
				$email = $_POST['arguments'][0];
				$jobName = $_POST['arguments'][1];
				$pdbId    = $_POST['arguments'][2];
				$mutation = $_POST['arguments'][3];
				
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();
				
				$aResult['result'] = $db->getEval($email, $jobName,$pdbId, $mutation);
				echo json_encode($aResult['result']);
			}
			$db->disconnect();
			break;


/* [START] Account Info/Changes ------------------------------- */
		
	/**
	 * Description:	Changes the password of a user.
	 * Label: 		'passwdChange'
	 * Args:		sessionUserName(email), old_password, new_password
	 */
		case 'passwdChange':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 3)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$email = $_POST['arguments'][0];
				$old_passwd = $_POST['arguments'][1];
				$new_passwd = $_POST['arguments'][2];
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();
				
				$aResult['result'] = $db->changePasswd($email, $old_passwd, $new_passwd);
				echo $aResult['result'];
			}
			$db->disconnect();
			break;
		
	/**
	 * Description:	Changes the first name of a user.
	 * Label: 		'fnameChange'
	 * Args:		sessionUserName(email), new_first_name
	 *
		case 'fnameChange':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 2)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$email = $_POST['arguments'][0];
				$nFname = $_POST['arguments'][1];

				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();
				
				$aResult['result'] = $db->changeFame($email, $nFname);
				echo $aResult['result'];
			}
			$db->disconnect();
			break;
	*/	
	/**
	 * Description:	Changes the last name of a user.
	 * Label: 		'lnameChange'
	 * Args:		sessionUserName(email), new_last_name
	 *
		case 'lnameChange':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 2)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$email = $_POST['arguments'][0];
				$nLname = $_POST['arguments'][1];

				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();
				
				$aResult['result'] = $db->changeLname($email, $nLname);
				echo $aResult['result'];
			}
			$db->disconnect();
			break;
	*/	
	/**
	 * Description:	Changes the purpose of a user.
	 * Label: 		'purposeChange'
	 * Args:		sessionUserName(email), new_purpose
	 *
		case 'purposeChange':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 2)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$email = $_POST['arguments'][0];
				$nPurpose = $_POST['arguments'][1];

				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();
				
				$aResult['result'] = $db->changePurpose($email, $nPurpose);
				echo $aResult['result'];
			}
			$db->disconnect();
			break;
	*/
	
	/**
	 * Description:	Changes the information of a user.
	 * Label: 		'userInfoChange'
	 * Args:		sessionUserName(email), new_fname, new_lname, new_purpose
	 */
		case 'userInfoChange':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 4)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$email = $_POST['arguments'][0];
				$nfname = $_POST['arguments'][1];
				$nlname = $_POST['arguments'][2]; 
				$nPurpose = $_POST['arguments'][3];

				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();
				
				$aResult['result'] = $db->updateUserInfo($email, $nfname, $nlname, $nPurpose);
				echo $aResult['result'];
			}
			$db->disconnect();
			break;

	/**
	 * Description:	Gets information about a specific user.
	 * Label: 		'getUserInfo'
	 * Args:		sessionUserName(email)
	 */
		case 'getUserInfo':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$email = $_POST['arguments'][0];

				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();
				
				$aResult['result'] = $db->userInfo($email);
				echo json_encode($aResult['result']);
			}
			$db->disconnect();
			break;
			
	/**
	 * Description:	Registration of a new user.
	 * Label: 		'registration'
	 * Args:		email, password, [firstname], [lastname], [purpose]
	 */
		case 'registration':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 5)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$email = $_POST['arguments'][0];
				$passwd = $_POST['arguments'][1];
				$fname = $_POST['arguments'][2];
				$lname = $_POST['arguments'][3];
				$purpose = $_POST['arguments'][4];
				
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();
				
				$aResult['result'] = $db->userReg($email, $passwd, $fname, $lname, $purpose);
				echo $aResult['result'];
			}
			$db->disconnect();
			break;

	/**
	 * Description:	Login of an existed user.
	 * Label: 		'userLogin'
	 * Args:		email, password
	 */
		case 'userLogin':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 2)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();

				$aResult['result'] = $db->userLogin($_POST['arguments'][0], $_POST['arguments'][1]);
				echo $aResult['result'];
			}
			$db->disconnect();
			break;
		
		/**
		 * Description:	Gets the evaluations that the user has done.
		 * Label: 		'getUserEvals'
		 * Args:		email
		 */
		case 'getUserEvals':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();

				$aResult['result'] = $db->getEvalList($_POST['arguments'][0]);
				echo json_encode($aResult['result']);
			}
			$db->disconnect();
			break;
/* [END] Account Info/Changes -------------------------------------- */

/* [START] Tool Management Info ------------------------------------ */
		

	/**
	 * Description:	Retrieves the directory and brings the file of a specific project.
	 * Label: 		'getPdb'
	 * Args:		jobName, pdbId, mutationString, sessionID
	 */

                 case 'getPdb':
                         // Needed Arguments: JobName, pdbHomolog, complexHomolog,  MutationString, SessionID
                         
                         if(is_array(!$_POST['arguments']) || (!count($_POST['arguments']) == 5)){
                                 $aResult['error'] = 'Error in arguments!';
                                 echo $aResult['error'];
                                 //return false;
                         }
                         else {
                                 $jobName = $_POST['arguments'][0];
                                 $pdbId = $_POST['arguments'][1];
                                 $complexHomolog = $_POST['arguments'][2];
                                 $mString = $_POST['arguments'][3];
                                 $sessionID = stripslashes($_POST['arguments'][4]);
                                 $db = new Database(webhost, webusername, webpassword, mmbdatabase);
                                 $db->connect();
                                 //SCF This should still be fine. However it should be the overarching directory, we will need the primaryPdbId and homologPdbId:
                                 $remotePath = $db->getProjectDir($jobName);
                                 error_log("check 8");
                                 error_log($remotePath);
                                 $jobName = str_replace("/", "_", $jobName);
                                 if(!$remotePath){
                                         return false;
                                 }
                                 clearstatcache();
                                 $primaryPdbId = $db->getPrimaryPdbId($pdbId,$complexHomolog);
                                 $db->disconnect();
                                 $remotePath .= '/' . $primaryPdbId . '/' . $pdbId . '/' . $mString . '/raw_Repair_1.pdb';
                                 
                                 $localFile = LOCAL_FPATH . $sessionID;
                                 error_log("check 9");
                                 error_log($localFile );
                                 //echo $localFile;
                                 if(!file_exists($localFile) || !is_dir($localFile)){
                                         if(!mkdir($localFile, 0777, true)){
                                                 $error = error_get_last();
                                                 echo "<script>console.log( 'Debug Objects: " . $error['message'] . "' );</script>";
                                         }
                                 }
                                 clearstatcache();
 
                                 $localFile .= '/' . $jobName . "_" . $mString . ".pdb";
 
                                 if(file_exists($localFile) && !is_dir($localFile)){
                                         if(!Utils::deletePdbFile($localFile)){
                                         }
                                 }
                                 clearstatcache();
 
 
                                 $aResult['result'] = copy($remotePath, $localFile);
                                 if($aResult['result']){
                                         echo $aResult['result'];
                                 }
                                 else {
                                         echo "Copying from $remotePath to $localFile .. There is currently no file for this mutation.";
                                         error_log("Copying from $remotePath to $localFile .. There is currently no file for this mutation.");
                                 }
                         }
                         break;



	/**
	 * Description:	Retrieves the directory and brings the file of a specific project.
	 * Label: 		'getProject'
	 * Args:		jobName, mutationString, sessionID
	 */
		/*
                case 'getPdb':
			// Needed Arguments: JobName, MutationString, SessionID
			
			if(is_array(!$_POST['arguments']) || (!count($_POST['arguments']) == 3)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
				//return false;
			}
			else {
				$jobName = $_POST['arguments'][0];
				$mString = $_POST['arguments'][1];
				$sessionID = stripslashes($_POST['arguments'][2]);
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();

				$remotePath = $db->getProjectDir($jobName);
				$db->disconnect();

				$jobName = str_replace("/", "_", $jobName);

				if(!$remotePath){
					return false;
				}

				//$remotePath .= '/' . $mString . '/last.2.pdb';
                                // SCF updated:
				$remotePath .= '/' . $mString . '/raw_Repair_1.pdb';
				//echo $remotePath . '<br>';
				
				$localFile = LOCAL_FPATH . $sessionID;
				//echo $localFile;

				if(!file_exists($localFile) || !is_dir($localFile)){
					if(!mkdir($localFile, 0777, true)){
						//return false;
						$error = error_get_last();
						echo "<script>console.log( 'Debug Objects: " . $error['message'] . "' );</script>";
					}
				}
				clearstatcache();

				$localFile .= '/' . $jobName . "_" . $mString . ".pdb";
				//echo $localFile;

				if(file_exists($localFile) && !is_dir($localFile)){
					//if(!Utils::deletePdbFile($sessionID . '/' . $jobName . "_" . $mString . ".pdb")){
					if(!Utils::deletePdbFile($localFile)){
						//return false;
					}
				}
				clearstatcache();


				$aResult['result'] = copy($remotePath, $localFile);
				if($aResult['result']){
					echo $aResult['result'];
				}
				else {
					echo "Copying from $remotePath to $localFile .. There is currently no file for this mutation.";
				}
				//return true;
			}
			break;*/

	/**
	 * Description:	Fetch all project names existed into the database.
	 * Label: 		'getJobNames'
	 * Args:		sessionUserName(email)
	 */
		case 'getJobNames':
                        //$_POST['arguments'] =  array('this'); //SCF
			if ((!count($_POST['arguments'] == 1))){ //SCF removed first test, because it was unexplainably failing always
			//if(!is_array($_POST['arguments']) || (!count($_POST['arguments'] == 1))){
				//$aResult['error'] = 'Error in arguments!'; //,$_POST['arguments'],' howdy-doo ';
				//$aResult['error'] = $_POST['arguments'];
				//$aResult['error'] = count($_POST['arguments']);
                                $aResult['error'] =  is_array($_POST['arguments']);
				echo $aResult['error'];
				return false;
			}
			else {
				$cred = 0;
				if(filter_var($_POST['arguments'][0], FILTER_VALIDATE_EMAIL) && !($_POST['arguments'][0] === "Guest")){
					$cred = 1;
				}
				elseif($_POST['arguments'][0] === "Guest") {
					$cred = 0;
				}
				
                                // mmbdatabase is database 'mmb'
                                //echo "howdy";
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();

				$aResult['result'] = $db->getJobNames($cred);
				$db->disconnect();
				echo json_encode($aResult['result']);
                                //return array("foo" => "bar");//{howdy:'hello'};
                                //echo array("foo" => "bar");//{howdy:'hello'};
				//return true;
			}
			break;

        /**
         * Description: Fetch distinct PDB IDs 
         * for a specific project.
         * Label:               'getDistinctPdbIdsForProject'
         * Args:                jobName
         */
                case 'getDistinctPdbIdsForProject':
                        if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
                                $aResult['error'] = 'Error in arguments!';
                                echo $aResult['error'];
                        }
                        else {
                                $db = new Database(webhost, webusername, webpassword, mmbdatabase);
                                $db->connect();
                                $aResult['result'] = $db->getDistinctPdbIdsForProject($_POST['arguments'][0]);
                                $db->disconnect();
                                echo json_encode($aResult['result']);
                        }
                        break;

	/**
	 * Description:	Fetch mutation info (Mutation and WildType string, Status and ddg)
	 * for a specific project.
	 * Label: 		'getMutInfo'
	 * Args:		jobName
	 */
		case 'getMutInfo':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();
				
				$aResult['result'] = $db->getMutInfo($_POST['arguments'][0]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;
		
	/**
	 * Description:	Fetch mutation info (Mutation and WildType string, Status and ddg)
	 * for a specific project.
	 * Label: 		'getMutInfoFromProjectAndPdb'
	 * Args:		jobName
	 */
		case 'getMutInfoFromProjectAndPdb':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 2)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();
				
				$aResult['result'] = $db->getMutInfoFromProjectAndPdb($_POST['arguments'][0], $_POST['arguments'][1]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;

	/**
	 * Description:	Fetch mutation info (Mutation and WildType string, Status and ddg)
	 * for a specific $jobName,$pdbHomolog,$complexHomolog
	 * Label: 		'getMutInfoFromHomologPdbAndComplex'
	 * Args:		jobName pdbHomolog complexHomolog
	 */
		case 'getMutInfoFromHomologPdbAndComplex':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 3)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();
				
				$aResult['result'] = $db->getMutInfoFromHomologPdbAndComplex($_POST['arguments'][0], $_POST['arguments'][1], $_POST['arguments'][2]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;




        /**
         * Description: Fetch complexHomolog 
         * for a specific pdbHomolog .
         * Label:               'getComplexesFromPdb'
         * Args:                pdbHomolog
         */
                case 'getComplexesFromPdb':
                        if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
                                $aResult['error'] = 'Error in arguments!';
                                echo $aResult['error'];
                        }
                        else {
                                $db = new Database(webhost, webusername, webpassword, mmbdatabase);
                                $db->connect();
                                
                                $aResult['result'] = $db->getComplexesFromPdb($_POST['arguments'][0]);
                                $db->disconnect();
                                
                                echo json_encode($aResult['result']);
                        }
                        break;

	/**
	 * Description:	Returns the whole set of information of a specific project.
	 * Label: 		'getJobInfo'
	 * Args:		jobName
	 */
		case 'getJobInfo':
			/* TODO later */
			/* As a First Guess -> Needed Arguments: jobName */
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();

				$aResult['result'] = $db->getJobInfo($_POST['arguments'][0]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;

	/**
	 * Description:	Returns the existed purposes.
	 * Label: 		'getPurposes'
	 * Args:		jobName
	 */
		case 'getPurposes':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();

				$aResult['result'] = $db->getPurp();
				$db->disconnect();

				//echo '[{"type":"Academic","id":0},{"type":"Commercial","id":1},{"type":"Private","id":2}]';
				echo json_encode($aResult['result']);
			}
			break;

/* [END] Tool Management Info -------------------------------------- */

/* [END] DATABASE CASES ------------------------------------------------------ */

/* [START] UTILS CASES BELOW ------------------------------------------------- */

		/**
		 * Description:	Deletes the specified user folder.
		 * Label: 		deleteUserFolder
		 * Args:		sessionID
		 */
		case 'deleteUserFolder':
			/* TODO: Add Security: Only .pdb file can be deleted. */

			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				//$util = new Utils();
				//$aResult['result'] = $util->deleteTempPdbFile($_POST['arguments'][0]);
				//$path = '\\temp\\' . $_POST['arguments'][0];
				//$path .= '\\' . $_POST['arguments'][1];
				
				$aResult['result'] = Utils::deleteUserFolder($_POST['arguments'][0]);
				echo $aResult['result'];
			}
			break;
			
/* [END] UTILS CASES -------------------------------------------------------- */

/* [START] SUBMIT NEW JOB CASES -------------------------------------------------------- */

	/**
	 * Description:	Returns the number of sequence table entries. If empty, it will call a script to fill and count again.
	 * Label: 		'countAndFillSequenceTable'
	 * Args:		pdbId  
	 */
         /*
		case 'countAndFillSequenceTable':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();

				$aResult['result'] = $db->countAndFillSequenceTable($_POST['arguments'][0]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;
        */

	/**
	 * Description:	Returns the set of PDB chains.
	 * Label: 		'getPdbChains'
	 * Args:		pdbId  
	 */

		case 'getPdbChains':
                        //console.log("check 3");
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();

				$aResult['result'] = $db->getPdbChains($_POST['arguments'][0]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
                                //echo "howdy hoo hoo you";
			}
			break;

	/**
	 * Description:	Returns the set of residueIds (concatenated residue number + insertion code) for the provided pdbId and chainId. 
	 * Label: 		'getPdbResidueIds'
	 * Args:		pdbId , chainId 
	 */
                case 'getPdbResidueIds':
                        //console.log("check 4");
			if(!is_array($_POST['arguments'])  ){
			//if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();

				//$aResult['result'] = $db->getPdbChains($_POST['arguments'][0]);
				$aResult['result'] = $db->getPdbResidueIds($_POST['arguments'][0], $_POST['arguments'][1]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
                                //echo "howdy hoo hoo you";
			}
			break;



         /**
         * Description:Returns the number of parents found for the given PDB ID. This should include any self-parents. Acceptable results are nonnegative integers. Multiple parents may be OK, if the chains in the complexes are different.

         * Label: 'getPdbPrimary'
         * Args:pdbId  
         */
                case    'getPdbPrimary':
                     if(!is_array($_POST['arguments'])  ){
                         $aResult['error'] = 'Error in arguments!';
                         echo $aResult['error'];
                    }
                    else {
                        $db = new Database(webhost, webusername, webpassword, mmbdatabase);
                        $db->connect();
                        //$aResult['result'] = $db->getPdbPrimary ($_POST['arguments'][0]);
                        if (sizeof($_POST['arguments']) == 1) {
                            $aResult['result'] = $db->getPdbPrimary ($_POST['arguments'][0]);}
                        else if (sizeof($_POST['arguments']) == 3) {
                            $aResult['result'] = $db->getPdbPrimary ($_POST['arguments'][0], $_POST['arguments'][1], $_POST['arguments'][2]);}
                        else {exit(1);} 
                        $db->disconnect();
                        echo json_encode($aResult['result']);
                    }
                    break;

         /**
         * Description:Returns the wild type residue type for the given PDB ID, chain ID, and residue ID. 
         * Label: 'getWildTypeAminoAcidType'
         * Args:pdbId
         */
                case    'getWildTypeAminoAcidType':
                     error_log("getWildTypeAminoAcidType (".$_POST['arguments'][0]. $_POST['arguments'][1]. $_POST['arguments'][2].")\n", 3, "/home/mmb/error.log");
                     if(!is_array($_POST['arguments'])  ){
                         $aResult['error'] = 'Error in arguments!';
                         echo $aResult['error'];
                    }
                    else {
                        error_log("getWildTypeAminoAcidType (".$_POST['arguments'][0]. $_POST['arguments'][1]. $_POST['arguments'][2].")\n", 3, "/var/log/apache2/error.log");
                        $db = new Database(webhost, webusername, webpassword, mmbdatabase);
                        $db->connect();
                        if (sizeof($_POST['arguments']) == 3) {
                            //$aResult['result'] = $db->getPdbResidueIds ($_POST['arguments'][0], $_POST['arguments'][1]);}
                            $aResult['result'] = $db->getWildTypeAminoAcidType ($_POST['arguments'][0], $_POST['arguments'][1], $_POST['arguments'][2]);}
                        else {exit(1);}
                        $db->disconnect();
                        echo json_encode($aResult['result']);
                    }
                    break;/*
		//case 'getPdbChains':
                        //console.log("check 3");
			if(!is_array($_POST['arguments'])) {// || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(webhost, webusername, webpassword, mmbdatabase);
				$db->connect();
				$aResult['result'] = $db->getPdbChains($_POST['arguments'][0]);
				$db->disconnect();
				echo json_encode($aResult['result']);
                                //echo "howdy hoo hoo you";
			}
			break;*/



/* [END] SUBMIT NEW JOB CASES -------------------------------------------------------- */




		default:
			$aResult['error'] = 'Not found function '.$_POST['restcall'].'!';
			//$db->disconnect();
			echo $aResult['error'];
			break;
	}


		/* Deprecated *//*
		case 'getMutString':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();

				$aResult['result'] = $db->getMutString($_POST['arguments'][0]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;
			*/

		/* Deprecated *//*
		case 'getWTString':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();

				$aResult['result'] = $db->getWTString($_POST['arguments'][0]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;
			*/

		/* Deprecated *//*
		case 'getProjectDir':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();

				$aResult['result'] = $db->getProjectDir($_POST['arguments'][0]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;
			*/
		
		/* Deprecated *//*
		case 'getStatus':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 1)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();

				$aResult['result'] = $db->getStatus($_POST['arguments'][0]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;
			*/

		/* Deprecated *//*
		case 'getDDG':
			if(!is_array($_POST['arguments']) || (!count($_POST['arguments']) == 2)){
				$aResult['error'] = 'Error in arguments!';
				echo $aResult['error'];
			}
			else {
				$db = new Database(limshost, limsusername, limspassword, limsdatabase);
				$db->connect();

				$aResult['result'] = $db->getDDG($_POST['arguments'][0], $_POST['arguments'][0]);
				$db->disconnect();
				
				echo json_encode($aResult['result']);
			}
			break;
			*/


}
?>
