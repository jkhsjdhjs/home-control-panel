<?php
	//auth.php einbinden, damit man das Passwort nicht ohne eingeloggt zu sein ändern kann
	require_once("auth.php");
	require_once("mysql_functions.php");
	if(isset($_POST['oldpw']) && isset($_POST['newpw'])) {
		$userid = $_SESSION['userid'];
		$oldpw = $_POST['oldpw'];
		$newpw = $_POST['newpw'];
		//Verbinden...
		$connection = db_connect();
		//password auslesen, wo der Nutzername gleich dem Nutzername der DB übereinstimmt
		$result = mysqli_query($connection, "SELECT password FROM user WHERE userid LIKE $userid");
		$row = mysqli_fetch_object($result);
		//Falls das alte Passwort mit dem der DB übereinstimmt...
		if($row->password == $oldpw) {
			//...altes Passwort zu neuem ändern
			mysqli_query($connection, "UPDATE user SET password = '$newpw' WHERE userid LIKE $userid");
			$success = true;
		}
		else {
			$success = false;
		}
		//zurückgeben, ob es erfolgreich war oder nicht
		echo $success;
	}
?>
