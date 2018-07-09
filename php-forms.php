<?php

    echo "Hello, ".$_POST['userFirstName']." ". $_POST['userLastName'];
    echo $_POST['submit'];

?>

<form action="php-forms.php" method="post">

    Last Name: <input name="userLastName" type="text" />

    First Name: <input name="userFirstName" type="text" />

    <input name="submit" type="submit" value="Blahit"/>

</form>
