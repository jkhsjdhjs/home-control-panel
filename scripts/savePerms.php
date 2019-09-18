<?php
	require_once("../mysql_functions.php");
	session_start();
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['id']) && isset($_POST['admin']) && isset($_POST['socket1']) && isset($_POST['socket2']) && isset($_POST['socket3'])) {
		$link = db_connect();
		if($link) {
			$id = $_SESSION['userid'];
			$result = mysqli_query($link, "SELECT admin FROM permissions WHERE userid LIKE $id LIMIT 1");
			$row = mysqli_fetch_object($result);
			if($row->admin) {
				$id = intval($_POST['id']);
				$admin = intval($_POST['admin']);
				$socket1 = intval($_POST['socket1']);
				$socket2 = intval($_POST['socket2']);
				$socket3 = intval($_POST['socket3']);
				mysqli_query($link, "UPDATE permissions SET admin = $admin, socket1 = $socket1, socket2 = $socket2, socket3 = $socket3 WHERE userid LIKE $id LIMIT 1");
				echo "1";
			}
		}
	}
?>
