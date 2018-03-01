<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" />
<title>重置密码</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?php
	require('db.php');

	$isSent = false;

	// If form submitted, insert values into the database.
	if (isset($_REQUEST['email'])){
		$email = stripslashes($_REQUEST['email']);
		$email = mysqli_real_escape_string($con,$email);

		$query = "SELECT id FROM `users` WHERE email = '$email'";
		$result = mysqli_query($con, $query);

		if ($result) {
			$rows = mysqli_num_rows($result);
			if($rows > 0) {
				echo "<div class='form'><h3>邮件已发送，请根据邮件内容进行下一步操作。</h3><br/>点击回到<a href='login.php'>首页</a>。</div>";
				$isSent = true;
			}
		} else {
			echo "<div class='form'><h3>邮箱或用户名已被注册，请换一个试试。</h3></div>";
		}

		if (! $isSent) {
			echo "<div class='form'><h3>邮箱未注册，请换一个试试。</h3></div>";
		}
	}

	if (! $isSent) {
?>
<div class="form">
<h1>密码重置</h1>
<p>请输入您注册时使用的邮箱地址：</p>
<form name="resetpw" action="" method="post">
<input type="email" name="email" placeholder="Email" required />
<input type="submit" name="submit" value="重置密码" />
</form>
</div>
<?php } ?>
</body>
</html>
