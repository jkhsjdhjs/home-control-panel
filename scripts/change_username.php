<?php
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['user'])) {
		require_once("../mysql_functions.php");
		$link = db_connect();
		if($link) {
			session_start();
			$id = $_SESSION['userid'];
			$user = $_POST['user'];
			$result = mysqli_query($link, "SELECT * FROM user WHERE username LIKE '$user' LIMIT 1");
			if(mysqli_num_rows($result) == 0) {
				mysqli_query($link, "UPDATE user SET username = '$user' WHERE userid LIKE $id LIMIT 1");
				echo "1";
			}
			else {
				echo "username_taken";
			}
		}
	}
?>
