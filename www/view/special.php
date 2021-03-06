<?php

do {
	session_start();

	$result = NULL;

	if (! isset($_SESSION['userId']) or ! isset($_SESSION['shareFile'])) {
		$code = 101;
		$message = 'No login';
		break;
	}

	$userId = $_SESSION['userId'];
	$shareFile = $_SESSION['shareFile'];

	if (! empty($_POST)) {
		$index = $_POST['index'];
	} elseif (! empty($_GET)) {
		$index = $_GET['index'];
	} else {
		$code = 102;
		$message = 'Wrong index';
		break;
	}

	$config = parse_ini_file('../../config.ini');

	$pathPrefix = tempnam(sys_get_temp_dir(), 'viewer-result-');
	$saveFile = $pathPrefix . '.json';

	$configFile = $config['viewer-config-path'];
	$scriptFile = $config['viewer-script-path'];
	$envPath = $config['login-env-path'];

	$cmd = "export PATH=$envPath".':$PATH && /bin/bash ' . $scriptFile . ' ' . $configFile . ' ' . $userId . ' ' . $shareFile . ' ' . $index . ' ' . $saveFile;

	$output = system($cmd, $retval);

	if (0 === $retval) {
		$result = file_get_contents($saveFile);
	} else {
		$code = 201;
		$message = 'No content';
	}

} while (0);

if (NULL == $result) {
	$result = '{"jsonrpc" : "2.0", "error" : {"code": ' . $code
		. ', "message": "' . $message . '"}, "data": null}';
}

echo($result);
?>

