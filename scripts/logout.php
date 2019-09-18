<?php
	$cookies = false;
	session_start();
	session_destroy();
	if(isset($_COOKIE['remember_me_userid'])) {
		setcookie('remember_me_userid', '', time() - 1, "/hcp/");
		$cookies = true;
	}
	if(isset($_COOKIE['remember_me_pw'])) {
		setcookie('remember_me_pw', '', time() - 1, "/hcp/");
		$cookies = true;
	}
	echo $cookies;
?>