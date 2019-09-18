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
				$result = mysqli_query($link, "SELECT * FROM general LIMIT 1");
				$row = mysqli_fetch_object($result);
				$general_settings = array(
					'charset' => $row->website_charset,
					'title' => $row->website_title,
					'socket1' => $row->name_socket1,
					'socket2' => $row->name_socket2,
					'socket3' => $row->name_socket3
				);
				echo json_encode($general_settings);
			}
		}
	}
?>
