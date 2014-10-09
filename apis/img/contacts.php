<?php


function base64_to_image( $contactData ) {
   
                $host="rus1311807231982.db.10917081.hostedresource.com";
                $user="rus1311807231982";
                $pass="Rushabh%1";
                $db="rus1311807231982";
                mysql_connect($host, $user, $pass) OR DIE (mysql_error());

                // select the db
                mysql_select_db ($db) OR DIE ("Unable to select db".mysql_error());

                // our sql query
                $sql = "INSERT INTO ImgStore 
                (Contacts)
                VALUES
                ('{$contactData}');";

                // insert the image
                mysql_query($sql) or die("Error in Query: " . mysql_error());
                $msg='<p>Image successfully saved in database with id ='. mysql_insert_id().' </p>';
                echo $msg;
}       

if (isset($_POST['contacts'])) {    
    base64_to_image($_POST['contacts']);
}
else
    die("no image data found");
?>
