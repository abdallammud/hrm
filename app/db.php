<?php 

$servername = "localhost";
$username   = "root";
$password   = "";
$db = "edurdur_t";

/*$servername = "localhost";
$username   = "u138037914_gamaas";
$password   = ";9lZgpiFqxOA";
$db = "u138037914_gamaas";*/

$GLOBALS['conn'] = $conn = new mysqli($servername, $username, $password, $db);

if($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}





?>
