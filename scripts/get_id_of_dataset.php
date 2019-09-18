<?php
	require_once("../mysql_functions.php");
	session_start();
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['dataset'])) {
		if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
			$link = db_connect();
			if($link) {
				$id = $_SESSION['userid'];
				$result = mysqli_query($link, "SELECT admin FROM permissions WHERE userid LIKE $id LIMIT 1");
				$row = mysqli_fetch_object($result);
				if($row->admin) {
					$dataset = intval($_POST['dataset']) - 1;
					$result = mysqli_query($link, "SELECT userid FROM user LIMIT $dataset, 1");
					$row = mysqli_fetch_object($result);
					echo $row->userid;
				}
			}
		}
	}
?>
