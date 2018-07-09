<?php
/** 
 * This class contains general tools needed for the project internal operations.
 * Check below for specific details.
 *
 * @author Anastasios Glaros
 * @version v1.0, 2015-3-10
 */

class Utils
{
	const LOCAL_FPATH = "/home/mmb/www/temp/";
	
	/**
	 * This function is used to delete a file or a folder. For security
	 * reasons it is set to delete files only under 'temp' folder.
	 * @param string pathOrFile
	 * @return boolean
	 */
	public function deletePdbFile($filename){
		$filename = self::LOCAL_FPATH . $filename;
		if(@unlink($filename)){
			//echo "File " . $filename . " : Deleted.<br>";
			return true;
		}
	}
	
	public function deleteUserFolder($folderName){
		stripslashes($folderName);
		if($folderName != NULL){
			$folderName = self::LOCAL_FPATH . $folderName;
			if(($folderName != NULL) && (is_dir($folderName))){
				array_map('unlink', glob("$folderName/*.*"));

				if(rmdir($folderName)){
					//echo "File " . $folderName . " : Deleted.<br>";
					return true;
				}
				else return false;
			}
			else return false;
		}
		else return false;
	}
	
}
?>
