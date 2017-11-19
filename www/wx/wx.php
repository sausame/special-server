<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8" />
  <title>QR</title>
</head>
<body>

<?php

$config = parse_ini_file('../../config.ini');

$configFile = $config['wx-config-path'];
$scriptFile = $config['wx-script-path'];

$tempFile = tempnam(sys_get_temp_dir(), 'qr-pic-');

$cmd = '/bin/bash ' . $scriptFile . ' ' . $configFile . ' ' . $tempFile;
$output = system($cmd, $retval);

if (0 === $retval) {
    for ($i = 1; $i <= 10; $i++) {
        sleep(6);
        if (file_exists($tempFile)) {
            sleep(10);
            $imageData = base64_encode(file_get_contents($tempFile));
            $src = 'data: ' . mime_content_type($tempFile) . ';base64,' . $imageData;
            echo '<img src="' . $src . '"/>';
            break;
        }
    }
    unlink($tempFile);
} else {
    unlink($tempFile);
    die('Error in wx');
}
?>
</body>
</html>

