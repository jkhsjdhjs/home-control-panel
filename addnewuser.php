<?php
	function sendMAIL($validationID, $email) {
		$subject = "Home Control Panel - Anmeldung";
		$sender = "moeller.mx <noreply@moeller.mx>";
		$message = "Hallo!<br><br>Sie sind soeben zum Home Control Panel eingeladen worden.<br>Klicken Sie einfach auf nachfolgenden Link um Ihren Nutzernamen und Ihr Passwort festzulegen.<br>Dieser Link ist 24h lang gültig:<br><a href=http://moeller.mx/hcp/register.php?email=".$email."&validationID=".$validationID.">http://moeller.mx/hcp/register.php?email=".$email."&validationID=".$validationID."</a><br><br>Mit freundlichen Grüßen<br><br>Ihr moeller.mx Team";
						
		$header  = "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html; charset=UTF-8\r\n";
						 
		$header .= "From: $sender\r\n";
		// $header .= "Reply-To: $sender\r\n"; //für anwort an andere adresse
		// $header .= "Cc: $cc\r\n";  // falls an CC gesendet werden soll
		$header .= "X-Mailer: PHP " . phpversion();
		mail($email, $subject, $message, $header);
	}
	
	function validate_email($e_mail) {
		//E-Mail Adresse auf richtiges Format überprüfen
		if (!preg_match("^([a-zA-Z0-9\\-\\.\\_]+)(\\@)([a-zA-Z0-9\\-\\.]+)(\\.)([a-zA-Z]{2,4})$^", $e_mail)) {
			return false;
		}
		else {
			return true;
		}
	}

	//auth.php einbinden, damit kein nicht-eingeloggter user neue user hinzufügen kann
	require_once("auth.php");
	require_once("mysql_functions.php");
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		//prüfen, ob der ausführende Nutzer auch wirklich admin ist
		if($_SESSION['admin']) {
			//Zufällige validationID generieren
			$validationID = md5(md5((string)mt_rand() . $_SERVER['REMOTE_ADDR'])) . md5(md5((string)mt_rand() . $_SERVER['REMOTE_ADDR']));
			//Informationen aus den POST Argumenten lesen
			$email = $_POST['email'];
			$admin = $_POST['admin'];
			$socket1 = $_POST['socket1'];
			$socket2 = $_POST['socket2'];
			$socket3 = $_POST['socket3'];
			if(validate_email($email)) {
				//Verbindung zur Datenbank aufbauen
				$connection = db_connect();
				//Abfragen, ob es bereits Einträge in der Tabelle "user" mit der übergebenen E-Mail Adresse gibt
				$result = mysqli_query($connection, "SELECT * FROM user WHERE email LIKE '$email' LIMIT 1");
				$numberofrows = mysqli_num_rows($result);
				//Falls nicht, dann...
				if ($numberofrows == 0) {
					//Abfragen, ob es in der Tabelle "pending_users" bereits Einträge mit der übergebenen E-Mail Adresse gibt
					$result = mysqli_query($connection, "SELECT * FROM pending_users WHERE email LIKE '$email' LIMIT 1");
					$numberofrows = mysqli_num_rows($result);
					//Falls nicht, dann...
					if ($numberofrows == 0) {
						//Funktion zum Eintragen des neuen Nutzers in dei Tabelle "pending_users" ausrufen
						mysqli_query($connection, "INSERT INTO pending_users (validationID, email, admin, socket1, socket2, socket3) VALUES ('$validationID', '$email', $admin, $socket1, $socket2, $socket3)");
						sendMAIL($validationID, $email);
						$returnvalue = "success";
					}
					//Falls doch, dann...
					else if ($numberofrows == 1) {
						//Überprüfen, ob der gefundene Eintrag schon abgelaufen ist
						$row = mysqli_fetch_object($result);
						//Falls ja, dann...
						if (time() - strtotime($row->timestamp) > 86400) {
							//alten Eintrag löschen
							mysqli_query($connection, "DELETE FROM pending_users WHERE email LIKE '$email'");
							//Funktion zum Eintragen des neuen Nutzers in dei Tabelle "pending_users" ausrufen
							mysqli_query($connection, "INSERT INTO pending_users (validationID, email, admin, socket1, socket2, socket3) VALUES ('$validationID', '$email', $admin, $socket1, $socket2, $socket3)");
							sendMAIL($validationID, $email);
							$returnvalue = "success";
						}
						else {
							$returnvalue = "email_already_got_valid_invite";
						}
					}
					else {
						$returnvalue = "other_error";
					}
				}
				else if ($numberofrows == 1) {
					$returnvalue = "email_already_assigned_to_acc";
				}
				else {
					$returnvalue = "other_error";
				}
			}
			else {
				$returnvalue = "email_not_valid";
			}
		}
		else {
			$returnvalue = "not_an_admin";
		}
	}
	else {
		$returnvalue = "post_not_used";
	}
	echo $returnvalue;
?>
