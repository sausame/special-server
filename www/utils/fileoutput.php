<?php
session_start();

$path = $_SESSION['outputFile'];
if (isset($path) && file_exists($path)) {
	echo(file_get_contents($path));
}
?>

