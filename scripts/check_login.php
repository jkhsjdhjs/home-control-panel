<?php
	function validate_email($e_mail) {
		// E-Mail Adresse auf richtiges Format überprüfen
		if (!preg_match("^([a-zA-Z0-9\\-\\.\\_]+)(\\@)([a-zA-Z0-9\\-\\.]+)(\\.)([a-zA-Z]{2,4})$^", $e_mail)) {
			return false;
		}
		else {
			return true;
		}
	}
	if(isset($_POST['user']) && isset($_POST['pw']) && $_POST['pw'] != "d41d8cd98f00b204e9800998ecf8427e" && isset($_POST['remember_me']) && $_SERVER['REQUEST_METHOD'] == "POST") {
		require_once("../mysql_functions.php");
		$link = db_connect();
		if($link) {
			$usernameoremail = $_POST['user'];
			$password = $_POST['pw'];
			$remember = $_POST['remember_me'];
			if(!validate_email($usernameoremail)) {
				$result = mysqli_query($link, "SELECT * FROM user LEFT JOIN permissions ON user.userid = permissions.userid WHERE username LIKE '$usernameoremail' LIMIT 1");
			}
			else {
				$result = mysqli_query($link, "SELECT * FROM user LEFT JOIN permissions ON user.userid = permissions.userid WHERE email LIKE '$usernameoremail' LIMIT 1");
			}
			$row = mysqli_fetch_object($result);
			if($row->password == $password) {
				// falls der User angemeldet bleiben will...
				if($remember) {
					// ...Cookies setzen
					setcookie('remember_me_userid', $row->userid, time() + 31536000, "/hcp/");
					setcookie('remember_me_pw', $password, time() + 31536000, "/hcp/");
				}
				$userid = $row->userid;
				// LastLogin in MySQL DB updaten
				mysqli_query($link, "UPDATE user SET LastLogin2 = LastLogin WHERE userid LIKE $userid LIMIT 1");
				mysqli_query($link, "UPDATE user SET LastLogin = now() WHERE userid LIKE $userid LIMIT 1");
				// Anzahl der Logins um 1 erhöhen
				mysqli_query($link, "UPDATE user SET NumberOfLogins = NumberOfLogins + 1 WHERE userid LIKE $userid LIMIT 1");
				// SESSION Variablen füllen
				session_start();
				$_SESSION['userid'] = $row->userid;
				$_SESSION['loggedIn'] = true;
				echo true;
			}
			else {
				echo false;
			}
		}
		else {
			echo false;
		}
	}
	else {
		echo false;
	}
?>
