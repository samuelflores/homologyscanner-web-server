<?php
error_log("check 50");
define('myLibPath' , "/usr/lib/x86_64-linux-gnu/blas:/home/sam/svn/breeder/build:/usr/local/lib"); // libs for homologyScanner, to be used for running jobs, either in SLURM or directly in the foreground. "define" is superior in this case to simply assigning wiht "=" because the former is a constant that is defined globally. 
error_log("check 51");
define('coderootdirectory','/home/sam/github/homologyscanner-web-server'); // This needs to be separate from webrootdirectory, because it will hold the logins and passwords for mysql. Coul    d not define in dtypes.php, because that is precisely one of the files that needs to be hidden from the web accessible directories.
error_log("check 52");
include (coderootdirectory."/dtypes.php"); // location of dtypes.php, which in turn has the not-safe config variables.
error_log("check 55");
?>
