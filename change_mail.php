<?php
	$msg = "Es sind Fehler aufgetreten. Kontaktieren Sie den Seiten-Administrator, falls Sie sicher sind, keinen Fehler gemacht zu haben!";
	require_once("mysql_functions.php");
	if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['ChallengeID']) && isset($_GET['UserID'])) {
		$challenge = $_GET['ChallengeID'];
		$id = intval($_GET['UserID']);
		$link = db_connect();
		if($link) {
			$result = mysqli_query($link, "SELECT valid, timestamp, email FROM change_email WHERE userid LIKE $id AND challenge_id LIKE '$challenge' LIMIT 1");
			if(mysqli_num_rows($result) == 1) {
				$row = mysqli_fetch_object($result);
				if($row->valid) {
					if(time() - strtotime($row->timestamp) <= 86400) {
						$email = $row->email;
						mysqli_query($link, "UPDATE user SET email = '$email' WHERE userid LIKE $id LIMIT 1");
						mysqli_query($link, "UPDATE change_email SET valid = 0 WHERE userid LIKE $id AND email LIKE '$email' LIMIT 1");
						echo "Ihre E-Mail Adresse wurde erfolgreich geaendert!";
						echo "<br>Sie werden in 5 Sekunden weitergeleitet!";
						$success = true;
					}
				}
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
