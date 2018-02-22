<?php

include("auth.php"); //include auth.php file on all secure pages

$query = "SELECT config, entryCookies FROM `configs` WHERE userId = '$userId'";
$result = mysqli_query($con, $query) or die(mysql_error());
$row = mysqli_fetch_row($result);

$isConfigExisted = true;
$isEntryLoginNeeded = true;

if (NULL != $row) {

	if (NULL == $row[0] || '' == $row[0]) {
		$isConfigExisted = false;
	}

	if (NULL != $row[1] && '' != $row[1]) {
		$isEntryLoginNeeded = false;
	}
}

?>
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
<?php
if (! $isConfigExisted) { // No configuration
?>
<p><h2><a href="config.php">配置</a></h2></p>
<?php
} else if ($isEntryLoginNeeded) { // Need to login
?>
<p><h2><a href="entry.php">京东系统登录</a></h2></p>
<p><a href="config.php">配置</a></p>
<?php
} else { // Not need to login again
?>
<p><a href="../view/viewer.php">查看特价商品</a></p>
<p><a href="../search/search.html">搜索商品</a></p>
<p><a href="config.php">配置</a></p>
<?php
}
?>
<br/>
<a href="logout.php">退出登录</a>

</div>
</body>
</html>

