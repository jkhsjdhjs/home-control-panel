<?php
	require_once("../mysql_functions.php");
	session_start();
	if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
		$link = db_connect();
		if($link) {
			$id = $_SESSION['userid'];
			$result = mysqli_query($link, "SELECT email FROM user WHERE userid LIKE $id LIMIT 1");
			$row = mysqli_fetch_object($result);
			$email = $row->email;
		}
	}
?>
<script type="text/javascript">
	$(document).ready(function() {
		function change_mail() {
			var email = $('#new_email').val();
			$.ajax({
				type: 'POST',
				url: 'scripts/new_mail.php',
				data: { 'email': email },
				success: function callback(returnvalue) {
					if(returnvalue == 'already_in_use') {
						$('#new_email').val('Bereits vergeben!');
						$('#new_email').select();
					}
					else if(returnvalue == 'valid_link_already_sent') {
						alert('An diese E-Mail Adresse wurde bereits ein noch gültiger Aktivierungslink gesendet!');
					}
					else if(returnvalue == '1') {
						alert('Es wurde eine E-Mail mit weiteren Instruktionen an Ihre neue E-Mail Adresse versendet!');
					}
					else {
						alert(returnvalue);
					}
				}
			});
		}
		$('#save').on('click', function(e) {
			change_mail();
		});
		$('input').keypress(function(e) {
			if(e.which == 13) {
				change_mail();
			}
		});
	});
</script>
E-Mail Adresse ändern<br>
Jetzige E-Mail Adresse: <a id="current_email"><?php echo $email; ?></a><br>
<input type="text" id="new_email" placeholder="Neue E-Mail Adresse"><br>
<button id="save">Speichern</button>
