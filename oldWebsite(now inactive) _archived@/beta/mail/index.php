<?php
    $to = $_GET['to']? $_GET['to'] : "info@myseniorhomeconnection.com";
    $from = "From: <rushabh55@live.com>";
    $subject = $_GET['subject']?$_GET['subject']:"Information";    
    $name = $_GET['name'];
    $email = $_GET['email'];
    $phone = $_GET['phone'];
    $other = $_GET['other'] ? $_GET['other'] : "<No other Info>";
    $body =  "This person has requested information from your website using the 'Click here and we will do the work for you'. Please use his/her information and contact him/her back." . "\n".
        "Name: " . $name . "\n".
        "Email: " . $email . "\n".
        "Phone: " . $phone . "\n" .
	"Other Details: " . $other;
    $r = mail($to, $subject, $body, $from);
    
    if(!r)
    {
       // echo 'Mailing failed <script>isTrue = true;</script>';
    }
    else
    {
        // echo "Mail succeded\n";
    }
    echo '<meta http-equiv="refresh" content="0; url=http://myseniorhomeconnection.com">';
    //echo '<b> Taking you to myseniorhomeconnection.com </b>';   
?>
<html>
    <head>
<script>
</script>
        </head>
    </html>