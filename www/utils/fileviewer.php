<?php

if (! empty($_POST)) {
	$path = $_POST['file'];
} elseif (! empty($_GET)) {
	$path = $_GET['file'];
} else {
	die('No file.');
}

?>

<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8" />
	<title>File Viewer</title>
</head>
<body>
<?php
include_once('viewfile.php');
viewFile($path);
?>
</body>
</html>

