<?php
require_once __DIR__ . '/PHPConnector.php';
function addUser($name, $password, $email, $location) {
    $connector = new Connector();
    if (!$connector) die ("");
    $query = "INSERT INTO Users (username, password, email, location) VALUES ('$name', '$password', '$email', '$location')";
    $res = $connector->runQuery($query, $connector->dbName);
    if ( $res ) {
        return "Success";
    }
    return "Fail";
}

function edit_loc_id($id, $location){
	$connector = new Connector();
    if (!$connector) die ("");
    $query = "UPDATE Users SET location=".$location." WHERE id =".$id;
    $res = $connector->runQuery($query, $connector->dbName);
    if ( $res ) {
        return "Success";
    }
    return "Fail";
}
function edit_loc_name($name, $location){
$connector = new Connector();
    if (!$connector) die ("");
    $query = "UPDATE Users SET location='".$location."' WHERE username ='".$name."'";
	echo $query;
    $res = $connector->runQuery($query, $connector->dbName);
    if ( $res ) {
        return "Success";
    }
    return "Fail";
}

if (isset ( $_GET['q'] )) {
    if ($_GET['q'] == 'addUser') {
         $res = array ( 'status' => addUser($_GET['username'], $_GET['password'], $_GET['email'], $_GET['location']) );
            echo json_encode($res, JSON_PRETTY_PRINT);
    } else if($_GET['q'] == 'editUser') {
		if(isset($_GET['id']))
		{
		$res = array ( 'status' => edit_loc_id($_GET['id'], $_GET['location']) );
            	echo json_encode($res, JSON_PRETTY_PRINT);
		} else if(isset($_GET['username']))
		{
		$res = array ( 'status' => edit_loc_name($_GET['username'], $_GET['location']) );
            	echo json_encode($res, JSON_PRETTY_PRINT);
		}
	}

}
?>
