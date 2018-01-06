<?php

require('../login/db.php');

if (isset($_COOKIE['ID_your_site'])) { //if there is, it logs you in and directes you to the members page

 	$username = $_COOKIE['ID_your_site']; 
 	$password = $_COOKIE['Key_your_site'];

	$query = "SELECT id FROM `users` WHERE username = '$username' and password = '$password'";
	$result = mysqli_query($con, $query) or die(mysql_error());
	$rows = mysqli_num_rows($result);
	if(0 == $rows) {
		header("Location: ../login/login.php");
		exit();
	}

	$row = mysqli_fetch_row($result);
	$userId = $row[0];
} else {
	header("Location: ../login/login.php");
	exit();
}

?>
