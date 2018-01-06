<?php
include("auth.php"); //include auth.php file on all secure pages

$insertFlag = false;
$configId = NULL;
$userConfig = NULL;

$query = "SELECT id, config, updateTime FROM `configs` WHERE userId = '$userId'";
$result = mysqli_query($con, $query) or die(mysql_error());
$row = mysqli_fetch_row($result);

$pin = '';
$tgt = '';
$ctype = '';
$appleFlag = '';
$androidFlag = '';
$uuid = '';
$username = '';
$lastUpdateTime = NULL;

if (NULL != $row) {
	$configId = $row[0];

	$userConfig = json_decode($row[1]);
	$userConfig = $userConfig->user;

	$loginConfig = $userConfig->login;

	$pin = $loginConfig->pin;
	$tgt = $loginConfig->tgt;
	$ctype = $loginConfig->ctype;

	if ('apple' == $ctype) {
		$appleFlag = 'selected';
		$androidFlag = '';
	} else {
		$appleFlag = '';
		$androidFlag = 'selected';
	}

	$uuid = $loginConfig->uuid;

	$ploginConfig = $userConfig->plogin;
	$username = $ploginConfig->username;

	$lastUpdateTime = $row[2];
}

// If form submitted, query values from the database.
if (isset($_POST['pin']) || isset($_POST['username'])) {

	$pin = stripslashes($_REQUEST['pin']); // removes backslashes
	$pin = mysqli_real_escape_string($con, $pin); //escapes special characters in a string

	$tgt = stripslashes($_REQUEST['tgt']);
	$tgt = mysqli_real_escape_string($con, $tgt);

	$ctype = stripslashes($_REQUEST['ctype']);
	$ctype = mysqli_real_escape_string($con, $ctype);

	$uuid = stripslashes($_REQUEST['uuid']);
	$uuid = mysqli_real_escape_string($con, $uuid);

	$username = stripslashes($_REQUEST['username']);
	$username = mysqli_real_escape_string($con, $username);

	$password = stripslashes($_REQUEST['password']);
	$password = mysqli_real_escape_string($con, $password);
	$password = md5($password);

	$userConfig = '{ "user": { "login": { "pin": "'.$pin.'", "tgt": "'.$tgt.'", "ctype": "'.$ctype.'", "uuid": "'.$uuid.'" }, "plogin": { "username": "'.$username.'", "password": "'.$password.'" } } }';

	if ($configId) {
		$sql = "UPDATE `configs`
			SET `config` = '".$userConfig."', `updateTime` = CURRENT_TIMESTAMP
			WHERE `id` = ".$configId;
	} else {
		$sql = "INSERT INTO `configs`
			( `id`, `userId`, `config`, `createTime`, `updateTime` )
			VALUES
			( NULL, '$userId', '$userConfig', NULL, NULL);";
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
<title>Configurations</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="form">
<p>
<?php
if ($insertFlag) {
	echo('Configurations is saved.');
} else if ($lastUpdateTime) {
	echo('Configurations were updated at '.$lastUpdateTime);
} else {
	echo('New configurations');
}
?>
</p>
<form action="" method="post" name="login">
  <input type="text" name="pin" value="<?php echo($pin);?>" placeholder="PIN" required />
  <input type="text" name="tgt" value="<?php echo($tgt);?>" placeholder="TGT" required />
  <select type="select" name='ctype'>
    <option value="android" <?php echo($androidFlag);?> >ANDROID</option>
    <option value="apple" <?php echo($appleFlag);?> >APPLE</option>
  </select>
  <input type="text" name="uuid" value="<?php echo($uuid);?>" placeholder="UUID" required />
  <hr/>
  <input type="text" name="username" value="<?php echo($username);?>" placeholder="Username" required />
  <input type="password" name="password" value="Hello world!" placeholder="Password" required />
  <input name="submit" type="submit" value="Update" />
</form>

<p><a href="index.php">Home</a></p>
<a href="logout.php">Logout</a>

</div>
</body>
</html>
