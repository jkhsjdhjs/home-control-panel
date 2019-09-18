<?php
	require_once 'config.php';
	require_once("mysql_functions.php");
	function validate_email($e_mail) {
		//E-Mail Adresse auf richtiges Format überprüfen
		if (!preg_match("^([a-zA-Z0-9\\-\\.\\_]+)(\\@)([a-zA-Z0-9\\-\\.]+)(\\.)([a-zA-Z]{2,4})$^", $e_mail)) {
			return false;
		}
		else {
			return true;
		}
	}
	
	function SETchallengeANDsendMAIL($userid, $username, $email) {
		$connection = db_connect();
		$challenge = md5(md5((string)mt_rand() . $_SERVER['REMOTE_ADDR'])) . md5(md5((string)mt_rand() . $_SERVER['REMOTE_ADDR']));
		mysqli_query($connection, "UPDATE reset_pw SET challenge_id = '$challenge' WHERE userid LIKE $userid");
		mysqli_query($connection, "UPDATE reset_pw SET valid = 1 WHERE userid LIKE $userid");
						
		$subject = "Home Control Panel - Passwort zurücksetzen";
		$sender = "moeller.mx <noreply@moeller.mx>";
		$message = "Hallo ".$username."!<br><br>Sie haben soeben ein neues Passwort für das Home Control Panel angefordert.<br>Sollten Sie diese E-Mail nicht ausgelöst haben, ignorieren Sie diese Mail einfach.<br>Klicken Sie einfach auf folgenden Link, welcher 24 Stunden gültig ist, und Sie können Ihre neues Passwort festlegen:<br><a href=http://moeller.mx/hcp/newpw.php?UserID=".$userid."&ChallengeID=".$challenge.">http://moeller.mx/hcp/newpw.php?UserID=".$userid."&ChallengeID=".$challenge."</a><br><br>Mit freundlichen Grüßen<br><br>Ihr moeller.mx Team";
						
		$header  = "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html; charset=UTF-8\r\n";
						 
		$header .= "From: $sender\r\n";
		// $header .= "Reply-To: $sender\r\n"; //für anwort an andere adresse
		// $header .= "Cc: $cc\r\n";  // falls auch Kopien gesendet werden sollen
		$header .= "X-Mailer: PHP ". phpversion();
		mail($email, $subject, $message, $header);
	}
	
	if((!empty($_POST['e-mail']) || !empty($_POST['username'])) && $_SERVER['REQUEST_METHOD'] == "POST") {
		$email= strip_tags(trim($_POST['e-mail']));
		$username = strip_tags(trim($_POST['username']));
		if(strlen($email) != 0) {
			if(!validate_email($email)) {
				$error = "email_not_valid";
			}
			else {
				$connection = db_connect();
				$result = mysqli_query($connection, "SELECT * FROM user WHERE email LIKE '$email'");
				if($result !== false) {
					if(mysqli_num_rows($result) !== 1) {
						$error = "email_not_in_db";
					}
					else if(mysqli_num_rows($result) === 1) {
						$row = mysqli_fetch_object($result);
						$userid = $row->userid;
						$user = $row->username;
						SETchallengeANDsendMAIL($userid, $user, $email);
						$error = false;
					}
					else {
						$error = "unknown_exception";
					}
				}
				else {
					$error = "email_not_in_db";
				}
			}
		}
		else if(strlen($username) != 0) {
			$connection = db_connect();
			$result = mysqli_query($connection, "SELECT * FROM user WHERE username LIKE '$username'");
			if($result !== false) {
				if(mysqli_num_rows($result) !== 1) {
					$error = "username_not_in_db";
				}
				else if(mysqli_num_rows($result) === 1) {
					$row = mysqli_fetch_object($result);
					$userid = $row->userid;
					$email = $row->email;
					SETchallengeANDsendMAIL($userid, $username, $email);
					$error = false;
				}
				else {
					$error = "unknown_exception";
				}
			}
			else {
				$error = "username_not_in_db";
			}
		}
		else {
			$error = "no_input";
		}
	}
	else {
		$error = "no_input";
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
			 - Passwort vergessen
		</title>
	</head>
	<body>
		<div id="header">
			<?php
				echo strtolower($website_title);
			?>
		</div>
		<div id="body_content">
			<form action="reset_pw.php" method="post" class="login-form">
				<table class="login-table">
					<tr>
						<td>
							<span class="fontawesome-key" style="color: green; font-size: 18px"></span><span class="login">Passwort zurücksetzen</span>
						</td>
					</tr>
					<?php
						if($_SERVER['REQUEST_METHOD'] == "POST") {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box ";
							if($error === false) {
								echo "box-success small-box\">";
								echo "Aktivierungsmail wurde erfolgreich an ".$email." versendet!";
							}
							else if($error == "email_not_in_db") {
								echo "box-error small-box\">";
								echo "Diese E-Mail Adresse ist nicht vorhanden!";
							}
							else if($error == "email_not_valid") {
								echo "box-error small-box\">";
								echo "Diese E-Mail Adresse hat kein gültiges Format!";
							}
							else if($error == "username_not_in_db") {
								echo "box-error small-box\">";
								echo "Diese Benutzername ist nicht vorhanden!";
							}
							else if($error == "no_input") {
								echo "box-error small-box\">";
								echo "Sie haben nichts eingegeben!";
							}
							else if($error == "unknown_exception") {
								echo "box-error small-box\">";
								echo "Ein unbekannter Fehler ist aufgetreten!";
							}
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
						if(!isset($error) || $error !== false) {
							echo "<tr>";
							echo "<td>";
							echo "<a id=\"forgot_pw_link\">Geben Sie hier Ihren Nutzernamen<br>ODER Ihre E-Mail Adresse ein:</a>";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"text\" placeholder=\"E-Mail Adresse\" name=\"e-mail\" class=\"login-input animate\">";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"text\" placeholder=\"Nutzername\" name=\"username\" class=\"login-input animate\">";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"submit\" value=\"Aktivierungslink anfordern\" class=\"login-button animate pw-reset-button\">";
							echo "</td>";
							echo "</tr>";
						}
					?>
				</table>
			</form>
		</div>
	</body>
</html>
