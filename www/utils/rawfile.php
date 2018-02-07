<?php
session_start();

$path = $_SESSION['file'];
if (isset($path) && file_exists($path)) {
	$buff = file_get_contents($path);
	echo(base64_encode($buff));
}
?>

