<?php

$config = parse_ini_file('../../config.ini');

$pathPrefix = tempnam(sys_get_temp_dir(), 'viewer-result-');
$saveFile = $pathPrefix . '.json';

$configFile = $config['viewer-config-path'];
$shareScriptFile = $config['viewer-share-config-path'];
$scriptFile = $config['viewer-script-path'];

$cmd = '/bin/bash ' . $scriptFile . ' ' . $configFile . ' ' . $shareScriptFile . ' ' . $saveFile;

$output = system($cmd, $retval);

if (0 === $retval) {
	$result = file_get_contents($saveFile);
} else {
	die('NO result');
}

unlink($saveFile);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Swiper demo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Link Swiper's CSS -->
  <link rel="stylesheet" href="../dist/css/swiper.min.css">

  <!-- Demo styles -->
  <style>
    html, body {
      position: relative;
      height: 100%;
    }
    body {
      background: #eee;
      font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
      font-size: 14px;
      color:#000;
      margin: 0;
      padding: 0;
    }
    .swiper-container {
      width: 100%;
      height: 100%;
    }
    .swiper-slide {
      text-align: center;
      font-size: 18px;
      background: #fff;

      /* Center slide text vertically */
      display: -webkit-box;
      display: -ms-flexbox;
      display: -webkit-flex;
      display: flex;
      -webkit-box-pack: center;
      -ms-flex-pack: center;
      -webkit-justify-content: center;
      justify-content: center;
      -webkit-box-align: center;
      -ms-flex-align: center;
      -webkit-align-items: center;
      align-items: center;
    }
  </style>
</head>
<body>
  <!-- Swiper -->
  <div class="swiper-container">
    <div class="swiper-wrapper">
<?php
	$user = json_decode($result);
	$count = 0;
	foreach($user->list as $data) {
		$count ++;
		$name = 'bar' . $count;
?>
      <div class="swiper-slide">
        <div style="min-width: 80%">
          <textarea id="<?php echo($name); ?>" style="min-width: 100%" rows="20" readonly><?php echo($data->plate); ?></textarea>
          <br/>
          <img src="<?php echo($data->image); ?>"/>
          <br/>
          <button class="btn" data-clipboard-action="copy" data-clipboard-target="#<?php echo($name); ?>">Copy</button>
        </div>
      </div>
<?php
	}
?>
    </div>
    <!-- Add Pagination -->
    <div class="swiper-pagination"></div>
    <!-- Add Arrows -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>

  </div>

  <!-- Swiper JS -->
  <script src="../dist/js/swiper.min.js"></script>
  <script src="../dist/js/clipboard.min.js"></script>

  <!-- Initialize Swiper -->
  <script>
    var swiper = new Swiper('.swiper-container', {
      pagination: {
        el: '.swiper-pagination',
        type: 'fraction',
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    });

    var clipboard = new Clipboard('.btn');

    clipboard.on('success', function(e) {
        console.log(e);
    });

    clipboard.on('error', function(e) {
        console.log(e);
    });

  </script>
</body>
</html>
