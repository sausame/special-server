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
    .plate-textarea {
      border: 1px solid #cccccc;
      padding: 5px;
      min-width: 100%;
      font-family: Tahoma, sans-serif;
      background-image: url(bg.gif);
      background-position: bottom right;
      background-repeat: no-repeat;
    }
	.button-copy {
	  -moz-box-shadow: 0px 10px 14px -7px #276873;
	  -webkit-box-shadow: 0px 10px 14px -7px #276873;
	  box-shadow: 0px 10px 14px -7px #276873;
	  background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #599bb3), color-stop(1, #408c99));
	  background:-moz-linear-gradient(top, #599bb3 5%, #408c99 100%);
	  background:-webkit-linear-gradient(top, #599bb3 5%, #408c99 100%);
	  background:-o-linear-gradient(top, #599bb3 5%, #408c99 100%);
	  background:-ms-linear-gradient(top, #599bb3 5%, #408c99 100%);
	  background:linear-gradient(to bottom, #599bb3 5%, #408c99 100%);
	  filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#599bb3', endColorstr='#408c99',GradientType=0);
	  background-color:#599bb3;
	  -moz-border-radius:8px;
	  -webkit-border-radius:8px;
	  border-radius:8px;
	  display:inline-block;
	  cursor:pointer;
	  color:#ffffff;
	  font-family:Arial;
	  font-size:20px;
	  font-weight:bold;
	  padding:13px 32px;
	  text-decoration:none;
	  text-shadow:0px 1px 0px #3d768a;
	}
	.button-copy:hover {
	  background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #408c99), color-stop(1, #599bb3));
	  background:-moz-linear-gradient(top, #408c99 5%, #599bb3 100%);
	  background:-webkit-linear-gradient(top, #408c99 5%, #599bb3 100%);
	  background:-o-linear-gradient(top, #408c99 5%, #599bb3 100%);
	  background:-ms-linear-gradient(top, #408c99 5%, #599bb3 100%);
	  background:linear-gradient(to bottom, #408c99 5%, #599bb3 100%);
	  filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#408c99', endColorstr='#599bb3',GradientType=0);
	  background-color:#408c99;
	}
	.button-copy:active {
	  position:relative;
	  top:1px;
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
          <textarea id="<?php echo($name); ?>" class="plate-textarea" rows="12" readonly><?php echo($data->plate); ?></textarea>
          <button class="btn button-copy" data-clipboard-action="copy" data-clipboard-target="#<?php echo($name); ?>">Copy Text</button>
          <hr/>
          <img src="<?php echo($data->image); ?>"/>
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
