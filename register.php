<?php
	require_once 'config.php';
	require_once("mysql_functions.php");
	if(!empty($_GET['email']) && !empty($_GET['validationID']) && isset($_GET['email']) && isset($_GET['validationID'])) {
		$validationID = $_GET['validationID'];
		$email = $_GET['email'];
		$connection = db_connect();
		$result = mysqli_query($connection, "SELECT * FROM pending_users WHERE email LIKE '$email' LIMIT 1");
		$numberofrows = mysqli_num_rows($result);
		if($numberofrows == 1) {
			$row = mysqli_fetch_object($result);
			$timestamp = $row->timestamp;
			$validationIDdb = $row->validationID;
			$admin = $row->admin;
			$socket1 = $row->socket1;
			$socket2 = $row->socket2;
			$socket3 = $row->socket3;
			if($validationID == $validationIDdb) {
				if(time() - strtotime($timestamp) <= 86400) {
					if(isset($_POST['username']) && isset($_POST['pw']) && isset($_POST['repeat_pw']) && !empty($_POST['username']) && !empty($_POST['pw']) && !empty($_POST['repeat_pw'])) {
						$pw = md5($_POST['pw']);
						$repeat_pw = md5($_POST['repeat_pw']);
						if($pw == $repeat_pw) {
							$username = $_POST['username'];
							$result = mysqli_query($connection, "SELECT * FROM user WHERE username LIKE '$username' LIMIT 1");
							$numberofrows = mysqli_num_rows($result);
							if($numberofrows == 0) {
								mysqli_query($connection, "INSERT INTO user (username, password, email, AccountCreation) VALUES ('$username', '$pw', '$email', now())");
								$userid = mysqli_insert_id($connection);
								mysqli_multi_query($connection, "INSERT INTO permissions (userid, admin, socket1, socket2, socket3) VALUES ($userid, $admin, $socket1, $socket2, $socket3);
																INSERT INTO reset_pw (userid, valid) VALUES ($userid, 0);
																DELETE FROM pending_users WHERE email LIKE '$email'");
								$error = false;
							}
							else if ($numberofrows == 1) {
								$error = "username_taken";
							}
							else {
								$error = "unknown_exception";
							}
						}
						else {
							$error = "pw_no_match";
						}
					}
				}
				else {
					$error = "time_ran_out";
				}
			}
			else {
				$error = "invalid_link";
			}
		}
		else {
			$error = "unknown_exception";
		}
	}
	else {
		$error = "invalid_link";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=<?php echo "$website_charset"; ?>">
		<?php
			if($error === false) {
				echo "<meta http-equiv=\"refresh\" content=\"3; url=http://moeller.mx/hcp/\">";
			}
		?>
		<link rel="stylesheet" href="style.css">
		<title>
			<?php
				echo $website_title;
			?>
			 - Registrierung
		</title>
	</head>
	<body>
		<div id="header">
			<?php
				echo strtolower($website_title);
			?>
		</div>
		<div id="body_content">
			<form action="register.php?email=<?php echo $email; ?>&validationID=<?php echo $validationID; ?>" method="post" class="login-form">
				<table class="login-table">
					<tr>
						<td>
							<span class="fontawesome-lock" style="color: green; font-size: 18px"></span><span class="login">Registrierung</span>
						</td>
					</tr>
					<?php
						if ($error == "time_ran_out") {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-error small-box\">";
							echo "Aktivierungslink ist bereits abgelaufen!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
						else if ($error == "invalid_link") {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-error small-box\">";
							echo "Ungültiger Link!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
						else if ($error == "pw_no_match") {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-error small-box\">";
							echo "Passwörter stimmen nicht überein!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
						else if ($error == "username_taken") {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-error small-box\">";
							echo "Nutzername bereits vergeben!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
						if ((!isset($error) || $error !== false) && $error != "invalid_link" && $error != "time_ran_out") {
							echo "<tr>";
							echo "<td>";
							echo "<a id=\"forgot_pw_link\">Hier können Sie Ihren Nutzernamen<br>und Ihr Passwort festlegen:</a>";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"text\" placeholder=\"Nutzername\" name=\"username\" class=\"login-input animate\">";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"password\" placeholder=\"Passwort\" name=\"pw\" class=\"login-input animate\">";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"password\" placeholder=\"Passwort wiederholen\" name=\"repeat_pw\" class=\"login-input animate\">";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"submit\" value=\"Passwort speichern\" class=\"login-button animate pw-reset-button\">";
							echo "</td>";
							echo "</tr>";
						}
						else if ($error == false) {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-success small-box\">";
							echo "Sie haben sich erfolgreich registriert!<br>In 3 Sekunden werden Sie zum Login bereich weitergeleitet!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
					?>
				</table>
			</form>
		</div>
	</body>
</html>
