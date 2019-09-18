<?php
	$msg = "Es sind Fehler aufgetreten. Kontaktieren Sie den Seiten-Administrator, falls Sie sicher sind, keinen Fehler gemacht zu haben!";
	require_once("mysql_functions.php");
	if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['ChallengeID']) && isset($_GET['UserID'])) {
		$challenge = $_GET['ChallengeID'];
		$id = intval($_GET['UserID']);
		$link = db_connect();
		$result = mysqli_query($link, "SELECT old_pw, timestamp FROM changed_pw WHERE challenge_id LIKE '$challenge' AND userid LIKE $id LIMIT 1");
		if(mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
			if(time() - strtotime($row->timestamp) <= 259200) {
				$old_pw = $row->old_pw;
				mysqli_query($link, "UPDATE user SET password = '$old_pw' WHERE userid LIKE $id LIMIT 1");
				mysqli_query($link, "UPDATE changed_pw SET challenge_id = '' WHERE userid LIKE $id LIMIT 1");
				if(isset($_COOKIE['remember_me_userid']) && isset($_COOKIE['remember_me_pw'])) {
					setcookie('remember_me_pw', $old_pw, time() + 31536000, "/hcp/");
				}
				echo "Ihr Passwort wurde erfolgreich zurueckgesetzt und Sie koennen sich nun wieder mit ihm anmelden.<br>Sie werden in 5 Sekunden weitergeleitet!";
				$success = true;
			}
		}
	}
	if(!isset($success) && !$success) {
		echo $msg;
	}
	else {
		echo "<meta http-equiv=\"refresh\" content=\"5; URL=index.php\">"; 
	}
?>
