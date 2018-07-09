

<?php

/** 
 * This class provides functionality for database connection
 * and there are used dedicated prepared statements using mysqli.
 * 
 * mysqli Prepared Statements:
 * @link http://php.net/manual/en/mysqli.quickstart.prepared-statements.php
 *
 * @param string $host, string $user, string $password, string $db_name
 * @author Anastasios Glaros
 * @version v1.0, 2015-3-10
 */


class Database
{
	private $db_host;
	private $db_user;
	private $db_pass;
	private $db_name;
	
	// Checks to see if the connection is active
	private $con = false;
	private $myconn;
	
	//private $results_mtx = array();
	
	public function __construct($host, $user, $pass, $db){
		$this->db_host = $host;
		$this->db_user = $user;
		$this->db_pass = $pass;
		$this->db_name = $db;
	}
	
	/**
	 * This function tries to make a connection to the database.
	 * @return boolean
	 */
	public function connect() {
		if(!$this->con){
			//echo "connection not existed.<br>";
                        /*echo "passVord: >$this->db_pass< "; 
                        echo "host    : >$this->db_host< "; 
                        echo "user    : >$this->db_user< "; 
                        echo "database: >$this->db_name< "; */
			$this->myconn = mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
			if (mysqli_connect_errno()) {
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
			}
			
			if($this->myconn){
				$this->con = true;
				//echo "true connect<br>";
				return true;
			}
			else {
				//echo "false connect<br>";
				return false;
			}
		}
		else {
			//echo "true connection exists<br>";
			return true;
		}
	}
	
	/**
	 * This function closes a database connection.
	 * @return boolean
	 */
	public function disconnect() {
		if($this->con){
			if(mysqli_close($this->myconn)){
				$this->con = false;
				//echo "true disconnect<br>";
				return true;
			}
			else {
				//echo "false disconnect<br>";
				return false;
			}
		}
		else {
			//echo "No Connection to disconnect.<br>";
		}
	}

	/* ---------------- ACCOUNT OPERATIONS ---------------- */
	
	/**
	 * This function is used to register a new user.
	 * @param string $email, string $password
	 * @param [optional] string $firstname, string $lastname, string $purpose
	 * @return boolean
	 */
	public function userReg($email, $password, $firstname = null, $lastname = null, $purpose = null){
		
		/* Implement if registered.
		   Return message this email is already registered.
		 */
		
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			//echo "Email is not valid.";
			return false;
		}

		if($firstname != null){
			if(filter_var($firstname, FILTER_SANITIZE_STRING)){
				$firstname = $firstname;
			}else {
				echo "First name input not allowed.";
				return false;
			}
		}

		if($lastname != null){
			if(filter_var($lastname, FILTER_SANITIZE_STRING)){
				$lastname = $lastname;
			}else {
				echo "Last name input not allowed.";
				return false;
			}
		}

		if($purpose != null){
			if(!filter_var($purpose, FILTER_VALIDATE_INT)){
				echo "Purpose input not allowed.";
				return false;
			}
		}

		$passw = md5($password);

		$ins = "INSERT INTO reg_user(`email`, `passwd`";
		$values = "VALUES (?, ?";
		$binds = "ss";
		$case = 'ep';

		if($firstname != null){
			$ins .= ", `fname`";
			$values .= ", ?";
			$binds .= "s";
			$case .= "f";
		}

		if($lastname != null){
			$ins .= ", `lname`";
			$values .= ", ?";
			$binds .= "s";
			$case .= "l";
		}

		if($purpose != null){
			$ins .= ", `purpose`";
			$values .= ", ?";
			$binds .= "i";
			$case .= "p";
		}

		$query = $ins . ") " . $values . ")";

		//echo $query . "<br>";
		//echo $binds . "<br>";
		//echo $case . "<br>";

		//$query = "INSERT INTO reg_user(`email`, `fname`, `lname`, `passwd`, `purpose`) VALUES (?, ?, ?, ?, ?)";
		$this->regstmt = $this->myconn->prepare($query);
		
		switch($case){
			case 'ep':
				$this->regstmt->bind_param($binds, $email, $passw);
				break;

			case 'epf':
				$this->regstmt->bind_param($binds, $email, $passw, $firstname);
				break;
				
			case 'epl':
				$this->regstmt->bind_param($binds, $email, $passw, $lastname);
				break;
			
			case 'epp':
				$this->regstmt->bind_param($binds, $email, $passw, $purpose);
				break;
			
			case 'epfl':
				$this->regstmt->bind_param($binds, $email, $passw, $firstname, $lastname);
				break;
			
			case 'epfp':
				$this->regstmt->bind_param($binds, $email, $passw, $firstname, $purpose);
				break;
			
			case 'eplp':
				$this->regstmt->bind_param($binds, $email, $passw, $lastname, $purpose);
				break;
			
			case 'epflp':
				$this->regstmt->bind_param($binds, $email, $passw, $firstname, $lastname, $purpose);
				break;
				
			default:
				echo "Debugging Message: False Parameters to catch. Cases: " . $case;
				break;
		}

		if($this->regstmt->execute()) {
			$this->putRegDate($email);
			//$this->regstmt->store_result();
			$this->regstmt->close();
			//echo 'true';
			return true;
		}
		else {
			//$this->regstmt->store_result();
			$this->regstmt->close();
			echo "You registration is rejected! Try again.";
			return false;
		}
	}

	/**
	 * This function is private and is used from {@link userReg()}
	 * in order to keep registration date into the database.
	 * @param string $email
	 * @returns boolean
	 */
	private function putRegDate($email){
		$query = "UPDATE reg_user SET regDate = NOW() WHERE email = ?";
		$this->regdstmt = $this->myconn->prepare($query);
		$this->regdstmt->bind_param('s', $email);
		$this->regdstmt->execute();
		
		if($this->regdstmt->errno){
			//echo "Fail to Update: " . $stmt->error;
			$this->regdstmt->close();
			return false;
		}
		else {
			//echo "Updated {$this->regdstmt->affected_rows} rows";
			$this->regdstmt->close();
			return true;
		}
	}

	/**
	 * This function used for a user to login. Checks if the user exists in the system.
	 * @param string $email, string $password
	 * @return boolean
	 */
	public function userLogin($email, $password, $flag=true){
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			//echo "Email is not valid.";
			return false;
		}

		$query = "SELECT email FROM reg_user WHERE email = ? AND passwd = ?";
		$this->loginstmt = $this->myconn->prepare($query);
		$this->loginstmt->bind_param('ss', $email, md5($password));
		$this->loginstmt->execute();
		$this->loginstmt->store_result();

		$numRows = $this->loginstmt->num_rows;
		$this->loginstmt->close();
		if($numRows == 1){
			if($flag){
				$this->putLastLogin($email);
			}
			//echo "true.<br>";
			return true;
		}
		else {
			//echo "false.<br>";
			return false;
		}
	}

	/**
	 * This function is private and is used from {@link userLogin()}
	 * in order to keep login date into the database.
	 * @param string $email
	 * @returns boolean
	 */
	private function putLastLogin($email){
		$query = "UPDATE reg_user SET last_login = NOW() WHERE email = ?";
		$this->llogstmt = $this->myconn->prepare($query);
		$this->llogstmt->bind_param('s', $email);
		$this->llogstmt->execute();
		
		if($this->llogstmt->errno){
			//echo "Fail to Update: " . $stmt->error;
			$this->llogstmt->close();
			return false;
		}
		else {
			//echo "Updated {$this->llogstmt->affected_rows} rows";
			$this->llogstmt->close();
			return true;
		}
	}
	
	public function userInfo($email){
		$query = "SELECT fname, lname, purpose FROM `reg_user` WHERE email = ?";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $email);
		$this->stmt->execute();
		$this->stmt->bind_result($fname, $lname, $purpose);
		$this->stmt->fetch();
		
		$info['email'] = $email;
		$info['fname'] = $fname;
		$info['lname'] = $lname;
		$info['purpose'] = $purpose;

		if($this->stmt->errno){
			//echo "Fail to Update: " . $stmt->error;
			$this->stmt->close();
			return false;
		}
		else {
			//echo "Updated {$this->llogstmt->affected_rows} rows";
			$this->stmt->close();
			return $info;
		}
	}

	public function changePasswd($email, $old_passwd, $new_passwd){
		if(!$this->userLogin($email, $old_passwd, false)){	// We set flag to 'false' because we don't want to keep date as last login.
			echo "Invalid Credentials!";
			return false;
		}
		else {
			$query = "UPDATE `reg_user` SET `passwd`=? WHERE `email`=?";
			$this->cpwdstmt = $this->myconn->prepare($query);
			$this->cpwdstmt->bind_param('ss', md5($new_passwd), $email);
			$this->cpwdstmt->execute();

			if($this->cpwdstmt->errno){
				echo "Fail to Update your Password. Try again.";
				$this->cpwdstmt->close();
				return false;
			}
			else {
				//echo "Your password is successfully updated!";
				$this->cpwdstmt->close();
				return true;
			}
		}
	}

	public function updateUserInfo($email, $fname, $lname, $purpose){
		$query = "UPDATE `reg_user` SET `fname`=?, `lname`=?, `purpose`=? WHERE `email`=?";
		$this->cuistmt = $this->myconn->prepare($query);
		$this->cuistmt->bind_param('ssis', $fname, $lname, $purpose, $email);
		$this->cuistmt->execute();
		
		if($this->cuistmt->errno){
			echo "Fail to Update your Info. Try again.";
			$this->cuistmt->close();
			return false;
		}
		else {
			//echo "Your Info is successfully updated!";
			$this->cuistmt->close();
			return true;
		}
	}
/*
	public function changeFame($email, $fname){
		$query = "UPDATE `reg_user` SET `fname`=? WHERE `email`=?";
		$this->cpwdstmt = $this->myconn->prepare($query);
		$this->cpwdstmt->bind_param('ss', $fname, $lname, $purpose, $email);
		$this->cpwdstmt->execute();
		
		if($this->cpwdstmt->errno){
			echo "Fail to Update your First Name. Try again.";
			$this->cpwdstmt->close();
			return false;
		}
		else {
			//echo "Your First Name is successfully updated!";
			$this->cpwdstmt->close();
			return true;
		}
	}

	public function changeLname($email, $lname){
		$query = "UPDATE `reg_user` SET `lname`=? WHERE `email`=?";
		$this->cpwdstmt = $this->myconn->prepare($query);
		$this->cpwdstmt->bind_param('ss', $lname, $email);
		$this->cpwdstmt->execute();
		
		if($this->cpwdstmt->errno){
			echo "Fail to Update your First Name. Try again.";
			$this->cpwdstmt->close();
			return false;
		}
		else {
			//echo "Your First Name is successfully updated!";
			$this->cpwdstmt->close();
			return true;
		}
	}

	public function changePurpose($email, $purpose){
		$query = "UPDATE `reg_user` SET `purpose`=? WHERE `email`=?";
		$this->cpwdstmt = $this->myconn->prepare($query);
		$this->cpwdstmt->bind_param('is', $purpose, $email);
		$this->cpwdstmt->execute();
		
		if($this->cpwdstmt->errno){
			echo "Fail to Update your Purpose. Try again.";
			$this->cpwdstmt->close();
			return false;
		}
		else {
			//echo "Your Purpose is successfully updated!";
			$this->cpwdstmt->close();
			return true;
		}
	}
*/


	public function evaluation($email, $jobName, $mutation, $status, $note){
		/*if($this->evalExists($email, $jobName, $mutation)){
			$query = "UPDATE `evaluation` SET `note`=?,`status`=?, `changed`=NOW() WHERE `email`=? AND `projectName`=? AND `mutation` =?";
			$this->evstmt = $this->myconn->prepare($query);
			$this->evstmt->bind_param('sisss', $note, $status, $email, $jobName, $mutation);
		}
		else {*/
			$query = "INSERT INTO evaluation(`email`, `projectName`, `mutation`, `status`, `note`, `changed`) VALUES (?, ?, ?, ?, ?, NOW())";
			$this->evstmt = $this->myconn->prepare($query);
			$this->evstmt->bind_param('sssis', $email, $jobName, $mutation, $status, $note);
		//}
		$this->evstmt->execute();
                /*
		if($this->evstmt->errno){
			echo "Fail to Update your Password. Try again.";
			$this->evstmt->close();
			return false;
		}
		else {
			//echo "Your password is successfully updated!";
			$this->evstmt->close();
			return true;
		}*/
	}

	public function getEval($s_email, $jobName,$pdbId, $mut){
		if($this->evalExists($s_email, $jobName, $mut)){
			$query = "SELECT * FROM evaluation WHERE email = ? AND projectName = ? AND pdbId = ? AND mutation = ?";
                        error_log("query : " . $query);
			$this->gestmt = $this->myconn->prepare($query);
                        error_log("s_email = " . $s_email);
                        error_log("jobName = " . $jobName);
                        error_log("pdbId   = " . $pdbId);
                        error_log("mut     = " . $mut);
			$this->gestmt->bind_param('ssss', $s_email, $jobName, $pdbId, $mut);
			$this->gestmt->execute();
			$this->gestmt->store_result();
			$row = $this->fetchAssocStatement($this->gestmt);

			if($this->gestmt->errno){
				//echo "Fail to Update: " . $gestmt->error;
				$this->gestmt->close();
				return false;
			}
			else {
				//echo "Updated {$this->gestmt->affected_rows} rows";
				$this->gestmt->close();
				return $row;
			}

			/*
			$res = $this->gestmt->get_result();
			$info = array();
			while($row = $res->fetch(MYSQLI_ASSOC)) {
				//array_push($info, $row);
				$info = $row;
			}
			$this->gestmt->close();
			//echo json_encode($info);
			return $info;
			*/
		}
		else {
			echo "No user evaluation has been done on this mutation. <br> If you hit \"Load structure\" you can click the \"Evaluate\" button and provide your opinion on this mutation.<br>";
			return false;
		}
	}



	public function evalExists($email, $jobName, $mut){
		$query="SELECT count(*) as numEntries  FROM evaluation where  email = ? AND projectName = ? AND mutation = ? " ;
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('sss',$email, $jobName, $mut);
		$this->stmt->execute();

		$res = $this->stmt->get_result();
		$chains = array();
                $numSequenceTableEntries = 0;
		while($row = $res->fetch_array(MYSQLI_ASSOC)) {
			array_push($chains, $row);
                        error_log("Number of sequence table entries:" . $row['numEntries']);
                        error_log($row['numEntries']);
                        $numSequenceTableEntries = $row['numEntries'];
                        error_log($numSequenceTableEntries);
                        
		}
		$this->stmt->close();
                $errorArray = array();
                if ($numSequenceTableEntries == 0){
                    return false;
                } else { return true; }
                error_log("Check 32");
	}
/*
	private function evalExists($email, $jobName, $mut){
		$query = "SELECT evaluation.email, projectName, mutation FROM evaluation WHERE email = ? AND projectName = ? AND mutation = ?";
		$query = "SELECT count(*) as myCount                     FROM evaluation WHERE email = ? AND projectName = ? AND mutation = ?";
		$this->eexstmt = $this->myconn->prepare($query);
		$this->eexstmt->bind_param('sss', $email, $jobName, $mut);
		$this->eexstmt->execute();
                error_log("in evalExists : query = " . $query);
                $res = $this->eexstmt->get_result();
                $resultArray = array();
                $row         = array();

                while($row = $res->fetch_array(MYSQLI_ASSOC)) {
                    array_push($resultArray, $row); 
                    error_log("row contains : " . $row);
                    error_log("row contains implode : " . implode("|", $row));
                    error_log("Number of evaluation table entries : " . $row['myCount']);
                    $numEvaluations = $row['myCount'];
                    error_log("Number of evaluation table entries as evaluated : " . $numEvaluations);
                }
                error_log("Number of rows in resultArray = " . sizeof ($resultArray) );             
                if ($numEvaluations == 0) {
                    error_log("Number of evaluation table entries, should be zero : " . strval($numEvaluations) );
                    return false;
                } else {
                    error_log("Number of evaluation table entries, final : " . strval($numEvaluations) );
                    return true;
                } 
*/
/*
		$this->eexstmt->store_result();

		$numRows = $this->eexstmt->num_rows;
		$this->eexstmt->close();
                error_log("check 35 $numRows = " . $numRows );
		if($numRows == 1){
			return true;
		}
		else {
			return false;
		}
*/
	//}
	
	public function getEvalList($email){
		$query = "SELECT e.projectName, e.mutation, e.note, s.status, e.changed
					FROM `evaluation` as e, `status` as s;
                                        // SCF liberalized this query. Was:
					//ROM `evaluation` as e, `status` as s
					//WHERE e.email=? AND s.id=e.status";
                                        // note this means there is no privacy left!

		$this->gelstmt = $this->myconn->prepare($query);
		$this->gelstmt->bind_param('s', $email);
		$this->gelstmt->execute();
		$this->gelstmt->store_result();

		$this->gelstmt->bind_result($arr['projectName'], $arr['mutation'], $arr['note'], $arr['status'], $arr['changed']);
		$info = array();

		$i = 0;
		while($this->gelstmt->fetch()) {
			$info[$i]['projectName'] = $arr['projectName'];
			$info[$i]['mutation'] = $arr['mutation'];
			$info[$i]['note'] = $arr['note'];
			$info[$i]['status'] = $arr['status'];
			$info[$i]['changed'] = $arr['changed'];
			$i++;
		}
		$this->gelstmt->close();
		return $info;
	}


/*        public function getEvalList($email){
                $query = "SELECT e.projectName, e.mutation, e.note, s.status, e.changed
                                        FROM `evaluation` as e, `status` as s
                                        WHERE e.email=? AND s.id=e.status";

                $this->gelstmt = $this->myconn->prepare($query);
                $this->gelstmt->bind_param('s', $email);
                $this->gelstmt->execute();

                $this->gelstmt->execute();

                $res = $this->gelstmt->get_result();
                $info = array();

                while($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        array_push($info, $row);
                }
                $this->gelstmt->close();
                return $info;
        }
*/

	/* --- ACCOUNT OPERATIONS [end] --- */


	private function fetchAssocStatement($stmt) {
		if($stmt->num_rows>0) {
			$result = array();
			$md = $stmt->result_metadata();
			$params = array();
			while($field = $md->fetch_field()) {
				$params[] = &$result[$field->name];
			}
			call_user_func_array(array($stmt, 'bind_result'), $params);
			if($stmt->fetch())
				return $result;
		}

		return null;
	}


	/**
	 * This function uses a predefined query to access jobInfo table.
	 * @param string $jobName
	 * @return json
	 */
	public function getJobInfo($jobName){
		$query = "SELECT * FROM `jobInfo` WHERE runID = ?";
		//$query = "SELECT * FROM `jobinfo` WHERE runID = ?";
		//$query = "SELECT `batch_directory` FROM `jobinfo` WHERE runID = ?";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $jobName);
		$this->stmt->execute();

		$this->stmt->store_result();
		//$res = $this->stmt->get_result();
		//$info = array();
		//while($row = $res->fetch_array(MYSQLI_ASSOC)){
		//	array_push($info, $row);
		//}
		$row = $this->fetchAssocStatement($this->stmt);

		$this->stmt->close();
		return $row;
	}

	/**
	 * This function uses a predefined query to access submittedHomologs table.
	 * It fetches the primary or parent PDB ID .
	 * @param string $homologPdbId
	 * @return string
	 */


        public function getPrimaryPdbId($homologPdbId, $homologComplexString){
                // will eventually have to take complexHomolog argument as well
                $query = "SELECT distinct pdbPrimary FROM `submittedHomologs` WHERE pdbHomolog = ? and  complexHomolog = ? ";
                $this->stmt = $this->myconn->prepare($query);
                $this->stmt->bind_param('ss', $homologPdbId, $homologComplexString);
                $this->stmt->execute();
                $this->stmt->bind_result($pdbPrimary);
                $this->stmt->fetch();
                $this->stmt->close();
                return $pdbPrimary;
        }
	/**
	 * This function uses a predefined query to access submittedHomologs table.
	 * It fetches the primary or parent PDB ID .
	 * @param string $homologPdbId
	 * @return string
	 */


        public function getComplexPrimary($homologPdbId, $homologComplexString){
                // will eventually have to take complexHomolog argument as well
                $query = "SELECT distinct complexPrimary FROM `submittedHomologs` WHERE pdbHomolog = ? and complexHomolog = ? ";
                $this->stmt = $this->myconn->prepare($query);
                $this->stmt->bind_param('ss', $homologPdbId, $homologComplexString);
                $this->stmt->execute();
                $this->stmt->bind_result($complexPrimary);
                $this->stmt->fetch();
                $this->stmt->close();
                return $complexPrimary;
        }
	/**
	 * This function uses a predefined query to access submittedHomologs table.
	 * It fetches the primary or parent PDB ID .
	 * @param string $homologPdbId
	 * @return string
	 */

        public function getMutationStringPrimary($pdbHomolog, $complexHomolog, $mutationStringHomolog){
                // will eventually have to take complexHomolog argument as well
                $query = "SELECT distinct mutationStringPrimary  FROM `submittedHomologs` WHERE pdbHomolog = ? and complexHomolog = ?  and mutationStringHomolog = ? ";
                $this->stmt = $this->myconn->prepare($query);
                $this->stmt->bind_param('sss', $pdbHomolog, $complexHomolog, $mutationStringHomolog);
                $this->stmt->execute();
                $this->stmt->bind_result($mutationStringPrimary);
                $this->stmt->fetch();
                $this->stmt->close();
                return $mutationStringPrimary;
        }


	public function getProjectDir($jobName){
		//$query = "SELECT distinct batch_directory FROM results WHERE jobName = ?";
		$query = "SELECT distinct batch_directory_archive FROM `jobInfo` WHERE runID = ?";

		/* FOR DEBUGGING PURPOSES
		if($this->stmt = $this->myconn->prepare($query)){
			$this->stmt->bind_param('s', $jobName);
			$this->stmt->execute();
			$this->stmt->bind_result($batch_directory_archive);
			$this->stmt->fetch();
			$this->stmt->close();
		}else{
			//error !! don't go further
			echo "<script>console.log( 'Debug Objects: " . var_dump($this->db->error) . "' );</script>";
		}*/

		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $jobName);
		$this->stmt->execute();
		$this->stmt->bind_result($batch_directory_archive);
		$this->stmt->fetch();
		$this->stmt->close();

		return $batch_directory_archive;
		
		/*$batch_directoryArray = array();		
		while ($this->stmt->fetch()) {
			$batch_directoryArray[] = $batch_directory;
		}
		return $batch_directoryArray;
		*/
	}

	/**
	 * This function uses a predefined query to access results table.
	 * It fetches the whole set of the existed project names.
	 * @return array
	 */
	public function getJobNames($cred){
		if($cred === 1){
			// $query = "SELECT distinct jobName FROM results WHERE NOT ISNULL(batch_directory_archive)";
			$query = "SELECT distinct runID FROM jobInfo WHERE NOT ISNULL(batch_directory_archive)";
			$this->stmt = $this->myconn->prepare($query);
		}
		elseif($cred === 0){
			// $query = "SELECT distinct jobName FROM results WHERE private=? AND NOT ISNULL(batch_directory_archive)";
			$query = "SELECT distinct runID FROM jobInfo WHERE private=? AND NOT ISNULL(batch_directory_archive)";
			$this->stmt = $this->myconn->prepare($query);
			$this->stmt->bind_param('i', $cred);
		}

                $this->stmt->execute();
		$this->stmt->bind_result($jobName);

		//$res = $this->stmt->get_result();
		$info = array();

		while($this->stmt->fetch()) {
			$info[] = array("jobName" => $jobName);
		}
		$this->stmt->close();
		return $info;
	}



/*	public function getJobNames($cred){
		if($cred === 1){
			$query = "SELECT distinct jobName FROM results";
			$this->stmt = $this->myconn->prepare($query);
		}
		elseif($cred === 0){
			$query = "SELECT distinct jobName FROM results WHERE private=?";
			$this->stmt = $this->myconn->prepare($query);
			$this->stmt->bind_param('i', $cred);
		}

		$this->stmt->execute();
		//$this->stmt->bind_result($jobName);
		
		$res = $this->stmt->get_result();
		$info = array();

		while($row = $res->fetch_array(MYSQLI_ASSOC)) {
		    array_push($info, $row);
		}
		$this->stmt->close();
		return $info;
*/		
		/*
		$jobNameArray = array();
		
		while ($this->stmt->fetch()) {
			$jobNameArray[] = $jobName;
		}
		$this->stmt->close();
		return $jobNameArray;
		*/
/*
	}
*/


        /**  
         * This function calls functions {@link getDistinctPdbIdsForProject()}. 
         * Then it aggregates the results into
         * an array which is returned as JSON. 
         * @return array 
               
               
         * @todo TODO: Working For JobName --> We Need For Mutations
         */    

        public function getDistinctPdbIdsForProject($jobName){


                $query = "SELECT DISTINCT pdbId , jobName  FROM results WHERE jobName = ? order by pdbId asc "; 
                $this->stmt = $this->myconn->prepare($query);
                $this->stmt->bind_param('s', $jobName);
                $this->stmt->execute();

                $this->stmt->bind_result($myPdbId, $myJobName );
                      
                $myPdbIdArray = array();
                      
                while ($this->stmt->fetch()) {
                        $myPdbIdArray[] = $myPdbId;
                        $myJobNameArray[] = $myJobName;
                }    
                $this->stmt->close();
/*
                query = "SELECT jobName,  DISTINCT pdbId, results  FROM results WHERE jobName = ? order by pdbId asc "; 
                $this->stmt = $this->myconn->prepare($query);
                $this->stmt->bind_param('s', $jobName);
                $this->stmt->execute();
                $this->stmt->bind_result($myPdbId);
                      
                $myPdbIdArray = array();
                      
                while ($this->stmt->fetch()) {
                        $myPdbIdArray[] = $myPdbId[1];
                }    
                $this->stmt->close();
*/
                $pdbInfo[] = array();
                $i = 0;
                foreach($myPdbIdArray as $key => $value){
                        $pdbInfo[$i]['pdbId']   = $myPdbIdArray[$key];
                        $pdbInfo[$i]['jobName'] = $myJobNameArray[$key];
                        $i++;
                }    
                      
                return $pdbInfo;
        }



	/**
	 * This function calls functions {@link getMutString()}, {@link getWTString()},
	 * {@link getStatus()} and {@link getDDG()}. Then it aggregates the results into
	 * an array which is returned as JSON.
	 * @return array
	 
	 
	 * @todo TODO: Working For JobName --> We Need For Mutations
	 */
	public function getMutInfo($jobName){
		$mutString = $this->getMutString($jobName);
		$wtString = $this->getWTString($jobName);
		$status = $this->getStatus($jobName);
	        $pdbId  = $this->getPdbId ($jobName);	
		$mutInfo[] = array();
		$AllMutInfo = array();
		$i = 0;
		
		foreach($mutString as $key => $value){
			$mutInfo[$i]['mutationString'] = $value;
			$mutInfo[$i]['WTString'] = $wtString[$key];
			$mutInfo[$i]['status'] = $status[$key];
			$mutInfo[$i]['ddg'] = $this->getDDG($jobName, $value);
			$mutInfo[$i]['pdbId'] = $pdbId[$key];
			//$AllMutInfo[$i++] = $mutInfo;
			$i++;
		}
		
		return $mutInfo;
	}

	/**
	 * This function calls functions {@link getMutString()}, {@link getWTString()},
	 * {@link getStatus()} and {@link getDDG()}. Then it aggregates the results into
	 * an array which is returned as JSON.
	 * @return array
	 
	 
	 */
	public function getMutInfoFromProjectAndPdb($jobName,$pdbId){
		$mutString = $this->getMutStringFromProjectAndPdb($jobName,$pdbId);
		$wtString  = $this->getWTStringFromProjectAndPdb($jobName,$pdbId);
		$status    = $this->getStatusFromProjectAndPdb($jobName,$pdbId);
	        $pdbId     = $this->getPdbIdFromProjectAndPdb ($jobName,$pdbId);	
                
		$mutInfo[] = array();
		$AllMutInfo = array();
		$i = 0;
		
		foreach($mutString as $key => $value){
			$mutInfo[$i]['mutationString'] = $value;
			$mutInfo[$i]['WTString'] = $wtString[$key];
			$mutInfo[$i]['status'] = $status[$key];
			$mutInfo[$i]['ddg'] = $this->getDDG($jobName, $value);
			$mutInfo[$i]['pdbId'] = $pdbId[$key];
			//$AllMutInfo[$i++] = $mutInfo;
			$i++;
		}
		
		return $mutInfo;
	}

	/**
	 * This function calls functions {@link getMutString()}, {@link getWTString()},
	 * {@link getStatus()} and {@link getDDG()}. Then it aggregates the results into
	 * an array which is returned as JSON.
	 * @return array
	 
	 
	 */
	public function getMutInfoFromHomologPdbAndComplex($jobName,$pdbHomolog,$complexHomolog){
                error_log("check 19");
                error_log($pdbHomolog);
                error_log($complexHomolog);
                error_log("check 19.1");
		$mutString = $this->getMutStringFromHomologPdbAndComplex($jobName,$pdbHomolog,$complexHomolog);
		$wtString  = $this->getWTStringFromHomologPdbAndComplex ($jobName,$pdbHomolog,$complexHomolog);
		$status    = $this->getStatusFromHomologPdbAndComplex   ($jobName,$pdbHomolog,$complexHomolog);
	        $pdbId     = $this->getPdbIdFromHomologPdbAndComplex    ($jobName,$pdbHomolog,$complexHomolog);	
                
		$mutInfo[] = array();
		$AllMutInfo = array();
		$i = 0;
		
                error_log("check 15.0");
		foreach($mutString as $key => $value){
			$mutInfo[$i]['mutationString'] = $value;
                        error_log($value      );
			$mutInfo[$i]['WTString'] = $wtString[$key];
			$mutInfo[$i]['status'] = $status[$key];
			$mutInfo[$i]['ddg'] = $this->getDDG($jobName, $value);
			$mutInfo[$i]['pdbId'] = $pdbId[$key];
                        error_log("check 16");
                        error_log($pdbId[$key]);
			//$AllMutInfo[$i++] = $mutInfo;
			$i++;
		}
		
		return $mutInfo;
	}

	/**
	 * This function gets all results so they can be presented to the user or compiled 
	 * to report a final DDG. 
	 * Returns a 2D array with all results for this job.              
         * @param string $pdbId, string $mutationStringPrimary, string $complexStringPrimary, string $jobName
         * @returns array
	 */
        public function getSynopsisTable($pdbId,$mutationStringPrimary,$complexStringPrimary , $jobName){
                error_log("check 4");
                error_log($pdbId);
                error_log($mutationStringPrimary);
                error_log("check 5");
                $query = "SELECT pdbPrimary,  submittedHomologs.pdbHomolog,submittedHomologs.complexPrimary,   submittedHomologs.complexHomolog,  submittedHomologs.mutationStringPrimary, mutationStringHomolog,  results.complexString, results.mutationString, results.wildTypeString , (foldx_energy - foldx_energy_wild_type) as DDGhomolog from submittedHomologs, results where pdbPrimary = ? and mutationStringPrimary = ? and submittedHomologs.complexPrimary = ? and results.jobName =? AND mutationStringHomolog = mutationString and results.pdbId = submittedHomologs.pdbHomolog  ;";

                $this->gelstmt = $this->myconn->prepare($query);
                $this->gelstmt->bind_param('ssss', $pdbId,$mutationStringPrimary,$complexStringPrimary, $jobName);
		    //$this->gelstmt->execute();

		    $this->gelstmt->execute();

                $res = $this->gelstmt->get_result();
                $info = array();

                while($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        error_log("check 5.5");
                        error_log(json_encode($row));
                        error_log("check 5.6");
                        array_push($info, $row);
                }
                $this->gelstmt->close();
                error_log("check 5.7");
                error_log(json_encode($info));
                return $info;
        }
	/**
	 * This function takes  pdbHomolog, mutationStringHomolog , complexStringHomolog 
	 * as an intermediate result gets pdbPrimary,complexStringPrimary, mutationStringPrimary
	 * Uses this to call getSynopsisTable, passes on the results from that as a 
         * 2D array.              
         * @param string $pdbHomolog, string $mutationStringHomolog, (string $complexStringHomolog,) 
         * @returns array
	 */
        public function getSynopsisTableFromHomolog($pdbHomolog,$mutationStringHomolog,$complexHomolog , $jobName){
                error_log("check 4");
                error_log($pdbHomolog);
                error_log($mutationStringHomolog);
                error_log("check 5");
                $query = "SELECT pdbPrimary,  submittedHomologs.pdbHomolog,submittedHomologs.complexPrimary,   submittedHomologs.complexHomolog,  submittedHomologs.mutationStringPrimary, mutationStringHomolog,  results.complexString, results.mutationString, results.wildTypeString , (foldx_energy - foldx_energy_wild_type) as DDGhomolog from submittedHomologs, results where pdbPrimary = ? and mutationStringPrimary = ? and submittedHomologs.complexPrimary = ? and results.jobName =? AND mutationStringHomolog = mutationString and results.pdbId = submittedHomologs.pdbHomolog  ;";

                $this->gelstmt = $this->myconn->prepare($query);
                $myPrimaryPdbId          = $this->getPrimaryPdbId($pdbHomolog , $complexHomolog);
                $myMutationStringPrimary = $this->getMutationStringPrimary($pdbHomolog,$complexHomolog,$mutationStringHomolog) ;
                $myComplexPrimary        = $this->getComplexPrimary($pdbHomolog,$complexHomolog);
                $this->gelstmt->bind_param('ssss', $myPrimaryPdbId              , $myMutationStringPrimary                                                       , $myComplexPrimary,                             $jobName);
		    //$this->gelstmt->execute();

		$this->gelstmt->execute();

                $res = $this->gelstmt->get_result();
                $info = array();

                while($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        error_log("check 5.5");
                        error_log(json_encode($row));
                        error_log("check 5.6");
                        array_push($info, $row);
                }
                $this->gelstmt->close();
                error_log("check 5.7");
                error_log(json_encode($info));
                return $info;
        }


	/**
	 * This function takes  pdbHomolog, mutationStringHomolog (, complexStringHomolog) 
	 * as an intermediate result gets pdbPrimary,complexStringPrimary, mutationStringPrimary
	 * Uses this to call getSynopsisTable, passes on the results from that as a 
         * 2D array.              
         * @param string $pdbHomolog, string $mutationStringHomolog, (string $complexStringHomolog,) 
         * @returns array
	 */
        public function getSynopsisTableFromHomologOld($pdbHomolog,$mutationStringHomolog){
                $query = "SELECT pdbPrimary, submittedHomologs.mutationStringPrimary, submittedHomologs.complexPrimary from submittedHomologs where pdbHomolog = ? and mutationStringHomolog = ? and submittedHomologs.complexHomolog = ?   ;";
                $query = "SELECT pdbPrimary, mutationStringPrimary, complexPrimary from submittedHomologs where pdbHomolog = '$pdbHomolog' and mutationStringHomolog = '$mutationStringHomolog' and submittedHomologs.complexHomolog = 'A,B' limit 1;";
                //echo $query; 
                $this->gelstmt = $this->myconn->prepare($query);
                //$this->gelstmt->bind_param('sss',  $pdbHomolog, $mutationStringHomolog, "A,B"      );
                //$this->gelstmt->bind_param('ss',   $mutationStringHomolog, "A,B"      );
                $this->gelstmt->execute();

                $res = $this->gelstmt->get_result();
                $info = array();
                
                while($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        error_log("check 1 ");
                        error_log($row['pdbPrimary']); 
                        error_log($row['mutationStringPrimary']); 
                        error_log($row['complexPrimary']); 
                        error_log("check 2 ");
                        $pdbPrimary = ($row['pdbPrimary']); 
                        $mutationStringPrimary =($row['mutationStringPrimary']); 
                        $complexPrimary = ($row['complexPrimary']); 
                        error_log(sizeof($row));   
                        error_log("check 3 ");
                        array_push($info, $row);
                }
                if (sizeof($info)       > 1){
                    console.log("ERROR! The number of results is " + $info.size + " > 1.");
                    exit();
                }
                
                $this->gelstmt->close();
                $returnArray = array();


                $query = "SELECT pdbPrimary,  submittedHomologs.pdbHomolog,submittedHomologs.complexPrimary,   submittedHomologs.complexHomolog,  submittedHomologs.mutationStringPrimary, mutationStringHomolog,  results.complexString, results.mutationString, results.wildTypeString , (foldx_energy - foldx_energy_wild_type) as DDGhomolog from submittedHomologs, results where pdbPrimary = ? and mutationStringPrimary = ? and submittedHomologs.complexPrimary = ?  AND mutationStringHomolog = mutationString and results.pdbId = submittedHomologs.pdbHomolog  ;";

                $this->gelstmt = $this->myconn->prepare($query);
                $this->gelstmt->bind_param('sss', $pdbPrimary,$mutationStringPrimary,$complexPrimary );
		    //$this->gelstmt->execute();

		$this->gelstmt->execute();

                $res = $this->gelstmt->get_result();
                $info = array();

                while($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        error_log("check 5.5");
                        //error_log(json_encode($row));
                        error_log("check 5.6");
                        array_push($info, $row);
                }
                $this->gelstmt->close();
                error_log("check 5.7");
                //error_log(json_encode($info));
                //$returnArray = $this->getSynopsisTable("1A22", "A-174-A", "A,B"    , "homoScan.1");
                error_log("hellowww");
                //error_log($info);
                error_log(var_dump($info));
                // problem is e.g. $row[0] contains NULL .. fix this. maybe wrong row?
                //$returnArray = $this->getSynopsisTable($pdbPrimary , $mutationStringPrimary , $complexPrimary , "homoScan.1");
                //$returnArray = $info
                error_log("check 6");
                //error_log(json_encode($returnArray));
                error_log("check 6.1");
                //error_log($returnArray[0]['pdbPrimary']);
                error_log("check 7");
                return $info;
                //return json_encode($info);
                //return  $this->getSynopsisTable($pdbPrimary , $mutationStringPrimary , $complexPrimary , "homoScan.1");
                //return json_encode($returnArray);
                //return $returnArray ; //$mutString = $this->getMutString($jobName);
                //return $info;
        }
	/**
	 * This function is private and is used from {@link getMutInfo()}
	 * in order retrieve mutation names of the specified project.
	 * @param string $jobName
	 * @returns array
	 */
	private function getMutString($jobName){
		$query = "SELECT mutationString FROM results WHERE jobName = ?";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $jobName);
		$this->stmt->execute();
		$this->stmt->bind_result($mutationString);
		
		$mutationStringArray = array();
		
		while ($this->stmt->fetch()) {
			$mutationStringArray[] = $mutationString;
		}
		$this->stmt->close();
		return $mutationStringArray;
	}
	private function getMutStringFromHomologPdbAndComplex($jobName,$pdbHomolog,$complexHomolog){
		$query = "SELECT mutationString FROM results WHERE jobName = ? AND pdbId = ? and complexString = ?  order by mutationString asc";
                error_log("check 15.1");
                error_log($query);
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('sss',$jobName, $pdbHomolog, $complexHomolog);
		$this->stmt->execute();
		$this->stmt->bind_result($mutationString);
		
		$mutationStringArray = array();
		
		while ($this->stmt->fetch()) {
                        error_log("check 15.3");
			$mutationStringArray[] = $mutationString;
                        error_log($mutationString);
		}
		$this->stmt->close();
                error_log("check 15.5");
		return $mutationStringArray;
	}
	private function getMutStringFromProjectAndPdb($jobName,$pdbId){
		$query = "SELECT mutationString FROM results WHERE jobName = ? AND pdbId = ? order by mutationString asc";
		$this->stmt = $this->myconn->prepare($query);
                //console.log("howdy");
		$this->stmt->bind_param('ss',$jobName, $pdbId);
		$this->stmt->execute();
		$this->stmt->bind_result($mutationString);
		
		$mutationStringArray = array();
		
		while ($this->stmt->fetch()) {
			$mutationStringArray[] = $mutationString;
		}
		$this->stmt->close();
		return $mutationStringArray;
	}

	/**
	 * This function is private and is used from {@link getMutInfo()}
	 * in order retrieve WildType names of the specified project.
	 * @param string $jobName
	 * @returns array
	 */
	private function getWTString($jobName){
		$query = "SELECT wildTypeString FROM results WHERE jobName = ?";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $jobName);
		$this->stmt->execute();
		$this->stmt->bind_result($wildTypeString);
		
		$wildTypeStringArray = array();
		
		while ($this->stmt->fetch()) {
			$wildTypeStringArray[] = $wildTypeString;
		}
		$this->stmt->close();
		return $wildTypeStringArray;
	}
	/**
	 * This function is private and is used from {@link getMutInfoFromProjectAndPdb()}
	 * in order retrieve WildType names of the specified project.
	 * @param string $jobName
	 * @param string $pdbId
	 * @returns array
	 */
	private function getWTStringFromProjectAndPdb($jobName,$pdbId){
		$query = "SELECT wildTypeString FROM results WHERE jobName = ? AND pdbId = ? order by mutationString asc";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('ss', $jobName, $pdbId);
		$this->stmt->execute();
		$this->stmt->bind_result($wildTypeString);
		
		$wildTypeStringArray = array();
		
		while ($this->stmt->fetch()) {
			$wildTypeStringArray[] = $wildTypeString;
		}
		$this->stmt->close();
		return $wildTypeStringArray;
	}

	/**
	 * This function is private and is used from {@link getMutInfoFromProjectAndPdb()}
	 * in order retrieve WildType names of the specified project.
	 * @param string $jobName
	 * @param string $pdbId
	 * @returns array
	 */
	private function getWTStringFromHomologPdbAndComplex($jobName,$pdbHomolog, $complexHomolog){
		$query = "SELECT wildTypeString FROM results WHERE jobName = ? AND pdbId = ? and complexString = ? order by mutationString asc";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('sss', $jobName, $pdbHomolog, $complexHomolog);
		$this->stmt->execute();
		$this->stmt->bind_result($wildTypeString);
		
		$wildTypeStringArray = array();
		
		while ($this->stmt->fetch()) {
			$wildTypeStringArray[] = $wildTypeString;
		}
		$this->stmt->close();
		return $wildTypeStringArray;
	}

	/**
	 * This function is private and is used from {@link getMutInfo()}
	 * in order retrieve the statuses of the specified project mutations.
	 * @param string $jobName
	 * @returns array
	 */
	private function getStatus($jobName){
		$query = "SELECT status FROM results WHERE jobName = ?";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $jobName);
		$this->stmt->execute();
		$this->stmt->bind_result($status);
		
		$statusArray = array();
		
		while ($this->stmt->fetch()) {
			$statusArray[] = $status;
		}
		$this->stmt->close();
		return $statusArray;
	}
	/**
	 * This function is private and is used from {@link getMutInfoFromProjectAndPdb()}
	 * in order retrieve the statuses of the specified project mutations.
	 * @param string $jobName
	 * @param string $pdbId
	 * @returns array
	 */
	private function getStatusFromProjectAndPdb($jobName,$pdbId){
		$query = "SELECT status FROM results WHERE jobName = ? AND pdbId = ? order by mutationString asc";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('ss', $jobName, $pdbId);
		$this->stmt->execute();
		$this->stmt->bind_result($status);
		
		$statusArray = array();
		
		while ($this->stmt->fetch()) {
			$statusArray[] = $status;
		}
		$this->stmt->close();
		return $statusArray;
	}

	/**
	 * This function is private and is used from {@link getMutInfoFromHomologPdbAndComplex()}
	 * in order retrieve the statuses of the specified project mutations.
	 * @param string $jobName
	 * @param string $pdbHomolog
	 * @param string $complexHomolog
	 * @returns array
	 */
	private function getStatusFromHomologPdbAndComplex($jobName,$pdbHomolog, $complexHomolog){
		$query = "SELECT status FROM results WHERE jobName = ? AND pdbId = ? and complexString = ? order by mutationString asc";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('sss', $jobName, $pdbHomolog, $complexHomolog);
		$this->stmt->execute();
		$this->stmt->bind_result($status);
		
		$statusArray = array();
		
		while ($this->stmt->fetch()) {
			$statusArray[] = $status;
		}
		$this->stmt->close();
		return $statusArray;
	}



	/**
         * SCF created based on getDDG  
	 * This function is private 
	 * in order retrieve the pdb of the specified project and mutation. 
         * Mutation might not be necessary, since jobName seems to have pdbId rolled into it somehow.
         * Actually probably should be able to do it with no query at all, for the same reason.
	 * @param string $jobName, mutationString
	 * @should return some sort of string
	 */
	private function getPdbId($jobName ){
		$query = "SELECT pdbId  FROM results WHERE jobName = ?";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $jobName);
		$this->stmt->execute();
		$this->stmt->bind_result($myPdbId);
		
		$myPdbIdArray = array();
		
		while ($this->stmt->fetch()) {
			$myPdbIdArray[] = $myPdbId;
		}
		$this->stmt->close();
		return $myPdbIdArray;

	}


	/* --- TABLE 'submittedHomologs' OPERATIONS [start] --- */


        public function getPdbPrimary($pdbId, $complex1 = "", $complex2 = ""){
                if (($complex1 == "") && ($complex2 == "")){
                    $query="SELECT pdbPrimary , complexPrimary FROM submittedHomologs where pdbHomolog = ?  group by pdbPrimary, complexPrimary order by pdbPrimary, complexPrimary " ;
                    error_log("Check 31 " . $query);
                    $this->stmt = $this->myconn->prepare($query);
                    $this->stmt->bind_param('s', $pdbId );
                }
                else {
                    if (($complex1 == "") || ($complex2 == "")){exit("One of the two complexes is empty! Exiting now.");}
                    $complexStringA = $complex1 . "," . $complex2;
                    $complexStringB = $complex2 . "," . $complex1;
                    $query="SELECT pdbPrimary , complexPrimary FROM submittedHomologs where pdbHomolog = ? and (complexHomolog = ? OR complexHomolog = ?)   group by pdbPrimary, complexPrimary order by pdbPrimary, complexPrimary " ;
                    $this->stmt = $this->myconn->prepare($query);
                    $this->stmt->bind_param('sss', $pdbId, $complexStringA , $complexStringB );
                }

/*
	public function getPdbPrimary($pdbId){
		$query="SELECT pdbPrimary FROM submittedHomologs where pdbHomolog = ?  group by pdbPrimary, complexPrimary order by pdbPrimary, complexPrimary " ;
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $pdbId  );
*/

		$this->stmt->execute();
		$res = $this->stmt->get_result();
		$chains = array();
		while($row = $res->fetch_array(MYSQLI_ASSOC)) {
			array_push($chains, $row);
		}
		$this->stmt->close();
		return $chains;
	}

        /**
         * This function gets all complexHomolog associated with pdbHomolog in table submittedHomologs.
         * Args:                pdbHomolog
         * @return array
         */
        public function getComplexesFromPdb($pdbHomolog){
                
                $query = "SELECT distinct(complexHomolog)  FROM submittedHomologs, results  WHERE pdbHomolog = ? and submittedHomologs.complexHomolog = results.complexString and not isnull(results.foldx_energy)"; 
                $this->stmt = $this->myconn->prepare($query);
                $this->stmt->bind_param('s', $pdbHomolog);
                $this->stmt->execute();
                $this->stmt->bind_result($myComplexHomolog);
                      
                $myComplexHomologArray = array();
                      
                while ($this->stmt->fetch()) {
                        $myComplexHomologArray[] = $myComplexHomolog;
                }    
                $this->stmt->close();
                $returnArray[] = array();
                error_log("check 10");

		$i = 0;
                foreach( $myComplexHomologArray as $key => $value){
                    $returnArray[$i]['complexHomolog'] = $value;
                    $i++;    
                    error_log($value);                                         
                } 
                return $returnArray;

        }

	/* --- TABLE 'sequence' OPERATIONS [start] --- */

	public function countAndFillSequenceTable($pdbId){
		$query="SELECT count(*) as numEntries  FROM sequence where pdbId = ?" ;
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $pdbId  );
		$this->stmt->execute();

		$res = $this->stmt->get_result();
		$chains = array();
                $numSequenceTableEntries = 0;
		while($row = $res->fetch_array(MYSQLI_ASSOC)) {
			array_push($chains, $row);
                        error_log("Number of sequence table entries:" . $row['numEntries']);
                        error_log($row['numEntries']);
                        $numSequenceTableEntries = $row['numEntries'];
                        error_log($numSequenceTableEntries);
                        
		}
                $errorArray = array();
                if ($numSequenceTableEntries == 0){
                    exec(("export LD_LIBRARY_PATH=/usr/local/SimTK/lib:/usr/local/SimTK/lib64:/home/sam/svn/RNAToolbox/trunk/build/:/home/sam/svn/breeder/build ; /home/sam/svn/breeder/build/breeder   -PDBID " . $pdbId . "  -SEQUENCE -DATABASE mmb    -SQLSERVER localhost -SQLEXECUTABLE /usr/bin/mysql -SQLPASSWORD m1sQ1P@ssw0rd -USER root -SQLUSER root  -SQLSYSTEM MySQL  -WORKINGDIRECTORY /data//runs/homoScan.1/" . $pdbId . "/"), $errorArray);
                } 
                error_log("Check 32");
                for ($i = (sizeof($errorArray) -10); $i < sizeof($errorArray); $i++){ 
                    error_log($errorArray[$i]);
                }
		$this->stmt->close();
		return $numSequenceTableEntries;
	}

	public function getPdbChains($pdbId){
                $this->countAndFillSequenceTable($pdbId); // Run this first. It counts the entries in the "sequence" table. If this is zero, it calls a script to fill it.
		$query="SELECT distinct chainId FROM sequence where pdbId = ?" ;
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $pdbId  );
		$this->stmt->execute();

		$res = $this->stmt->get_result();
		$chains = array();

		while($row = $res->fetch_array(MYSQLI_ASSOC)) {
			array_push($chains, $row);
		}
		$this->stmt->close();
		return $chains;
	}


        // This returns an array of residueID strings for the given pdbId and chainId
        public function getPdbResidueIds($pdbId, $chainId){
                // The CONCAT statement fuses pdbResidueNumber (always an integer) with insertionCode (always a single-character) to form a string.
                $query="SELECT CONCAT(pdbResidueNumber, insertionCode) as residueId FROM sequence where pdbId = ? AND chainId = ?  ORDER by renumberedResidueNumber asc " ;
                //$query="SELECT CONCAT(pdbResidueNumber, insertionCode) as residueId FROM sequence where pdbId = ? AND chainId ='A' ORDER by renumberedResidueNumber asc " ;
                $this->stmt = $this->myconn->prepare($query);
                $this->stmt->bind_param('ss', $pdbId, $chainId );
                $this->stmt->execute();

                $res = $this->stmt->get_result();
                $residueIds = array();

                while($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        array_push($residueIds, $row);
                }
                $this->stmt->close();
                return $residueIds;
        }


        /**
         * Returns the wild type residue type. Takes a PDB ID, chain ID, and residue ID.
         * @return string
         */


        public function getWildTypeAminoAcidType($pdbId, $chainId, $residueId){
                $query = "SELECT aminoAcidType FROM sequence WHERE pdbId = ? AND chainId = ? AND CONCAT(pdbResidueNumber, insertionCode) = ? ";
                error_log($query."\n", 3, "/var/log/apache2/error.log"); 
                $this->stmt = $this->myconn->prepare($query);
                $this->stmt->bind_param('sss', $pdbId, $chainId, $residueId);
                $this->stmt->execute();
                $res = $this->stmt->get_result();
                $wildTypeAminoAcidTypes = array();
                while($row = $res->fetch_array(MYSQLI_ASSOC)) {
                        array_push($wildTypeAminoAcidTypes, $row);
                }
                $this->stmt->close();
                return $wildTypeAminoAcidTypes;
        }


	/* --- TABLE 'sequence' OPERATIONS [end] --- */

	/**
         * SCF created based on getDDG  
	 * This function is private 
	 * in order retrieve the pdb of the specified project and mutation. 
         * Mutation might not be necessary, since jobName seems to have pdbId rolled into it somehow.
         * Actually probably should be able to do it with no query at all, for the same reason.
	 * @param string $jobName, mutationString
	 * @should return some sort of string
	 */
	private function getPdbIdFromProjectAndPdb($jobName, $pdbId ){
		$query = "SELECT pdbId  FROM results WHERE jobName = ? and pdbId = ? order by mutationString asc";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('ss', $jobName, $pdbId);
		$this->stmt->execute();
		$this->stmt->bind_result($myPdbId);
		
		$myPdbIdArray = array();
		
		while ($this->stmt->fetch()) {
			$myPdbIdArray[] = $myPdbId;
		}
		$this->stmt->close();
		return $myPdbIdArray;

	}

	/**
         * SCF created based on getDDG  
	 * This function is private 
	 * in order retrieve the pdb of the specified project and mutation. 
         * Actually probably should be able to do it with no query at all, for the same reason.
         * Though it may seem silly to use the pdb to get a list of the pdb's, I want to force it to give me a list of the same length as the mutationString list
	 * @param string $jobName, $pdbHomolog, $complexHomolog
	 * @should return some sort of string
	 */

	private function getPdbIdFromHomologPdbAndComplex($jobName, $pdbHomolog, $complexHomolog ){
		$query = "SELECT pdbId  FROM results WHERE jobName = ? and pdbId = ? and complexString = ? order by mutationString asc";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('sss', $jobName, $pdbHomolog, $complexHomolog);
		$this->stmt->execute();
		$this->stmt->bind_result($myPdbId);
		
		$myPdbIdArray = array();
		
		while ($this->stmt->fetch()) {
			$myPdbIdArray[] = $myPdbId;
		}
		$this->stmt->close();
		return $myPdbIdArray;

	}

	/**
         * SCF created based on getDDG  
	 * This function is private 
	 * in order retrieve the pdb of the specified project and mutation. 
         * Mutation might not be necessary, since jobName seems to have pdbId rolled into it somehow.
         * Actually probably should be able to do it with no query at all, for the same reason.
	 * @param string $jobName, mutationString
	 * @should return some sort of string
	 */
	private function getHomologPdbIds($jobName ){
		$query = "SELECT pdbId  FROM results WHERE jobName = ?";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('s', $jobName);
		$this->stmt->execute();
		$this->stmt->bind_result($status);
		
		$statusArray = array();
		
		while ($this->stmt->fetch()) {
			$statusArray[] = $status;
		}
		$this->stmt->close();
		return $statusArray;

	}

	/**
	 * This function is private and is used from {@link getMutInfo()}
	 * in order retrieve the DDG of the specified project mutation.
	 * @param string $jobName, mutationString
	 * @returns float
	 */
	private function getDDG($jobName, $mutationString){
		$query = "SELECT foldx_energy - foldx_energy_wild_type as ddg FROM results WHERE jobName = ? AND mutationString = ?";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->bind_param('ss', $jobName, $mutationString);
		$this->stmt->execute();
		$this->stmt->bind_result($ddg);
		$this->stmt->fetch();
		$this->stmt->close();
		return $ddg;
	}




/*	public function getPurp(){
		$query="SELECT * FROM purpose";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->execute();

		$res = $this->stmt->get_result();
		$purp = array();

		while($row = $res->fetch_array(MYSQLI_ASSOC)) {
			array_push($purp, $row);
		}
		$this->stmt->close();
		return $purp;
	}
*/
	public function getPurp(){
		$query="SELECT id, type FROM purpose";
		$this->stmt = $this->myconn->prepare($query);
		$this->stmt->execute();

		$this->stmt->bind_result($id, $type);
		$purp = array();

		while($this->stmt->fetch()) {
			$purp[] = array("type" => $type, "id" => $id);
                }
                $this->stmt->close();
                return $purp;
	}



	private function tableExists($table){
		
		$tablesInDb = $this->myconn->query('SHOW TABLES FROM '.$this->db_name.' LIKE "'.$table.'"');
        if($tablesInDb)
        {
            if(mysqli_num_rows($tablesInDb) == 1)
            {
				//echo 'done.<br>';
                return true; 
            }
            else
            {
				//echo 'None.<br>';
                return false; 
            }
        }
		
		/*
		$query = "SHOW TABLES FROM `?` LIKE ?";
		$tblstmt = $this->myconn->prepare($query) or trigger_error($this->myconn->error);

		$tblstmt->bind_param('ss', $this->db_name, $table);
		if ( false===$tblstmt) {
			printf("error: %s\n", mysqli_error($this->myconn));
		}
		else {
			echo 'done.';
		}
		$tblstmt->execute();
		$tblstmt->store_result();
		$numTablesInDB = $tblstmt->num_rows;
		
		if($numTablesInDB == 1) {
			echo "true<br>";
			return true;
		}
		else {
			echo "false<br>";
			return false;
		}*/
	}
	
	/*
	public function select($table, $rows = '*', $where = null, $order = null) {
		$q = 'SELECT ' .$rows. ' FROM ' . $table;
        if($where != null)
            $q .= ' WHERE ' . $where;
        if($order != null)
            $q .= ' ORDER BY ' . $order;

        if($this->tableExists($table)){
			$query = mysqli_query($this->myconn, $q);
			if($query){
				$this->myconn->numResults = mysqli_num_rows($query);
				for($i = 0; $i < $this->myconn->numResults; $i++){
					$r = mysqli_fetch_array($query);
					$key = array_keys($r); 
					for($x = 0; $x < count($key); $x++){
						// Sanitizes keys so only alphavalues are allowed
						if(!is_int($key[$x])){
							if(mysqli_num_rows($query) > 1)
								$this->myconn->results_mtx[$i][$key[$x]] = $r[$key[$x]];
							else if(mysqli_num_rows($query) < 1)
								$this->myconn->results_mtx = null; 
							else
								$this->myconn->results_mtx[$key[$x]] = $r[$key[$x]]; 
						}
					}
				}            
				return true; 
			}
			else{
				return false; 
			}
        }
		else {
			//echo "No such table.<br>";
			return false;
		}
    }

	public function insert($table, $values, $rows = null) {
		if($this->tableExists($table)){
            $insert = 'INSERT INTO '.$table;
			
            if($rows != null){
                $insert .= ' ('.$rows.')'; 
            }
 
            for($i = 0; $i < count($values); $i++){
                if(is_string($values[$i]))
					$values[$i] = '"'.$values[$i].'"';
            }
            $values = implode(',',$values);
			
            $insert .= ' VALUES ('.$values.')';
            $ins = mysqli_query($this->myconn, $insert);
			
            if($ins){
				echo "Successful insertion.<br>";
                return true; 
            }
            else{
				//echo "Unsuccessful insertion.<br>";
                return false;
            }
        }
		else {
			echo "No such table.<br>";
			return false;
		}
    }

	public function update($table, $rows, $where) {
		if($this->tableExists($table)){
			
            // Parse the where values
            // even values (including 0) contain the where rows
            // odd values contain the clauses for the row
            for($i = 0; $i < count($where); $i++){
                if($i%2 != 0){
                    if(is_string($where[$i])){
                        if(($i+1) != null)
                            $where[$i] = '"' . $where[$i] . '" AND ';
                        else
                            $where[$i] = '"' . $where[$i] . '"';
                    }
                }
            }
            $where = implode('=', $where);
             
             
            $update = 'UPDATE ' . $table . ' SET ';
            $keys = array_keys($rows); 
            for($i = 0; $i < count($rows); $i++){
                if(is_string($rows[$keys[$i]])){
                    $update .= $keys[$i] . '="' . $rows[$keys[$i]] . '"';
                }
                else{
                    $update .= $keys[$i] . '=' . $rows[$keys[$i]];
                }
                 
                // Parse to add commas
                if($i != count($rows) - 1){
                    $update .= ','; 
                }
            }
            $update .= ' WHERE ' . $where;
			echo $update;
            $query = mysqli_query($this->myconn, $update);
			
            if($query){
				echo "Successful update.<br>";
				echo $query;
                return true; 
            }
            else{
				//echo "Unsuccessful update.<br>";
                return false; 
            }
        }
        else{
			echo "No such table.<br>";
            return false;
        }
	}
	public function delete() {
		echo "Under Construction.<br>";
	}

	public function getResult()
    {
		if(!isset($this->myconn->results_mtx)){
			//echo "Not set.<br>";
			return null;
		}
		else {
			//echo "Set.<br>";
			return $this->myconn->results_mtx;
		}
    }
	*/
}

