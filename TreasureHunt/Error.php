<?php
class Error
{
    var $error = "";

    public  function reportError($err) {
        var_dump($err);
        echo '\n';
        echo $err;
    }
}
?>

