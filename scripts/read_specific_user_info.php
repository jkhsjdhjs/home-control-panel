<?php
	require_once("../mysql_functions.php");
	session_start();
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['ID'])) {
		if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
			$link = db_connect();
			if($link) {
				$id = $_SESSION['userid'];
				$result = mysqli_query($link, "SELECT admin FROM permissions WHERE userid LIKE $id LIMIT 1");
				$row = mysqli_fetch_object($result);
				if($row->admin) {
					$ID = intval($_POST['ID']);
					$result = mysqli_query($link, "SELECT * FROM user LEFT JOIN permissions ON user.userid = permissions.userid WHERE user.userid LIKE $ID LIMIT 1");
					if(mysqli_num_rows($result) == 1) {
						$row = mysqli_fetch_object($result);
						$stamp = timestamp_mysql2german($row->LastLogin);
						$stamp2 = timestamp_mysql2german($row->LastLogin2);
						$stamp3 = timestamp_mysql2german($row->AccountCreation);
						$infos = array(
							'ID' => $ID,
							'username' => $row->username,
							'email' => $row->email,
							'NumberOfLogins' => $row->NumberOfLogins,
							'LastLoginDate' => $stamp['date'],
							'LastLoginTime' => $stamp['time'],
							'LastLoginDate2' => $stamp2['date'],
							'LastLoginTime2' => $stamp2['time'],
							'AccountCreationDate' => $stamp3['date'],
							'AccountCreationTime' => $stamp3['time'],
							'admin' => $row->admin,
							'socket1' => $row->socket1,
							'socket2' => $row->socket2,
							'socket3' => $row->socket3
						);
						echo json_encode($infos);
					}
					else {
						echo "not_existant";
					}
				}
			}
		}
	}
?>
