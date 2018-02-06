<?php

include("auth.php"); //include auth.php file on all secure pages ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Welcome Home</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="form">
<p>Welcome <?php echo $_COOKIE['ID_your_site']; ?>!</p>
<p>This is secure area.</p>
<p><a href="dashboard.php">Dashboard</a></p>
<p><a href="config.php">Configurations</a></p>
<p><a href="entry.php">Entry Login</a></p>
<p><a href="../view/viewer.php">View</a></p>
<a href="logout.php">Logout</a>

</div>
</body>
</html>
