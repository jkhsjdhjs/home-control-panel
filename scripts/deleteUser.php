<?php
	require_once("../mysql_functions.php");
	session_start();
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['id'])) {
		$link = db_connect();
		if($link) {
			$id = $_SESSION['userid'];
			$result = mysqli_query($link, "SELECT admin FROM permissions WHERE userid LIKE $id LIMIT 1");
			$row = mysqli_fetch_object($result);
			if($row->admin) {
				$id = intval($_POST['id']);
				mysqli_multi_query($link, "DELETE FROM user WHERE userid LIKE $id LIMIT 1; DELETE FROM permissions WHERE userid LIKE $id LIMIT 1");
				echo "1";
			}
		}
	}
?>
