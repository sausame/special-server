<?php

do {
	if (! empty($_POST)) {
		$data = $_POST['data'];
	} elseif (! empty($_GET)) {
		$data = $_GET['data'];
	} else {
		$code = 101;
		$message = 'No data.';
		break;
	}

	session_start();

	if (! isset($_SESSION['inputFile'])) {
		$code = 102;
		$message = 'No login.';
		break;
	}

	$path = $_SESSION['inputFile'];

	if (!$fp = @fopen($path, 'wb')) {
		$code = 103;
		$message = 'Failed to open file.';
		break;
	}

	fwrite($fp, $data);
	@fclose($fp);

	$code = 0;
	$message = 'Succeed';

} while (0);

echo('{"jsonrpc" : "2.0", "error" : {"code": ' . $code . ', "message": "' . $message . '"}, "data": null}');

?>

