<?php
require_once './include/membersite_config.php';

if(isset($_GET['apikey']))
    $fgmembersite->RetrieveScores($_GET['apikey'], $_GET['offset'], $_GET['take']);
else
    echo 'Please enter a API key';
?>