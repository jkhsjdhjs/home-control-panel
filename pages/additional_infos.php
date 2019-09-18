<?php
	session_start();
	if(!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
		echo "Sie sind nicht (mehr) eingeloggt!";
	}
	else {
		require_once("additional_infos.html");
	}
?>