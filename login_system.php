<?php
	function is_logged_in() {
		require_once("mysql_functions.php");
		session_start();
		if(!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
			if(!isset($_COOKIE['remember_me_userid'])) {
				return false;
			}
			else {
				if(isset($_COOKIE['remember_me_pw'])) {
					$userid = $_COOKIE['remember_me_userid'];
					$password = $_COOKIE['remember_me_pw'];
					$link = db_connect();
					if($link) {
						$result = mysqli_query($link, "SELECT password FROM user WHERE userid LIKE $userid LIMIT 1");
						if(mysqli_num_rows($result) == 1) {
							$row = mysqli_fetch_object($result);
							if($row->password == $password) {
								//Cookies erneuern
								setcookie('remember_me_userid', $userid, time() + 31536000);
								setcookie('remember_me_pw', $password, time() + 31536000);
								//SESSION Variablen füllen
								$_SESSION['userid'] = $userid;
								$_SESSION['loggedIn'] = true;
								return true;
							}
							else {
								return false;
							}
						}
						else {
							return false;
						}
					}
					else {
						return "no_connection";
					}
				}
				else {
					return false;
				}
			}
		}
		else {
			return true;
		}
	}
?>
