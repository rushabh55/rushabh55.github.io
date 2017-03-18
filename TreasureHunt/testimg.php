
<?php
require_once __DIR__ . '/PHPConnector.php';
function img() {
    $connector = new Connector();
    if (!$connector) die ("");
    $query = "SELECT image from Hunt where id=21";//hard coded

    $res = $connector->runQuery($query, $connector->dbName);
	$result=mysqli_fetch_array($res);
// this is code to display 
echo '<img src="data:image/'.$result['image_type'].';base64,'.base64_encode( $result['image'] ).'"/>';
}

    //$image = mysqli_fetch_assoc($res);
	//$image = $image['image'];
//
//	header("Content-type: image/PNG");
//
//	echo $image;



$res = array ( 'status' => img() );
		    echo json_encode($res, JSON_PRETTY_PRINT);
?>
