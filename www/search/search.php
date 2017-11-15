<?php

if (! empty($_POST)) {
	$content = $_POST['content'];
} elseif (! empty($_GET)) {
	$content = $_GET['content'];
} else {
	die('NO content');
}

if (empty($content)) {
	die('NO content');
}

$config = parse_ini_file('../../config.ini');

$configFile = $config['searcher-config-path'];
$scriptFile = $config['searcher-script-path'];

$tempFile = tempnam(sys_get_temp_dir(), 'search-result-');

$cmd = 'bash ' . $scriptFile . ' ' . $configFile . ' "' . $content . '" ' . $tempFile;

$output = system($cmd, $retval);

if (0 === $retval) {
	readfile($tempFile);
} else {
	die('Error in searching');
}

unlink($tempFile);

?>

