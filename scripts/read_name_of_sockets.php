<?php
	require_once("../mysql_functions.php");
	session_start();
	if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
		$link = db_connect();
		if($link) {
			$result = mysqli_query($link, "SELECT name_socket1, name_socket2, name_socket3 FROM general LIMIT 1");
			$row = mysqli_fetch_object($result);
			$names = array(
				'socket1' => $row->name_socket1,
				'socket2' => $row->name_socket2,
				'socket3' => $row->name_socket3
			);
			echo json_encode($names);
		}
	}
?>
