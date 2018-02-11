<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>注册账号</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?php
	require('db.php');

	$result = null;

	// If form submitted, insert values into the database.
	if (isset($_REQUEST['username'])){
		$username = stripslashes($_REQUEST['username']); // removes backslashes
		$username = mysqli_real_escape_string($con,$username); //escapes special characters in a string
		$email = stripslashes($_REQUEST['email']);
		$email = mysqli_real_escape_string($con,$email);
		$password = stripslashes($_REQUEST['password']);
		$password = mysqli_real_escape_string($con,$password);

		$trn_date = date("Y-m-d H:i:s");
		$query = "INSERT into `users` (username, password, email, trn_date) VALUES ('$username', '".md5($password)."', '$email', '$trn_date')";
		$result = mysqli_query($con,$query);
		if($result){
			echo "<div class='form'><h3>您已经成功注册。</h3><br/>点击回到<a href='login.php'>首页</a>。</div>";
		} else {
			echo "<div class='form'><h3>邮箱或用户名已被注册，请换一个试试。</h3></div>";
		}
	}

	if (! $result) {
?>
<div class="form">
<h1>注册账号</h1>
<form name="registration" action="" method="post">
<input type="email" name="email" placeholder="Email" required />
<input type="text" name="username" placeholder="用户名" required />
<input type="password" name="password" placeholder="密码" required />
<input type="submit" name="submit" value="注册账号" />
</form>
</div>
<?php } ?>
</body>
</html>
