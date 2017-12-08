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

$pathPrefix = tempnam(sys_get_temp_dir(), 'wx-');
$logPath = $pathPrefix . '.log';

$configFile = $config['wx-config-path'];
$shareScriptFile = $config['wx-share-config-path'];
$scriptFile = $config['wx-helper-script-path'];

// TODO: Configs and log should be different.
$cmd = '/bin/bash ' . $scriptFile . ' ' . $configFile . ' ' . $shareScriptFile . ' ' . $uuid . ' ' . $logPath . '> /dev/null &';
system($cmd);

$logPath = base64_encode($logPath);
?>
  <h2>If it quits, please re-login.</h2>
  <form id="refresh" name="refreshForm" method="POST" action="login.php" >
    <input type="submit" value="Refresh" />
  </form>
  <h2>Log</h2>
  <iframe width="100%" height="100%" src="../utils/fileviewer.php?file=<?php echo($logPath); ?>" frameborder="0" allowfullscreen>
  </iframe>
</body>
</html>

