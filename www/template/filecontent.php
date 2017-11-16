<?php
$filename = '';
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8" />
  <title><?php echo $filename; ?></title>
</head>
<body>
<pre>
<?php
echo file_get_contents($filename);
?>
</pre>
</body>
</html>
