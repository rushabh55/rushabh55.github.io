<?php
$headers  = "MIME-Version: 1.0" . "\r\n";
function  mailGo($To, $Subject, $Message, $Sender, $headers){     
        $headers .= "Content-type: text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: ".$Sender."";    
        $resp = mail($To, $Subject, $Message, $headers);
        echo $resp . "\n  Success sent to " . $To . "Subj = ". $Subject ." header= ".$headers . " Sender= " . $Sender;  
   }
   
if(isset($_POST['To']) && isset($_POST['Subject']) && isset($_POST['Message']) && isset($_POST['Sender']))
    mailGo($_POST['To'], $_POST['Subject'], $_POST['Message'], $_POST['Sender'], $headers);     
      ?>
