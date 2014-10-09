<?php
require_once("./include/membersite_config.php");

$success =  array("Reponse" => "succeed");
$invalidParam = array("Reponse" => "Invalid Parameters. Specify the name, score, overwrite parameters");
$invalidAPI = array("Response" => "Invalid API Key!");
$Failed =  array("Reponse" => "An internal error occured");

$fgmembersite->DBLogin();

if(isset($_GET['name']) && isset($_GET['score']) && isset($_GET['apikey']) && isset($_GET['overwrite']) )
{    
    if($fgmembersite->APIexists($_GET['apikey']))
    {       
       // echo 'API Exists';
        if($fgmembersite->EnterScores($_GET['name'], $_GET['score'], $_GET['age'], $_GET['country'], $_GET['additional'], $_GET['apikey'], $_GET['overwrite']))
        {
            //echo 'Function called <br />';
            echo $fgmembersite->beautify(json_encode($success));
        }
        else
        {
            echo $fgmembersite->beautify(json_encode($Failed));
        }
    }
    else
       {     echo $fgmembersite->beautify(json_encode($invalidAPI));
             echo '<br /><br />Get a key from <a href="http://rushabhgosar.com/apis">here</a>.';
            
       }
}
else
    echo $fgmembersite->beautify(json_encode($invalidParam));

?>
