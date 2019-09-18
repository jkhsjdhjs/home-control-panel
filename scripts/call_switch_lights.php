<?php
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['light']) && isset($_POST['on_off'])) {
		session_start();
		if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) {
			$light = intval($_POST['light']);
			$on_off = $_POST['on_off'];
			if($light >= 1 && $light <= 3) {
				if($on_off == "on" || $on_off == "off") {
					require_once("../mysql_functions.php");
					$link = db_connect();
					if($link) {
						$id = $_SESSION['userid'];
						$result = mysqli_query($link, "SELECT socket1, socket2, socket3 FROM permissions WHERE userid LIKE $id LIMIT 1");
						$row = mysqli_fetch_object($result);
						switch($light) {
							case 1:
								if($row->socket1) {
									$switch = true;
								}
								break;
							case 2:
								if($row->socket2) {
									$switch = true;
								}
								break;
							case 3:
								if($row->socket3) {
									$switch = true;
								}
								break;
						}
						if(isset($switch) && $switch) {
							$url = 'http://s2.moeller.mx/switch_lights.php';
							$data = array(
							   'key' => '7920c3b56ab60b39783d56aae0ffadf7cf09d47a625dfb905a9181d7fa9781ce4c661040',
							   'light' => strval($light),
							   'on_off' => $on_off
							);
							$options = array(
								'http' => array(
									'header' => "Content-type: application/x-www-form-urlencoded\r\n",
									'method' => 'POST',
									'content' => http_build_query($data)
								)
							);
							$context = stream_context_create($options);
							echo file_get_contents($url, false, $context);
						}
					}
				}
			}
		}
	}
?>
