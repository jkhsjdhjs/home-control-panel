<?php
	require_once("mysql_functions.php");
	session_start();

	if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
		if (!isset($_COOKIE['remember_me_user'])) {
			header('Location: login.php');
			exit;
		}
		else {
			if (isset($_COOKIE['remember_me_pw'])) {
				$username = $_COOKIE['remember_me_user'];
				$password = $_COOKIE['remember_me_pw'];
				$connection = db_connect();
				$result = mysqli_query($connection, "SELECT * FROM user LEFT JOIN permissions ON user.userid = permissions.userid WHERE username LIKE '$username' LIMIT 1");
				$numberofrows = mysqli_num_rows($result);
				if ($numberofrows == 1) {
					$row = mysqli_fetch_object($result);
					$userid = $row->userid;
					if($row->password == $password) {
						//Cookies erneuern
						setcookie('remember_me_user', $username, time() + 31536000);
						setcookie('remember_me_pw', $password, time() + 31536000);
						$stamp = timestamp_mysql2german($row->LastLogin);
						//SESSION Variablen füllen
						$_SESSION['userid'] = $userid;
						$_SESSION['loggedIn'] = true;
						$_SESSION['username'] = $username;
						$_SESSION['NumberOfLogins'] = $row->NumberOfLogins;
						$_SESSION['LastLoginDate'] = $stamp['date'];
						$_SESSION['LastLoginTime'] = $stamp['time'];
						$_SESSION['admin'] = $row->admin;
						$_SESSION['socket1'] = $row->socket1;
						$_SESSION['socket2'] = $row->socket2;
						$_SESSION['socket3'] = $row->socket3;
						//Weiterleitung zur index.php
						header('Location: index.php');
						exit;
					}
					else {
						header('Location: login.php');
					}
				}
				else {
					header('Location: login.php');
				}
			}
			else {
				header('Location: login.php');
			}
		}
	}
?>
