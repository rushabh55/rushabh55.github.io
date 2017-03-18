<?php
require_once __DIR__ . '/PHPConnector.php';
function addHunt($name, $image, $hint, $coordinates, $created_user, $count_finished_users, $image_type) {
    $connector = new Connector();
    if (!$connector) die ("");
    $query = "INSERT INTO Hunt (id, name, image, hint, coordinates, created_user, count_finished_users, show_user, image_type) VALUES (null,'$name', '$image', '$hint', '$coordinates', '$created_user', '$count_finished_users', '$show_user','$image_type')";

    $res = $connector->runQuery($query, $connector->dbName);
    if ( $res ) {
        return "Success";
    }
    return "Fail";
}

function getHunt($id) {
    $connector = new Connector();
    if (!$connector) die ("");
    $query = "SELECT * FROM `Hunt` WHERE `id` = $id";
    $res = $connector->runQuery($query, $connector->dbName);
    if ( $res ) {
        return $res;
    }
    return null;
}
function rad($a) {
  return $a * 3.14 / 180;
}

function getDistance($p1, $p2){
	$p1 = explode(",", $p1);
	$p2 = explode(",", $p2);

	$R = 6378137;
	$dLat = rad($p2[0] - $p1[0]);
	$dLong = rad($p2[1] - $p1[1]);
	$a = sin($dLat / 2) * sin($dLat / 2) +
	    cos(rad($p1[0])) * cos(rad($p2[0])) *
	    sin($dLong / 2) * sin($dLong / 2);
	  $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
	   $d = $R * $c;
	  //echo $d."<br />"; // returns the distance in meter
	return $d;
}

function getNearestHunts($coord){
	$connector = new Connector();
	if (!$connector) die ("");
	$query = "SELECT * FROM Hunt";
	$hunts = $connector->runQuery($query, $connector->dbName);
	$nearest_hunts[] = null;
	while ($row = mysql_fetch_row($hunts)) {

	    $dist = getDistance($coord, $row[4]);

		if($dist < 10*160000) {
			$nearest_hunts[] = $row;
		}
	    }
	return $nearest_hunts;
	
    
}
function checkinHunt($username,$coord){

	$nearest_hunts = getNearestHunts($coord);
	foreach($nearest_hunts as $nh)
	{
		$dist = getDistance($coord, $nh[4]);

			if($dist < 50) {
				$hunts[] = $nh[1];
			}
		    
	}
	return $hunts;
}

function getCountNearestHunts($coord)
{
$nearest_hunts = getNearestHunts($coord);
return (count( $nearest_hunts)-1);

}
	if (isset ( $_POST['q'] )) {
	    if ($_POST['q'] == 'addHunt') {
		 $res = array ( 'status' => addHunt($_POST['name'], $_POST['image'], $_POST['hint'], $_POST['coordinates'], $_POST['created_user'], $_POST['count_finished_users'], $_POST['image_type']) );
		    echo json_encode($res, JSON_PRETTY_PRINT);
	    } 
		

	}
	
	if($_GET['q'] == 'getNearestHunts'){
			$coord = $_GET['coord'];
			
			$res = array ( 'status' => getNearestHunts($coord) );
		    echo json_encode($res, JSON_PRETTY_PRINT);
		}
	if($_GET['q'] == 'checkinHunt'){ //http://rushg.me/TreasureHunt/hunt.php?username=rushabh&coord=0,0&q=checkinHunt
			$coord = $_GET['coord'];
			$username = $GET['username'];
			$res = array ( 'status' => checkinHunt($GET['username'],$coord) );
		    echo json_encode($res, JSON_PRETTY_PRINT);
		}
	
	

?>
