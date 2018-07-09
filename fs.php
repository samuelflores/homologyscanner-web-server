<?php

/** 
 * This class provides functionality for secure FileServer connection
 * through SSH and SCP protocols. Also, there are used dedicated prepared
 * statements using mysqli.
 *
 * For the SSH and SCP connections phpseclib is used.
 * @link http://phpseclib.sourceforge.net/
 * 
 * mysqli Prepared Statements:
 * @link http://php.net/manual/en/mysqli.quickstart.prepared-statements.php
 *
 * @param string $host, string $user, string $password
 * @author Anastasios Glaros
 * @version v1.0, 2015-3-10
 */


set_include_path(__DIR__ . '/../contents/phpseclib0.3.10/');
require_once('Crypt/RSA.php');
require_once('Net/SSH2.php');
require_once('Net/SCP.php');

class FileServer
{
	private $fs_host;
	private $fs_user;
	private $fs_pass;
	//private $fs_port;
	
	private $con = false;	// Keeping track of if the connection is active or not
	private $fsssh;
	private $fsscp;
	
	//private $results_mtx = array();
	
	public function __construct($host, $user, $pass){
		$this->fs_host = $host;
		$this->fs_user = $user;
		$this->fs_pass = $pass;
		//$this->fs_port = $port;
	}

	/**
	 * This function establishes a secure SSH connection with the remote
	 * file server.
	 * @return boolean
	 */
	public function ssh_connection() {
		if(!$this->con){
			//echo "connection not existed.<br>";
			$this->fsssh = new Net_SSH2($this->fs_host);
			if(!$this->fsssh->login($this->fs_user, $this->fs_pass)){
				echo "SSH: Fail to login.<br>";
				return false;
			}
			
			if($this->fsssh){
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
			echo "SSH connection exists.<br>";
			return true;
		}
	}

	/**
	 * This function transfers a file from the remote file server to a local
	 * folder using a SCP connection.
	 * @param string $remotePathAndFile, string $localPathAndFile
	 * @return boolean
	 */
	public function scp_bring($remoteFile, $localFile){
		$this->fsscp = new Net_SCP($this->fsssh);

		if(!$this->fsscp->get($remoteFile, $localFile)){
			//echo "SCP: Fail retrieving file.<br>";
			return false;
		}
		else {
			//echo "Successful File Transfer. Path: " . $localFile;
			return true;
		}
	}

	/**
	 * This function closes an SSH connection.
	 * @return boolean
	 */
	public function disconnect() {
		if($this->fsssh->isConnected()){
			if($this->fsssh->disconnect()){
				$this->con = false;
				return true;
			}
			else return false;
		}
		else {
			echo "No Connection to disconnect.<br>";
		}
	}
	
	public function __destruct(){
		//echo "destructed";
	}
}
?>