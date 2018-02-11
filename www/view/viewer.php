<?php

include("auth.php"); //include auth.php file on all secure pages

session_start();

// Get user config
$query = "SELECT id FROM `configs` WHERE userId = '$userId'";
$result = mysqli_query($con, $query) or die(mysql_error());
$row = mysqli_fetch_row($result);

if (NULL == $row) {
	header("Location: ../login/config.php");
	exit();
}

$pathPrefix = sys_get_temp_dir() . '/'. "viewer-share-$userId";
$shareFile = $pathPrefix . '.json';
$saveFile = $pathPrefix . '-result.json';

$_SESSION['outputFile'] = $saveFile;

$config = parse_ini_file('../../config.ini');
$shareUrl = $config['viewer-share-url'];

file_put_contents($shareFile, fopen($shareUrl, 'r'));

$needed = true;

if (file_exists($saveFile)) {

	$content = file_get_contents($shareFile);
	$shareObj = json_decode($content);

	$content = file_get_contents($saveFile);
	$saveObj = json_decode($content);

	if ($shareObj->startTime != $saveObj->startTime) {
		$needed = false;
	}
}

if ($needed) {

	$configFile = $config['viewer-config-path'];
	$scriptFile = $config['viewer-script-path'];
	$envPath = $config['login-env-path'];

	$cmd = "export PATH=$envPath".':$PATH && /bin/bash ' . "$scriptFile $configFile $userId $shareFile -1 $saveFile > /dev/null &";
	system($cmd);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>京东实时特价</title>
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
  <div id='container' class="swiper-container">
    <div class="swiper-slide">
      <div style="min-width: 80%">正在更新，请稍候……</div>
    </div>
  </div>

  <!-- Swiper JS -->
  <script src="../dist/js/swiper.min.js"></script>
  <script src="../dist/js/clipboard.min.js"></script>

  <!-- Initialize Swiper -->
  <script>

    function getContent(index, data) {

      var content = '<div class="swiper-slide"><div style="min-width: 80%">';

      var plate = data['plate'];
      var image = data['image'];

      content += '<textarea id="bar' + index + '" class="plate-textarea" rows="12" readonly>' + plate + '</textarea>';
      content += '<button class="btn button-copy" data-clipboard-action="copy" data-clipboard-target="#bar' + index + '">复制文本</button>';
      content += '<hr/>';
      content += '<img src="' + image + '"/>';
      content += '</div></div>';

      return content;
    }

    function showContainer(data) {

      var num = data['num'];
      var startTime = data['startTime'];
      var endTime = data['endTime'];
      var dataList = data['list'];

      var content = '<div class="swiper-wrapper">';

      console.log('Get ' + num + ' items between ' + startTime + ' and ' + endTime);

      for (var index = 0; index < dataList.length; index ++) {
          content += getContent(index, dataList[index]);
      }

      content += '</div>';
      content += '<div class="swiper-pagination"></div>';
      content += '<div class="swiper-button-next"></div>';
      content += '<div class="swiper-button-prev"></div>';

      document.getElementById('container').innerHTML = content;

      swiper = new Swiper('.swiper-container', {
        pagination: {
          el: '.swiper-pagination',
          type: 'fraction',
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
      });
    }

    function getData() {

      var xhr = new XMLHttpRequest();

      xhr.onload = function() {

        if (200 === xhr.status) {

          var text = xhr.responseText.trim();

          if ('' != text) {
            res = JSON.parse(text);
            error = res['error'];

            if (0 === error['code']) {
              clearInterval(timer);

              var data = res['data'];
              showContainer(data);

            } else {
              console.log(error);
            }
          }
        }
      };

      xhr.open('GET', '../utils/fileoutput.php', true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send();
    }

    var swiper = null;

    var clipboard = new Clipboard('.btn');

    clipboard.on('success', function(e) {
      console.log(e);
    });

    clipboard.on('error', function(e) {
      console.log(e);
    });

    var timer = setInterval(getData, 3000);

    window.onload = getData();
  </script>
</body>
</html>
