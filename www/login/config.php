<?php
include("auth.php"); //include auth.php file on all secure pages

$insertFlag = false;
$configId = NULL;
$userConfig = NULL;

$query = "SELECT id, config, entryCookies, updateTime FROM `configs` WHERE userId = '$userId'";
$result = mysqli_query($con, $query) or die(mysql_error());
$row = mysqli_fetch_row($result);

$username = '';
$currentPassword = '';
$fakePassword = '**********';
$lastUpdateTime = NULL;

$isEntryLoginNeeded = true;

if (NULL != $row) {
	$configId = $row[0];

	$userConfig = json_decode($row[1]);

	$loginConfig = $userConfig->login;

	$username = $loginConfig->username;
	$currentPassword = $loginConfig->password;

	if (NULL != $row[2] or '' != $row[2]) {
		$isEntryLoginNeeded = false;
	}

	$lastUpdateTime = $row[3];
}

// If form submitted, query values from the database.
if (isset($_POST['username'])) {

	$username = stripslashes($_REQUEST['username']); // removes backslashes
	$username = mysqli_real_escape_string($con, $username); //escapes special characters in a string

	$password = stripslashes($_REQUEST['password']);
	$password = mysqli_real_escape_string($con, $password);

	if ($fakePassword == $password) {
		$password = $currentPassword;
	} else {
		$password = base64_encode($password);
	}

	$userConfig = '{ "login": { "username": "'.$username.'", "password": "'.$password.'" } }';

	if ($configId) {
		$sql = "UPDATE `configs`
			SET `config` = '".$userConfig."',
				`updateTime` = CURRENT_TIMESTAMP
			WHERE `id` = ".$configId;
	} else {
		$sql = "INSERT INTO `configs`
			( `id`, `userId`, `config`, `entryCookies`, `keyCookies`, `createTime`, `updateTime` )
			VALUES
			( NULL, $userId, '$userConfig', NULL, NULL, NULL, NULL);";
	}

	$result = mysqli_query($con, $sql);

	if ($result) {
		$insertFlag = true;
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>配置</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="form">
<p>
<?php
if ($insertFlag) {
	echo('配置已保存。');
} else if ($lastUpdateTime) {
	echo('上次修改配置的时间为：'.$lastUpdateTime);
} else {
	echo('新配置');
	$isEntryLoginNeeded = false;
}
?>
</p>
<hr/>
<h3>注意是京东系统的用户名和密码</h3>
<form action="" method="post" name="login">
  <input type="text" name="username" value="<?php echo($username);?>" placeholder="用户名" required />
  <input type="text" name="password" value="<?php echo($fakePassword);?>" placeholder="密码" required />
  <hr/>
  <input name="submit" type="submit" value="更新配置" />
</form>

<?php
if ($isEntryLoginNeeded) {
?>
<p><h2><a href="entry.php">京东系统登录</a></h2></p>
<?php
}
?>

<p><a href="index.php">回到首页</a></p>
<a href="logout.php">退出登录</a>

</div>
</body>
</html>
