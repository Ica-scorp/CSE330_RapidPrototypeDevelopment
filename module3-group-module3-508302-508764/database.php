<?php

$mysqli = new mysqli('localhost', 'Dijkstra', '20031023', 'module3Database');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>