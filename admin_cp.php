<?php
	//Varialben aus config.php lesen
	require_once("config.php");
	require_once("mysql_functions.php");
	require_once("auth.php");
	if($_SESSION['admin'] == false) { //Falls kein Admin...
		//Weiterleitung zur index.php
		header('Location: index.php');
	}
	else {
		$connection = db_connect();
		$result = mysqli_query($connection, "SELECT * FROM permissions");
		$lines = mysqli_num_rows($result);
		for($i = 1; $i <= $lines; $i++) {
			$k = $i - 1;
			$result = mysqli_query($connection, "SELECT * FROM permissions LIMIT $k, 1");
			$row[$i] = mysqli_fetch_object($result);
		}
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$changes = false;
			for($i = 1; $i <= $lines; $i++) {
				$userid = $row[$i]->userid;
				$result = mysqli_query($connection, "SELECT username FROM user WHERE userid LIKE $userid");
				$row2 = mysqli_fetch_object($result);
				$username = $row2->username;
				$admin = $_POST['admin-'.$userid];
				$socket1 = $_POST['socket1-'.$userid];
				$socket2 = $_POST['socket2-'.$userid];
				$socket3 = $_POST['socket3-'.$userid];
				if($admin == "on") {
					$admin = true;
				}
				else {
					$admin = false;
				}
				if($socket1 == "on") {
					$socket1 = true;
				}
				else {
					$socket1 = false;
				}
				if($socket2 == "on") {
					$socket2 = true;
				}
				else {
					$socket2 = false;
				}
				if($socket3 == "on") {
					$socket3 = true;
				}
				else {
					$socket3 = false;
				}
				if($row[$i]->admin != $admin) {
					$changes = true;
					$row[$i]->admin = $admin;
					mysqli_query($connection, "UPDATE permissions SET admin = '$admin' WHERE userid LIKE $userid");
				}
				if($row[$i]->socket1 != $socket1) {
					$changes = true;
					$row[$i]->socket1 = $socket1;
					mysqli_query($connection, "UPDATE permissions SET socket1 = '$socket1' WHERE userid LIKE $userid");
				}
				if($row[$i]->socket2 != $socket2) {
					$changes = true;
					$row[$i]->socket2 = $socket2;
					mysqli_query($connection, "UPDATE permissions SET socket2 = '$socket2' WHERE userid LIKE $userid");
				}
				if($row[$i]->socket3 != $socket3) {
					$changes = true;
					$row[$i]->socket3 = $socket3;
					mysqli_query($connection, "UPDATE permissions SET socket3 = '$socket3' WHERE userid LIKE $userid");
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=<?php echo "$website_charset"; ?>">
		<link rel="stylesheet" href="style.css">
		<title>
			<?php
				echo $website_title;
			?>
			 - Administration
		</title>
		<script type="text/javascript" src="/js/jquery.min.js"></script>
		<script type="text/javascript">
			function checkMail(email) {
				var result = "valid",
					regX;
				if (email == "") {
					result = "email_empty";
				}
				else if (typeof(RegExp) == 'function') {
					regX = new RegExp('^([a-zA-Z0-9\\-\\.\\_]+)(\\@)([a-zA-Z0-9\\-\\.]+)(\\.)([a-zA-Z]{2,4})$');
					if (!regX.test(email)) {
						result = "not_valid";
					}
				}
				return result;
			}
			var hidden = true;
			function ShowAddNewUser() {
				if (hidden == true) {
					$('#NewUser').load('html/AddNewUser.html');
					hidden = false;
				}
			}
			function hideFields() {
				if (hidden == false) {
					$('#info').text("");
					$('#NewUser').text("");
					hidden = true;
				}
			}
			function AddNewUser() {
				var email = document.getElementById("e-mail-address").value;
				var emailcheck = document.getElementById("e-mail-address-check").value;
				var mail_valid_or_not = checkMail(email);
				if (mail_valid_or_not == "not_valid") {
					alert('Die eingegebene E-Mail Adresse ist nicht gültig!');
				}
				else if (mail_valid_or_not == "email_empty") {
					alert('Sie haben keine E-Mail Adresse eingegeben!');
				}
				else if (mail_valid_or_not == "valid") {
					if(email != emailcheck) {
						alert('Die eingegebenen E-Mail Adressen stimmen nicht überein!');
					}
					else {
						if (!document.getElementById("admin").checked) {
							var admin = 0;
						}
						else {
							var admin = 1;
						}
						if (!document.getElementById("socket1").checked) {
							var socket1 = 0;
						}
						else {
							var socket1 = 1;
						}
						if (!document.getElementById("socket2").checked) {
							var socket2 = 0;
						}
						else {
							var socket2 = 1;
						}
						if (!document.getElementById("socket3").checked) {
							var socket3 = 0;
						}
						else {
							var socket3 = 1;
						}
						jQuery.ajax({
							type: "POST",
							url: "addnewuser.php",
							data: "email=" + email + "&admin=" + admin + "&socket1=" + socket1 + "&socket2=" + socket2 + "&socket3=" + socket3,
							success: function callback(returnvalue){
								switch(returnvalue) {
									case "success":
										$('#NewUser').text("");
										$('#info').load('html/AddNewUser_success.html');
										window.setTimeout(hideFields, 10000);
										break;
									case "post_not_used":
										alert('Error! Die POST Methode wurde bei der Datenübertragung nicht genutzt!<br>Kontaktieren Sie den Seiten-Administrator!');
										break;
									case "not_an_admin":
										alert('Error! Entweder sind Sie nicht eingeloggt oder kein Administrator!');
										break;
									case "email_already_got_valid_invite":
										alert('Error! An diese E-Mail Adresse wurde bereits ein Invite geschickt, der noch gültig ist!');
										break;
									case "email_already_assigned_to_acc":
										alert('Error! Es existiert bereits ein Nutzer mit dieser E-Mail Adresse!');
										break;
									case "email_not_valid":
										alert('Error! Die eingegebene E-Mail Adresse ist nicht gültig!');
										break;
									case "other_error":
										alert('Error! Unbekannter Fehler! Bitte kontaktieren Sie den Seiten-Administrator!');
										break;
									default:
										alert('Error! Nicht definierter Fehler! Kontaktieren Sie den Seiten-Administrator!');
								}
							}
						})
					}
				}
				else {
					alert('Error! Bitte kontaktieren Sie den Seiten-Administrator!');
				}
			}
			document.onkeydown = function(event) {
				if(event.keyCode == 13 && hidden == false) {
					AddNewUser();
				}
			}
			function deleteUser(userid, username) {
				if(!confirm('Möchten Sie den Nutzer "' + username + '" mit der ID ' + userid + ' wirklich löschen?')) {
				}
				else {
					jQuery.ajax({
							type: "POST",
							url: "deleteUser.php",
							data: "id=" + userid + "&username=" + username,
							success: function callback(returnvalue){
								switch(returnvalue) {
									case "success":
										alert('Der Nutzer "' + username + '" mit der ID ' + userid + ' wurde erfolgreich gelöscht!');
										window.location.href = "admin_cp.php";
										break;
									case "post_not_used":
										alert('Error! Die POST Methode wurde bei der Datenübertragung nicht genutzt!<br>Kontaktieren Sie den Seiten-Administrator!');
										break;
									case "not_an_admin":
										alert('Error! Entweder sind Sie nicht eingeloggt oder kein Administrator!');
										break;
									case "no_data_transmitted":
										alert('Error! Es wurden keine Daten an das PHP Skript übertragen! Laden Sie die Seite neu und/oder kontaktieren Sie den Seiten-Administrator!');
										break;
									case "no_user_w/_id":
										alert('Error! Es existiert kein Nutzer mit der angegebene ID ' + userid + '! Laden Sie die Seite neu und/oder kontaktieren Sie den Seiten-Administrator!');
										break;
									case "cannot_delete_yourself":
										alert('Error! Sie können sich nicht selbst löschen!');
										break;
									default:
										alert('Error! Nicht definierter Fehler! Kontaktieren Sie den Seiten-Administrator!');
								}
							}
						})
				}
			}
		</script>
	</head>
	<body>
		<div id="header">
			<?php
				echo strtolower($website_title);
			?>
		</div>
		<div id="body_content">
			<div id="welcome">
				Eingeloggt als 
				<?php
					echo "<span class=\"welcome-highlighted\">".$_SESSION['username']."</span>";
				?>
			</div>
			<div id = "admin_cp_link">
				<a href="index.php">Zurück</a>
			</div>
			<br>
			<br>
			<br>
			<br>
			<?php
				if($changes == true) {
					echo "<div class=\"box box-success\">";
					echo "Änderungen erfolgreich gespeichert!<br>";
					echo "Die Änderungen werden beim nächsten Login des jeweiligen Nutzers wirksam.";
					echo "</div>";
				}
			?>
			<br>
			<form action="admin_cp.php" method="POST">
				<span id="title">Benutzerverwaltung</span>
				<table id="admin_cp_table" border="1">
					<tr>
						<td>
						</td>
						<td>
							User-ID
						</td>
						<td>
							Benutzername
						</td>
						<td>
							E-Mail Adresse
						</td>
						<td>
							Administrator
						</td>
						<?php
							for ($i = 1; $i <= 3; $i++) {
								echo "<td>";
								echo "Steckdose ".$i;
								echo "</td>";
							}
						?>
					</tr>
					<?php
						for($i = 1; $i <= $lines; $i++) {
							$userid = $row[$i]->userid;
							$result = mysqli_query($connection, "SELECT username, email FROM user WHERE userid LIKE $userid");
							$row2 = mysqli_fetch_object($result);
							$username = $row2->username;
							$email = $row2->email;
							echo "<tr>";
							echo "<td>";
							if($_SESSION['username'] != $username) {
								echo "<a href=\"#\" onClick=\"deleteUser($userid, '$username');\"><img src=\"img/delete_button.png\" width=\"15px\" alt=\"Delete Button\"></a>";
							}
							echo "</td>";
							echo "<td align=\"right\">";
							echo $userid;
							echo "</td>";
							echo "<td>";
							echo $username;
							echo "</td>";
							echo "<td>";
							echo $email;
							echo "</td>";
							echo "<td>";
							echo "<input type=\"checkbox\" name=\"admin-".$row[$i]->userid."\"";
							if($row[$i]->admin == true) {
								echo " checked>";
							}
							else {
								echo ">";
							}
							echo "</td>";
							echo "<td>";
							echo "<input type=\"checkbox\" name=\"socket1-".$row[$i]->userid."\"";
							if($row[$i]->socket1 == true) {
								echo " checked>";
							}
							else {
								echo ">";
							}
							echo "</td>";
							echo "<td>";
							echo "<input type=\"checkbox\" name=\"socket2-".$row[$i]->userid."\"";
							if($row[$i]->socket2 == true) {
								echo " checked>";
							}
							else {
								echo ">";
							}
							echo "</td>";
							echo "<td>";
							echo "<input type=\"checkbox\" name=\"socket3-".$row[$i]->userid."\"";
							if($row[$i]->socket3 == true) {
								echo " checked>";
							}
							else {
								echo ">";
							}
							echo "</td>";
							echo "</tr>";
						}
					?>
				</table>
				<br>
				<button type="submit">Änderungen speichern</button>
			</form>
			<button onClick="ShowAddNewUser();">Neuen Benutzer hinzufügen</button>
			<div id="info">
			</div>
			<div id="NewUser">
			</div>
			<br>
			<br>
		</div>
	</body>
</html>
