<?php

function viewFile($base64Path) {

	$name = "live" . time();
?>
<div>
	<p>
		<button onclick="clearData()" id='clearBtn'>Clear</button>
		<button onclick="reloadData()" id='reloadBtn'>Reload</button>
	</p>
	<hr/>
		<pre id='<?php echo($name); ?>'></pre>
	<hr/>
	<p>
		<button onclick="clearData()" id='clearBtn'>Clear</button>
		<button onclick="reloadData()" id='reloadBtn'>Reload</button>
	</p>
	<script src="base64js.min.js"></script>
	<script>

		function getData() {

			var xhr = new XMLHttpRequest();

			xhr.onload = function() {

				if (200 === xhr.status) {

					res = JSON.parse(xhr.responseText);
					error = res['error'];

					if (0 === error['code']) {

						var data = base64js.toByteArray(res['data']);
						data = new TextDecoder('utf-8').decode(data);

						document.getElementById(name).innerHTML = document.getElementById(name).innerHTML
							+ data; // Update

						offset = res['offset'];

					} else {
						console.log(error);
					}
				}
			};

			xhr.open('POST', 'filecontent.php', true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send('file=' + path + '&offset=' + offset);
		}

		function clearData() {
			document.getElementById(name).innerHTML = '';
		}

		function reloadData() {

			offset = 0;

			clearData();
			getData();
		}

		var name = "<?php echo($name); ?>";
		var path = "<?php echo($base64Path); ?>";
		var offset = 0;

		setInterval(getData, 10000);

		window.onload = getData();

	</script>

</div>

<?php
}
?>

