<?php

$offset = 0;
$code = 0;
$message = '';

$fp = NULL;

do {
	if (! empty($_POST)) {
		$path = $_POST['file'];
	} elseif (! empty($_GET)) {
		$path = $_GET['file'];
	} else {
		$code = 101;
		$message = 'No file.';
		break;
	}

	$path = base64_decode($path);

	if (!$fp = @fopen($path, 'rb')) {
		$code = 101;
		$message = 'Failed to open file.';
		break;
	}

	if (! empty($_POST)) {
		$offset = $_POST['offset'];
	} elseif (! empty($_GET)) {
		$offset = $_GET['offset'];
	} else {
		break;
	}

} while (0);

echo('{"jsonrpc" : "2.0", "error" : {"code": ' . $code
	. ', "message": "' . $message . '"}, "data": "');

if (NULL != $fp) {

	fseek($fp, 0, SEEK_END);
	$size = ftell($fp);

	fseek($fp, $offset, SEEK_SET);

	if ($size > $offset) {
		$buff = fread($fp, ($size - $offset));
		$buff = base64_encode($buff);

		echo($buff);
	}

	@fclose($fp);

	$offset = $size;
}

echo('", "offset": ' . $offset . '}');

?>

