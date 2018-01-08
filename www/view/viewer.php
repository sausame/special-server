<?php

include("auth.php"); //include auth.php file on all secure pages

session_start();

if (isset($_SESSION['userConfigFile'])) {
	unlink($_SESSION['userConfigFile']);
}

if (isset($_SESSION['shareFile'])) {
	unlink($_SESSION['shareFile']);
}

// Get user config
$query = "SELECT config FROM `configs` WHERE userId = '$userId'";
$result = mysqli_query($con, $query) or die(mysql_error());
$row = mysqli_fetch_row($result);

if (NULL == $row) {
	header("Location: ../login/config.php");
	exit();
}

$pathPrefix = tempnam(sys_get_temp_dir(), "viewer-$userId-");

$userConfigFile = $pathPrefix . '-user-config.json';
$shareFile = $pathPrefix . '-share.json';

$fp = fopen($userConfigFile, 'wb');
if (NULL == $fp) {
	echo("Error to write user config.");
	exit();
}

fwrite($fp, $row[0]);
fclose($fp);

$config = parse_ini_file('../../config.ini');
$shareUrl = $config['viewer-share-url'];

file_put_contents($shareFile, fopen($shareUrl, 'r'));

$_SESSION['shareFile'] = $shareFile;
$_SESSION['userConfigFile'] = $userConfigFile;

$result = file_get_contents($shareFile);
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
  <div class="swiper-container">
    <div class="swiper-wrapper">
<?php
	$user = json_decode($result);
	$num = $user->num;
	for ($index = 0; $index < $num; $index ++) {
		$divName = 'div' . $index;
?>
      <div class="swiper-slide">
        <div id="<?php echo($divName); ?>" style="min-width: 80%">正在更新，请稍候……</div>
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

    function getData(index) {

      switch(statuses[index]) {
      case 0:
        statuses[index] = 1; // Updating
        console.log('' + index + ' is updating ...');
        break;
      case 1:
      case 2:
      default:
        return;
      }

      var xhr = new XMLHttpRequest();

      xhr.onload = function() {

        if (200 === xhr.status) {

          statuses[index] = 2; // Updated

          console.log('' + index + ' is updated.');

          res = JSON.parse(xhr.responseText);
          error = res['error'];

          if (0 === error['code']) {

            var data = res['data'];
            var plate = data['plate'];
            var image = data['image'];

            document.getElementById('div' + index).innerHTML = '<textarea id="bar' + index + '" class="plate-textarea" rows="12" readonly>' + plate + '</textarea>\n<button class="btn button-copy" data-clipboard-action="copy" data-clipboard-target="#bar' + index + '">复制文本</button>\n<hr/>\n<img src="' + image + '"/>';

          } else {
            console.log(error);
          }
        }
      };

      xhr.open('POST', 'special.php', true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send('index=' + index);
    }

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

    swiper.on('slideChange', function () {
      getData(swiper.realIndex);
    });

    var clipboard = new Clipboard('.btn');

    clipboard.on('success', function(e) {
      console.log(e);
    });

    clipboard.on('error', function(e) {
      console.log(e);
    });

    var statuses = [];
    var num = <?php echo($num); ?>;
    for (i = 0; i < num; i ++) {
      statuses[i] = 0; // Initialization
    }

    window.onload = getData(0);

  </script>
</body>
</html>
