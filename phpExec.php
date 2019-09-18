<?php
	require_once("auth.php");
	if(isset($_GET['command']) AND !empty($_GET['command'])) {
		$command = $_GET['command'];
		if ($command == "1on") {
			exec("sudo /var/sudowebscript.sh 1on",$output,$error);
		}
		if ($command == "1off") {
			exec("sudo /var/sudowebscript.sh 1off",$output,$error);
		}
		if ($command == "2on") {
			exec("sudo /var/sudowebscript.sh 2on",$output,$error);
		}
		if ($command == "2off") {
			exec("sudo /var/sudowebscript.sh 2off",$output,$error);
		}
		if ($command == "3on") {
			exec("sudo /var/sudowebscript.sh 3on",$output,$error);
		}
		if ($command == "3off") {
			exec("sudo /var/sudowebscript.sh abc",$output,$error);
		}
	}
?>