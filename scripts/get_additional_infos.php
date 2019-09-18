<?php
	session_start();
	if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
		require_once("../mysql_functions.php");
		$link = db_connect();
		if($link) {
			$id = $_SESSION['userid'];
			$result = mysqli_query($link, "SELECT * FROM general LIMIT 1");
			$row = mysqli_fetch_object($result);
			$name_socket1 = $row->name_socket1;
			$name_socket2 = $row->name_socket2;
			$name_socket3 = $row->name_socket3;
			$result = mysqli_query($link, "SELECT * FROM user LEFT JOIN permissions ON user.userid = permissions.userid WHERE user.userid LIKE $id LIMIT 1");
			$row = mysqli_fetch_object($result);
			$stamp = timestamp_mysql2german($row->LastLogin);
			$stamp2 = timestamp_mysql2german($row->LastLogin2);
			$infos = array(
				'ID' => $id,
				'username' => $row->username,
				'email' => $row->email,
				'NumberOfLogins' => $row->NumberOfLogins,
				'LastLoginDate' => $stamp['date'],
				'LastLoginTime' => $stamp['time'],
				'LastLoginDate2' => $stamp2['date'],
				'LastLoginTime2' => $stamp2['time'],
				'admin' => $row->admin,
				'socket1' => $row->socket1,
				'socket2' => $row->socket2,
				'socket3' => $row->socket3,
				'name_socket1' => $name_socket1,
				'name_socket2' => $name_socket2,
				'name_socket3' => $name_socket3
			);
			echo json_encode($infos);
		}
	}
?>
