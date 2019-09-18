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
	function generateRandomString($length) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	function sendMail($username, $id, $email, $link, $overwrite) {
		$challenge = md5(generateRandomString(20)) . md5(generateRandomString(20));
		if(!$overwrite) {
			mysqli_query($link, "INSERT INTO change_email (userid, email, challenge_id, valid, timestamp) VALUES ($id, '$email', '$challenge', 1, now())");
		}
		else {
			mysqli_query($link, "UPDATE change_email SET email = '$email', challenge_id = '$challenge', valid = 1, timestamp = now() WHERE userid LIKE $id LIMIT 1");
		}
		$subject = "HCP - E-Mail Adresse ändern";
		$subject = "=?utf-8?b?".base64_encode($subject)."?=";
		$msg = "Hallo $username,<br><br>Sie haben soeben Ihre E-Mail Adresse im Home Control Panel ändern wollen.<br>Sollten Sie das nicht gewesen sein, ignorieren Sie diese Mail und ändern Sie ggf. Ihr Passwort.<br>Sollten Sie Ihre E-Mail Adresse ändern wollen, klicken Sie <a href=\"http://totally.rip/hcp/change_mail.php?UserID=$id&ChallengeID=$challenge\">HIER</a>, um Ihre neue E-Mail Adresse zu übernehmen.<br>Der Link ist 24 Stunden lang gültig.<br><br>Mit freundlichen Grüßen<br><br>Ihr Home Control Panel Team";
		$header = "From: Home Control Panel <noreply@totally.rip>\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=utf-8\r\n";
		mail($email, $subject, $msg, $header);
	}
	require_once("../mysql_functions.php");
	session_start();
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['email'])) {
		$email = $_POST['email'];
		if(validate_email($email)) {
			if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
				$link = db_connect();
				if($link) {
					$result = mysqli_query($link, "SELECT username FROM user WHERE email LIKE '$email' LIMIT 1");
					if(mysqli_num_rows($result) == 0) {
						$id = $_SESSION['userid'];
						$result = mysqli_query($link, "SELECT valid, timestamp FROM change_email WHERE userid LIKE $id AND email LIKE '$email' LIMIT 1");
						if(mysqli_num_rows($result) == 0) {
							$result = mysqli_query($link, "SELECT username FROM user WHERE userid LIKE $id LIMIT 1");
							$row = mysqli_fetch_object($result);
							$username = $row->username;
							sendMail($username, $id, $email, $link, false);
							echo "1";
						}
						else {
							$row = mysqli_fetch_object($result);
							if($row->valid) {
								if(time() - strtotime($row->timestamp) <= 86400) {
									echo "valid_link_already_sent";
								}
								else {
									$result = mysqli_query($link, "SELECT username FROM user WHERE userid LIKE $id LIMIT 1");
									$row = mysqli_fetch_object($result);
									$username = $row->username;
									sendMail($username, $id, $email, $link, true);
									echo "1";
								}
							}
							else {
								$result = mysqli_query($link, "SELECT username FROM user WHERE userid LIKE $id LIMIT 1");
								$row = mysqli_fetch_object($result);
								$username = $row->username;
								sendMail($username, $id, $email, $link, true);
								echo "1";
							}
						}
					}
					else {
						echo "already_in_use";
					}
				}
			}
		}
	}
?>
