<?php
session_start();
require './twitteroauth.php';
if(isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) 
{
    echo 'Session has expired';
}   
else
{
 $connection = new TwitterOAuth("zRkL1VQONTQduf9GdWYhkQ", "MTORu7zxaJxoSjZdt9wrEntsggB8EM9ifswWLgSA", $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
 $token_credentials = $connection->getAccessToken($oauth_verifier);
}echo $token_credentials['oauth_token'];
?>
