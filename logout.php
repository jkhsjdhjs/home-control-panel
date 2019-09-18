<?php
	require_once("config.php");
    session_start();
    session_destroy();
	$cookies = false;
	if(isset($_COOKIE['remember_me_user'])) {
		$cookies = true;
		setcookie('remember_me_user', '', time() - 1);
	}
	if(isset($_COOKIE['remember_me_pw'])) {
		$cookies = true;
		setcookie('remember_me_pw', '', time() - 1);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=<?php echo "$website_charset"; ?>">
		<title>
			<?php
				echo $website_title;
			?>
			 - Logout
		</title>
		<meta http-equiv="refresh" content="3; URL=login.php"> 
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div id="header">
			<?php
				echo strtolower($website_title);
			?>
		</div>
		<div id="body_content">
			<div class="box box-success">
				Logout erfolgreich!
				<?php
					if($cookies == true) {
						echo " Alle Cookies wurden gelöscht.";
					}
				?>
				<br>
				Sie werden in 3 Sekunden weitergeleitet...
			</div>
		</div>
	</body>
</html>
