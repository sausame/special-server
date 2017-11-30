<?php

function viewFile($name, $path) {

?>
<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8" />
	<title><?php echo($name); ?></title>
</head>
<body>
<pre id='live'></pre>
<script src="base64js.min.js"></script>
<script>
<?php
	$file = base64_encode($path);
	echo('var path = "' . $file . '"');
	$data = '"file=' . $file . '&offset=" + offset';
?>

function getData() {

	var xhr = new XMLHttpRequest();

	xhr.onload = function() {

		if (200 === xhr.status) {

			res = JSON.parse(xhr.responseText);
			error = res['error'];

			if (0 === error['code']) {

				var data = base64js.toByteArray(res['data']);
				data = new TextDecoder('utf-8').decode(data);

				document.getElementById('live').innerHTML = document.getElementById('live').innerHTML
				   	+ data; // Update

				offset = res['offset'];

			} else {
				console.log(error);
			}
		}
	};

	xhr.open('POST', 'filecontent.php', true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(<?php echo($data); ?>);
}

var offset = 0;
setInterval(getData, 10000);
window.onload = getData(); 
</script> 
</body>
</html>

<?php
}
?>

