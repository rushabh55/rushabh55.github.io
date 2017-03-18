<?php
require_once __DIR__ . '/hunt.php';
 $no_nearest_hunts =  getCountNearestHunts($_GET['coordinates']);

?>
<style>
body {
    margin:2%;
}
#map {
    background-color: #80B2CC;
    float:left;
    height: 40%;
    width:100%;	
	
} 
div{
float:left;
margin-top: 1%;
} 

.left {
float:left;
}
.right {
float:right;
}
</style>
<body>
	<div id="map"> 
	</div>
	<div class="right">
	Active hunts : <?php echo $no_nearest_hunts; ?>
	</div>
	<div class="left">
	User Name : <?php echo $_GET['username']; ?>
	</div>

</body>
