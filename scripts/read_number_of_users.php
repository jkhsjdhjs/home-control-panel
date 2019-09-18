<?php
	require_once("../mysql_functions.php");
	session_start();
	if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
		$link = db_connect();
		if($link) {
			$id = $_SESSION['userid'];
			$result = mysqli_query($link, "SELECT admin FROM permissions WHERE userid LIKE $id LIMIT 1");
			$row = mysqli_fetch_object($result);
			if($row->admin) {
				$result = mysqli_query($link, "SELECT userid FROM user");
				echo mysqli_num_rows($result);
			}
		}
	}
?>
