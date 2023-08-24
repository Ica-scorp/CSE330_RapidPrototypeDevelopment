<?php

$mysqli = new mysqli('localhost', 'Dijkstra', '20031023', 'module5Database');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>