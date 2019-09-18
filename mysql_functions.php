<?php
	//Funktion, mit der die Verbindung zur MySQL Datenbank aufgebaut wird
	function db_connect() {
		return mysqli_connect("localhost", "hcp", "", "hcp");
	}
	//Funktion zum konvertieren des MySQL Timestamp in deutsches Zeitformat
	function timestamp_mysql2german($date) {
		$stamp['date'] = sprintf("%02d.%02d.%04d", substr($date, 8, 2), substr($date, 5, 2), substr($date, 0, 4));
		$stamp['time'] = sprintf("%02d:%02d:%02d", substr($date, 11, 2), substr($date, 14, 2), substr($date, 17, 2));
		return $stamp;
	}
?>
