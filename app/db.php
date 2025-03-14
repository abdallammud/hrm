<?php 

$servername = "localhost";
$username   = "root";
$password   = "";
$db = "asheeri";

$servername = "localhost";
$username   = "u138037914_hrm";
$password   = "|8nJaj9eU";
$db = "u138037914_hrm";

$GLOBALS['conn'] = $conn = new mysqli($servername, $username, $password, $db);

if($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}





?>
