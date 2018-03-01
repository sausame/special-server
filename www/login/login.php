<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>登录</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?php
	require('db.php');

	if (isset($_COOKIE['ID_your_site'])) { //if there is, it logs you in and directes you to the members page

		$username = $_COOKIE['ID_your_site'];
		$password = $_COOKIE['Key_your_site'];

		$query = "SELECT * FROM `users` WHERE username = '$username' and password = '$password'";
		$result = mysqli_query($con, $query) or die(mysql_error());
		$rows = mysqli_num_rows($result);
		if(0 == $rows) {
			header("Location: login.php");
			exit();
		}
		header("Location: index.php"); // Redirect user to index.php
		exit();
	}

	// If form submitted, query values from the database.
	if (isset($_POST['username'])){

		$username = stripslashes($_REQUEST['username']); // removes backslashes
		$username = mysqli_real_escape_string($con,$username); //escapes special characters in a string
		$password = stripslashes($_REQUEST['password']);
		$password = mysqli_real_escape_string($con,$password);
		$password = md5($password);

		// Checking is user existing in the database or not
		$query = "SELECT * FROM `users` WHERE username = '$username' and password = '$password'";
		$result = mysqli_query($con,$query) or die(mysql_error());
		$rows = mysqli_num_rows($result);

		if ($rows == 1) {
			$hour = time() + (7 * 24 * 3600); // A week
			setcookie('ID_your_site', $username, $hour, '/');
			setcookie('Key_your_site', $password, $hour, '/');
			header("Location: index.php"); // Redirect user to index.php
		} else {
?>
<div class="form">
  <h3>用户名或密码错误！</h3>
  <br/>请重新<a href="login.php" onclick="window.history.back(); return false;">登录</a>。
</div>
<?php
		}

	} else {
?>
<div class="form">
<h1>登录</h1>
<form action="" method="post" name="login">
<input type="text" name="username" placeholder="用户名" required />
<input type="password" name="password" placeholder="密码" required />
<br/>
<input name="submit" type="submit" value="登录" />
</form>
<p>没有注册，请点击<a href='registration.php'>注册</a>。</p>
<p>忘记密码，请点击<a href='resetpw.php'>重置密码</a>。</p>

</div>
<?php } ?>


</body>
</html>
