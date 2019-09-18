<?php
	function generateRandomString($length) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	require_once("../mysql_functions.php");
	session_start();
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['pw'])) {
		if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
			$link = db_connect();
			if($link) {
				$id = $_SESSION['userid'];
				$pw = $_POST['pw'];
				$result = mysqli_query($link, "SELECT username, password, email FROM user WHERE userid LIKE $id LIMIT 1");
				$row = mysqli_fetch_object($result);
				$old_pw = $row->password;
				$email = $row->email;
				$username = $row->username;
				$result = mysqli_query($link, "SELECT * FROM changed_pw WHERE userid LIKE $id LIMIT 1");
				$challenge = md5(generateRandomString(20)) . md5(generateRandomString(20));
				if(mysqli_num_rows($result) == 0) {
					mysqli_query($link, "INSERT INTO changed_pw (userid, old_pw, challenge_id, timestamp) VALUES ($id, '$old_pw', '$challenge', now())");
				}
				else {
					mysqli_query($link, "UPDATE changed_pw SET old_pw = '$old_pw', challenge_id = '$challenge', timestamp = now() WHERE userid LIKE $id LIMIT 1");
				}
				$subject = "HCP - Passwort geändert";
				$subject = "=?utf-8?b?".base64_encode($subject)."?=";
				$msg = "Hallo $username,<br><br>Soeben wurde das Passwort für Ihren Account beim Home Control Panel geändert.<br>Sollten Sie das nicht gewesen sein, können Sie auf <a href=\"http://moeller.mx/hcp/recover_pw.php?UserID=$id&ChallengeID=$challenge\">diesen Link</a> klicken um Ihr vorheriges Passwort wiederherzustellen. Dieser Link ist 3 Tage lang gültig.<br>Sollten Sie Ihr Passwort selbst geändert haben, ignorieren Sie diese E-Mail einfach.<br><br>Mit freundlichen Grüßen<br><br>Ihr Home Control Panel Team";
				$header = "From: Home Control Panel <noreply@moeller.mx>\r\n";
				$header .= "MIME-Version: 1.0\r\n";
				$header .= "Content-Type: text/html; charset=utf-8\r\n";
				mail($email, $subject, $msg, $header);
				mysqli_query($link, "UPDATE user SET password = '$pw' WHERE userid LIKE $id LIMIT 1");
				if(isset($_COOKIE['remember_me_pw']) && isset($_COOKIE['remember_me_userid'])) {
					setcookie('remember_me_userid', $id, time() + 31536000, "/hcp/");
					setcookie('remember_me_pw', $pw, time() + 31536000, "/hcp/");
				}
				echo "1";
			}
		}
	}
?>
