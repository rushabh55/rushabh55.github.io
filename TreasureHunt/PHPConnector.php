<?php
define ("HOSTNAME", "localhost");
define ("USERNAME", "rushjzvr_rushabh");
define ("PASSWORD", "Rushabh%1");
class Connector {
    var $connection;
    var $clConnection;
    var $dbName = "rushjzvr_TreasureHunt";
    function __construct () {

    }

    function runQuery($query) {
        $link = mysql_connect(HOSTNAME, USERNAME, PASSWORD);
   
        mysql_select_db($this->dbName, $link);

       $result = mysql_query($query, $link);
        // $this->clConnection = new mysqli(HOSTNAME, USERNAME, PASSWORD, $this->dbName);

       // $result = $this->clConnection->query($query);
        return $result;
    }


}
?>
