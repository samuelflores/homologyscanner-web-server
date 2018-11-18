<?php
         include('calls.php'); 
         //echo "about to getJobNames\n";
         $cred = 1;
         $db = new Database(webhost, webusername, webpassword, mmbdatabase);
         //$db = new Database(key1, key2, key3, key4);
         $db->connect();
         $result['result'] = $db->getJobNames($cred);
         $db->disconnect();
         $myArray = array(
             "foo" => "bar",
             "bar" => "foo",
         );
         $myName = "Sammy";

         echo "test 1";

?>
