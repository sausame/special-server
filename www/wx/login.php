<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8" />
  <title>Wechat Login</title>
</head>
<body>

<?php

$config = parse_ini_file('../../config.ini');

$scriptFile = $config['wx-login-script-path'];

$pathPrefix = tempnam(sys_get_temp_dir(), 'wx-');
$uuidPath = $pathPrefix . '-uuid.txt';
$qrPath = $pathPrefix . '-qr.png';

$cmd = '/bin/bash ' . $scriptFile . ' ' . $uuidPath . ' ' . $qrPath;

echo('<h2>Log</h2><pre>');
$output = system($cmd, $retval);
echo('</pre>');

if (0 === $retval) {
	if (file_exists($uuidPath) and file_exists($qrPath)) {

		$imageData = base64_encode(file_get_contents($qrPath));
		$src = 'data: ' . mime_content_type($qrPath) . ';base64,' . $imageData;
?>
		<h2>Please scan the QR Code</h2><br/>
		<img src="<?php echo($src); ?>"/><br/>
		<form id="wechat" name="wechatForm" method="POST" action="wechat.php" >
		  <input type="hidden" name="uuid" value="<?php echo file_get_contents($uuidPath); ?>">
		  <input type="submit" value="Please press button after scan QR code." />
		</form>
<?php

	}
} else {

?>
		<h2>Unable to get QR code (error #<?php echo($retval); ?>), please refresh the page.</h2><br/>
		<form id="refresh" name="refreshForm" method="POST" action="<?php echo(basename(__FILE__)); ?>" >
		  <input type="submit" value="Refresh" />
		</form>
<?php
}

unlink($qrPath);
unlink($uuidPath);

?>
</body>
</html>

