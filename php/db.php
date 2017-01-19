<?php
$host = "localhost"; //Host (127.0.0.1 / localhost / mydomain.org / db.mydomain.org...)
$user = "ninja"; // mysql username (shall have the proper right to the database)
$pass = "*********"; // mysql user password
$db = "ninja"; // Database name (table are under the prefix ninja_)

$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}

$mysqli->set_charset("utf8");

?>
