<?php
// phpinfo();
include 'conf/mysql.php';

//Turn on error reporting
ini_set('display_errors', 'On');

//Connects to the database
$mysqli = new mysqli($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASS,$MYSQL_DB);

if ($mysqli->connect_errno) {
	echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
}

?>
