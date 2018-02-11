<?php

include("auth.php"); //include auth.php file on all secure pages ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title><?php echo $_COOKIE['ID_your_site']; ?>的首页</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="form">
<p>欢迎<?php echo $_COOKIE['ID_your_site']; ?>！</p>
<p><a href="../view/viewer.php">查看特价商品</a></p>
<p><a href="../search/search.html">搜索商品</a></p>
<p><a href="config.php">配置</a></p>
<br/>
<a href="logout.php">退出登录</a>

</div>
</body>
</html>

