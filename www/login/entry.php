<?php

include("auth.php"); //include auth.php file on all secure pages

session_start();

$pathPrefix = sys_get_temp_dir()."/entry-$userId";
$screenshotFile = $pathPrefix . '-screenshot.jpeg';
$inputFile = $pathPrefix . '-in.json';
$outputFile = $pathPrefix . '-out.json';

if (file_exists($screenshotFile)) {
	unlink($screenshotFile);
}

if (file_exists($inputFile)) {
	unlink($inputFile);
}

if (file_exists($outputFile)) {
	unlink($outputFile);
}

$_SESSION['file'] = $screenshotFile;
$_SESSION['inputFile'] = $inputFile;
$_SESSION['outputFile'] = $outputFile;
$_SESSION['userId'] = $userId;

$scriptFile = $config['login-script-path'];
$configFile = $config['login-config-path'];
$entryConfigFile = $config['login-entry-config-path'];
$envPath = $config['login-env-path'];

$cmd = "export PATH=$envPath".':$PATH && '."/bin/bash $scriptFile $configFile $userId $screenshotFile $inputFile $outputFile $entryConfigFile > /dev/null &";
system($cmd);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>京东系统登录</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div style="min-width: 80%">
    <div id="screenshotdiv"></div>
  </div>
  <hr>
  <div id="logindiv" style="min-width: 80%">正在登录，请稍候……</div>
  <script>

    function sendData() {

      element = document.getElementById('code');

      if ('' == element.value) {
        return;
      }

      if (element.maxLength > 0 && element.maxLength > element.value.length) {
        alert("长度不足" + element.maxLength);
        return;
      }

      if (lastId == curId && lastValue == element.value) {
        alert("重复输入");
        element.value = "";
        return;
      }

      lastId = curId;
      lastValue = element.value;

      var xhr = new XMLHttpRequest();

      xhr.onload = function() {
        if (200 === xhr.status) {
          document.getElementById('logindiv').innerHTML = '发送成功！继续登录，请稍候……';
        }
      };

      xhr.open('POST', '../utils/fileinput.php', true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send('data=' + element.value);
    }

    function getData() {

      var xhr = new XMLHttpRequest();

      xhr.onload = function() {

        if (200 === xhr.status) {

          var text = xhr.responseText.trim();

          if ('' != text) {

            var res = JSON.parse(text);
            var error = res['error'];

            var code = error['code'];

            if (code > 0) {

              var data = res['data'];

              curId = data['id'];

              if (lastCode != code) {

                console.log('ID #' + curId + ', Code #' + code);
                lastCode = code;

                var content = '<div>';

                if (data['message']) {
                  content += '<p>' + data['message'] + '</p>';
                }

                if (data['image']) {
                  content += '<p><img src="data: png;base64,' + data['image'] + '"/></p>';
                }

                if (data['notice']) {
                  content += '<p>' + data['notice'] + '</p>';
                }

                if (data['prompt']) {
                  promptMsg = 'placeholder="' + data['prompt'] + '"';
                }

                if (data['length']) {
                  lengthMsg = ' maxlength="' + data['length'] + '"';
                }

                content += '<p><input type="text" id="code" name="code" ' + promptMsg + ' ' + lengthMsg + ' required /></p>';
                content += '<p><input id="sendbutton" name="submit" type="submit" value="Input" onclick="sendData()" /></p>';

                content += '</div>' ;

                document.getElementById('logindiv').innerHTML = content;
              }

            } else if (0 === code) {

              clearInterval(timer);

              var content = '<div>';
              content += '<p>登录成功！</p>';
              content += '<p><a href="index.php">Home</a></p>';
              content += '</div>' ;

              document.getElementById('logindiv').innerHTML = content;

            } else {

              clearInterval(timer);
              console.log(xhr.responseText);

              var content = '<div>';
              content += '<p>登录失败！(' + error['message'] + ')</p>';
              content += '<p><a href="index.php">Home</a></p>';
              content += '</div>' ;

              document.getElementById('logindiv').innerHTML = content;
            }
          }
        }
      };

      xhr.open('GET', '../utils/fileoutput.php', true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send();
    }

    function updateScreenshot() {

      var xhr = new XMLHttpRequest();

      xhr.onload = function() {
        if (200 === xhr.status) {
          text = xhr.responseText.trim();
          if ('' != text) {
            var content = '<img src="data: jpeg;base64,' + text + '"/>';
            document.getElementById('screenshotdiv').innerHTML = content;
          }
        }
      };

      xhr.open('GET', '../utils/rawfile.php', true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send();
    }

    function update() {
        updateScreenshot();
        getData();
    }

    var timer = setInterval(update, 1000);
    var lastCode = 0;

    var curId = null;
    var lastId = null;
    var lastValue = null;

    window.onload = update();

  </script>
</body>
</html>

