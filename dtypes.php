<?php
if(!defined('flag')) {
   die();
}
if(count(get_included_files()) ==1) exit("DEEP NO!");

define('key1', 'localhost');			// rembrandt host name
define('key2', 'root');						// rembrandt username
define('key3', 'm1sQ1P@ssw0rd');			// password
define('key4', 'mmb');							// database
define('key5', 'tintin.uppmax.uu.se');			// scp host
define('key6', 'kallebus89');					// scp password
define('key7', 22);								// scp port
define('key8', 'fredrw');						// scp username
define('key9', 'localhost');					// host
define('key10', 'root');						// user
define('key11', 'm1sQ1P@ssw0rd');					// password
define('key12', 'lims_project');				// database
// SCF defined 11 Sept 2018
// Use these instead of key1,key2,key3,key4
define('webhost', 'localhost'); // global database host name 
define('webusername', 'mmbcgi'); // web server username for mysql
define('webpassword', 'mMBc9IU5@r'); 
define( 'mmbdatabase', 'mmb');          
// instead of key12:
define('webrootdirectory','/var/www/html');
define('limshost','localhost');      // in place of key9
define('limsusername','mmbcgi');     // in place of key10
define('limspassword','mMBc9IU5@r'); // in place of key11 
define('limsdatabase', 'lims_project'); // in place of key12
define('administratorEmail', 'samuel.flores@scilifelab.se');
?>
