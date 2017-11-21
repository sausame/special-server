<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8" />
  <title>Wechat is launched</title>
</head>
<body>
<?php

if (! empty($_POST)) {
	$uuid = $_POST['uuid'];
} elseif (! empty($_GET)) {
	$uuid = $_GET['uuid'];
} else {
	die('NO uuid');
}

$config = parse_ini_file('../../config.ini');

$configFile = $config['wx-config-path'];
$scriptFile = $config['wx-helper-script-path'];

// TODO: Configs and log should be different.
$cmd = '/bin/bash ' . $scriptFile . ' ' . $configFile . ' ' . $uuid . '> /dev/null &';
system($cmd);

?>
  <h2>If it quits, please re-login.</h2>
  <form id="refresh" name="refreshForm" method="POST" action="login.php" >
    <input type="submit" value="Refresh" />
  </form>
</body>
</html>

