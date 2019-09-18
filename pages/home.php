<script type="text/javascript">
	$(document).ready(function() {
		$('a.action-button').on('click', function(e) {
			var id = $(this).attr('id');
			if($(this).hasClass('green')) {
				var on_off = 'on';
			}
			else {
				var on_off = 'off';
			}
			$.ajax({
				type: 'POST',
				url: 'scripts/call_switch_lights.php',
				data: { 'light': id, 'on_off': on_off },
				success: function callback(returnvalue) {
					if(returnvalue == 1) {
						alert('Erfolg!');
					}
					else {
						alert('Fehler! Evtl. ist Ihre SESSION abgelaufen. Laden Sie die Seite neu oder kontaktieren Sie den Administrator!');
					}
				}
			});
		});
	});
</script>
<?php
	function space($k) {
		for($i = 1; $i <= $k; $i++) {
			echo "<br>";
		}
	}

	session_start();
	if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
		require_once("../mysql_functions.php");
		$link = db_connect();
		if($link) {
			$id = $_SESSION['userid'];
			$result = mysqli_query($link, "SELECT socket1, socket2, socket3 FROM permissions WHERE userid LIKE $id LIMIT 1");
			$row = mysqli_fetch_object($result);
			$socket1 = $row->socket1;
			$socket2 = $row->socket2;
			$socket3 = $row->socket3;
			$result = mysqli_query($link, "SELECT name_socket1, name_socket2, name_socket3 FROM general LIMIT 1");
			$row = mysqli_fetch_object($result);
			$name_socket1 = $row->name_socket1;
			$name_socket2 = $row->name_socket2;
			$name_socket3 = $row->name_socket3;
		}
	}
	if(isset($socket1) && $socket1) {
		echo "<div id=\"button_title\">";
		echo $name_socket1;
		echo "</div>";
		echo "<a href=\"#\" id=\"1\" class=\"action-button animate green\">AN</a>";
		echo "<a href=\"#\" id=\"1\" class=\"action-button animate red\">AUS</a>";
		space(4);
	}
	if(isset($socket2) && $socket2) {
		space(2);
		echo "<div id=\"button_title\">";
		echo $name_socket2;
		echo "</div>";
		echo "<a href=\"#\" id=\"2\" class=\"action-button animate green\">AN</a>";
		echo "<a href=\"#\" id=\"2\" class=\"action-button animate red\">AUS</a>";
		space(4);
	}
	if(isset($socket3) && $socket3) {
		space(2);
		echo "<div id=\"button_title\">";
		echo $name_socket3;
		echo "</div>";
		echo "<a href=\"#\" id=\"3\" class=\"action-button animate green\">AN</a>";
		echo "<a href=\"#\" id=\"3\" class=\"action-button animate red\">AUS</a>";
		space(4);
	}
?>
