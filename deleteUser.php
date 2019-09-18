<?php
	require_once("auth.php");
	require_once("mysql_functions.php");
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		//prüfen, ob der ausführende Nutzer auch wirklich admin ist
		if($_SESSION['admin']) {
			if(isset($_POST['id'])) {
					$id = $_POST['id'];
					$connection = db_connect();
					//Prüfen, ob der Nutzer existiert
					$result = mysqli_query($connection, "SELECT * FROM user WHERE userid LIKE $id LIMIT 1");
					if(mysqli_num_rows($result) != 0) {
						$row = mysqli_fetch_object($result);
						if($_SESSION['username'] != $row->username) {
							mysqli_query($connection, "DELETE FROM user WHERE userid = $id");
							mysqli_query($connection, "DELETE FROM permissions WHERE userid = $id");
							mysqli_query($connection, "DELETE FROM reset_pw WHERE userid = $id");
							$returnvalue = "success";
						}
						else {
							$returnvalue = "cannot_delete_yourself";
						}
					}
					else {
						$returnvalue = "no_user_w/_id";
					}
			}
			else {
				$returnvalue = "no_data_transmitted";
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
