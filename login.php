<?php
	function validate_email($e_mail) {
		//E-Mail Adresse auf richtiges Format überprüfen
		if (!preg_match("^([a-zA-Z0-9\\-\\.\\_]+)(\\@)([a-zA-Z0-9\\-\\.]+)(\\.)([a-zA-Z]{2,4})$^", $e_mail)) {
			return false;
		}
		else {
			return true;
		}
	}

	//Varialben aus config.php lesen
	require_once("config.php");
	require_once("mysql_functions.php");
	session_start();
	if(!$_SESSION['loggedIn']) {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$login_failed = false;
			$usernameoremail = $_POST['username'];
			$password = md5($_POST['password']);
			$remember = $_POST['remember'];
			//Verbinden...
			$connection = db_connect();
			//Zeile, in der der eingegebene Nutzername mit dem in der DB übereinstimmt finden und auslesen
			if(!validate_email($usernameoremail)) {
				$result = mysqli_query($connection, "SELECT * FROM user LEFT JOIN permissions ON user.userid = permissions.userid WHERE username LIKE '$usernameoremail' LIMIT 1");
			}
			else {
				$result = mysqli_query($connection, "SELECT * FROM user LEFT JOIN permissions ON user.userid = permissions.userid WHERE email LIKE '$usernameoremail' LIMIT 1");
			}
			//Werte der MySQL DB in Objekt umwandeln
			$row = mysqli_fetch_object($result);
			$username = $row->username;
			//falls das eingegebene PW mit dem der DB übereinstimmt...
			if ($row->password == $password) {
				//falls der User angemeldet bleiben will..
				if($remember == true) {
					//Cookies setzen
					setcookie('remember_me_user', $username, time() + 31536000);
					setcookie('remember_me_pw', $password, time() + 31536000);
				}
				//Anzahl der Logins um 1 erhöhen
				mysqli_query($connection, "UPDATE user SET NumberOfLogins = NumberOfLogins + 1 WHERE username LIKE '$username' LIMIT 1");
				//MySQL Zeitformat in deutsches Format konvertieren
				$stamp = timestamp_mysql2german($row->LastLogin);
				//SESSION Variablen füllen
				$userid = $row->userid;
				$_SESSION['userid'] = $userid;
				$_SESSION['loggedIn'] = true;
				$_SESSION['username'] = $username;
				$_SESSION['NumberOfLogins'] = $row->NumberOfLogins + 1;
				$_SESSION['LastLoginDate'] = $stamp['date'];
				$_SESSION['LastLoginTime'] = $stamp['time'];
				$_SESSION['admin'] = $row->admin;
				$_SESSION['socket1'] = $row->socket1;
				$_SESSION['socket2'] = $row->socket2;
				$_SESSION['socket3'] = $row->socket3;
				//LastLogin in MySQL DB updaten
				mysqli_query($connection, "UPDATE user SET LastLogin = now() WHERE userid LIKE $userid LIMIT 1");
				//Weiterleitung zur index.php
				header('Location: index.php');
				exit;
			}
			else {
				$login_failed = true;
			}
		}
	}
	else {
		header('Location: index.php');
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
			 - Login
		</title>
	</head>
	<body>
		<div id="header">
			<?php
				echo strtolower($website_title);
			?>
		</div>
		<div id="body_content">
			<form action="login.php" method="post" class="login-form">
				<table class="login-table">
					<tr>
						<td>
							<span class="fontawesome-lock" style="color: green; font-size: 18px"></span><span class="login">Login</span>
						</td>
					</tr>
					<?php
						if($login_failed == true) {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-error\">";
							echo "Login fehlgeschlagen!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
					?>
					<tr>
						<td>
							<input type="text" placeholder="Nutzername oder E-Mail" name="username" class="login-input animate">
						</td>
					</tr>
					<tr>
						<td>
							<input type="password" placeholder="Passwort" name="password" class="login-input animate">
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" name="remember" id="login-checkbox" checked>
							<span id="login-checkbox-text">Angemeldet bleiben</span>
						</td>
					</tr>
					<tr>
						<td>
							<a href="reset_pw.php" title="Klicken Sie hier, falls Sie Ihr Passwort vergessen haben." id="forgot_pw_link">Passwort vergessen?</a>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" value="Anmelden" class="login-button animate">
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>
