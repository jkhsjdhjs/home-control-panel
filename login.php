<?php
	require_once("login_system.php");
	if(is_logged_in()) {
		header('Location: index.php');
	}
	else {
		# Datei mit ein paar auf MySQL DBs bezogenen Funktionen
		require_once("mysql_functions.php");
		# Mit MySQL DB verbinden
		$link = db_connect();
		# Überprüfen, ob der Verbindungsaufbau erfolgreich war
		if($link) {
			# Allgemeine Einstellungen aus der MySQL DB lesen
			$result = mysqli_query($link, "SELECT website_charset, website_title FROM general");
			# Objekt in Array konvertieren
			$row = mysqli_fetch_object($result);
			# Array in kürzere Variablen schreiben
			$charset = $row->website_charset;
			$title = $row->website_title;
		}
		# Falls der Verbindungsaufbau nicht erfolgreich war...
		else {
			# ..."mysqli_connection_failed" in $error schreiben zur späteren Überprüfung auf Fehler
			$error = "mysqli_connection_failed";
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<!-- Charset aus MySQL DB anwenden -->
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>">
		<!-- Stylesheet "main_style.css" einbinden -->
		<link rel="stylesheet" type="text/css" href="main_style.css"></link>
		<!-- MD5 Hash Generator einbinden -->
		<script type="text/javascript" src="/js/md5.js"></script>
		<!-- jQuery einbinden -->
		<script type="text/javascript" src="/js/jquery.min.js"></script>
		<!-- Scripts für den Login Bereich einbinden -->
		<script type="text/javascript" src="login_scripts.js"></script>
		<!-- Webseiten Titel aus MySQL DB in den Seitentitel schreiben -->
		<title>
			<?php echo $title; ?> &mdash; Login
		</title>
	</head>
	<body>
		<!-- Header -->
		<header><!-- Spezialkonstruktion aus mehreren DIVs zum vertikalen zentrieren -->
			<img id="logo" height="100" title="&copy; Jan Jurdzinski" src="img/logo.png">
			<div id="outer_header_div">
				<div id="inner_header_div">
					<!-- Webseiten Titel aus MySQL DB in den Header Bereich schreiben -->
					<?php echo $title; ?>
				</div>
			</div>
		</header>
		<!-- Navigationsleiste/Navigationsbereich -->
		<nav>
			<!-- Dropdown Menü -->
			<ul id="menu">
				<li><a href="#" class="active">Login</a></li>
			</ul>
		</nav>
		<!-- Seitentitel -->
		<div id="content_title">
			Login
		</div>
		<!-- Seiteninhalt -->
		<div id="content">
			<div id="login">
				<div id="login_header">
					<span class="fontawesome-lock"></span>
					<span id="text">Login</span>
				</div>
				<div id="login_content">
					<input id="username" type="text" placeholder="Nutzername oder E-Mail"></input>
					<br>
					<input id="password" type="password" placeholder="Passwort"></input>
					<br>
					<input id="remember_me" type="checkbox" checked></input> Angemeldet bleiben?
					<br>
					<a href="#">Passwort vergessen?</a>
					<br>
					<button id="button">Login</button>
				</div>
			</div>
		</div>
	</body>
</html>
