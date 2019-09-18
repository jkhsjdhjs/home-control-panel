<?php
	require_once("../mysql_functions.php");
	session_start();
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['charset']) && isset($_POST['title']) && isset($_POST['socket1']) && isset($_POST['socket2']) && isset($_POST['socket3'])) {
		if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
			$link = db_connect();
			if($link) {
				$id = $_SESSION['userid'];
				$result = mysqli_query($link, "SELECT admin FROM permissions WHERE userid LIKE $id LIMIT 1");
				$row = mysqli_fetch_object($result);
				if($row->admin) {
					$charset = $_POST['charset'];
					$title = $_POST['title'];
					$socket1 = $_POST['socket1'];
					$socket2 = $_POST['socket2'];
					$socket3 = $_POST['socket3'];
					mysqli_query($link, "UPDATE general SET website_charset = '$charset', website_title = '$title', name_socket1 = '$socket1', name_socket2 = '$socket2', name_socket3 = '$socket3' LIMIT 1");
					echo "1";
				}
			}
		}
	}
?>
