<?php
	require_once("../mysql_functions.php");
	session_start();
	$link = db_connect();
	if($link) {
		$id = $_SESSION['userid'];
		$result = mysqli_query($link, "SELECT username FROM user WHERE userid LIKE $id");
		$row = mysqli_fetch_object($result);
		$username = $row->username;
	}
?>
<script type="text/javascript">
	function change_username(new_username) {
		$.ajax({
			type: 'POST',
			url: 'scripts/change_username.php',
			data: { 'user': new_username },
			success: function callback(returnvalue) {
				if(returnvalue == '1') {
					$('#old_username').text(new_username);
					$('#new_username').val('');
				}
				else if(returnvalue == 'username_taken') {
					$('#new_username').val('Bereits vergeben!');
					$('#new_username').select();
				}
			}
		});
	}
	
	$(document).ready(function() {
		$('#save').on('click', function(e) {
			change_username($('#new_username').val());
		});
		$('input').keypress(function(e) {
			if(e.which == 13) {
				change_username($('#new_username').val());
			}
		});
	});
</script>
<div id="abc">Benutzername ändern:</div>
Jetziger Benutzername: <a id="old_username"><?php echo $username; ?></a><br>
<input id="new_username" type="text" placeholder="Neuer Nutzername"><br>
<button id="save">Speichern</button>
