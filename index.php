<?php
	require_once("login_system.php");
	$loggedIn = is_logged_in();
	if(!$loggedIn) {
		header('Location: login.php');
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
<html ng-app="hcpApp">
	<head>
		<!-- Base URL festlegen (wird von Angular benötigt) -->
		<base href="/hcp/">
		<!-- Charset aus MySQL DB anwenden -->
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>">
		<!-- jQuery einbinden -->
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<!-- MD5-Hash Generator einbinden -->
		<script type="text/javascript" src="js/md5.js"></script>
		<!-- AngularJS - JavaScript Framework einbinden -->
		<script type="text/javascript" src="js/angular.min.js"></script>
		<!-- AngularJS Routing Script einbinden -->
		<script type="text/javascript" src="js/angular-route.min.js"></script>
		<!-- Javascript für Menü und Seitenwechsel einbinden -->
		<script type="text/javascript" src="main_scripts.js"></script>
		<!-- Stylesheet "main_style.css" einbinden -->
		<link rel="stylesheet" type="text/css" href="main_style.css"></link>
		<!-- Webseiten Titel aus MySQL DB in den Seitentitel schreiben -->
		<title ng-bind="'<?php echo $title; ?> &mdash; ' + title">
			Home Control Panel
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
				<li><a id="1" class="passive" href=".">Home</a></li>
				<li><a id="2" class="passive" href="#">Benutzerkonto verwalten</a></li>
				<?php
					session_start();
					$link = db_connect();
					if($link) {
						$id = $_SESSION['userid'];
						$result = mysqli_query($link, "SELECT admin FROM permissions WHERE userid LIKE $id LIMIT 1");
						$row = mysqli_fetch_object($result);
						$admin = $row->admin;
					}
					if(isset($admin) && $admin) {
						echo "<li><a id=\"3\" class=\"passive\" href=\"#\">Administration</a></li>";
					}
				?>
				<li><a id="4" class="passive" href="#" onClick="logout();">Logout</a></li>
			</ul>
			<ul id="sub2">
				<li><a id="2-1" class="sub-passive" href="change_username">Benutzername ändern</a></li>
				<li><a id="2-2" class="sub-passive" href="change_email">E-Mail Adresse ändern</a></li>
				<li><a id="2-3" class="sub-passive" href="change_pw">Passwort ändern</a></li>
				<li><a id="2-4" class="sub-passive" href="additional_infos">Sonstige Informationen</a></li>
			</ul>
			<?php
				if(isset($admin) && $admin) {
					echo "<ul id=\"sub3\">";
					echo "<li><a id=\"3-1\" class=\"sub-passive\" href=\"admin_cp/settings\">Allgemeine Einstellungen</a></li>";
					echo "<li><a id=\"3-2\" class=\"sub-passive\" href=\"admin_cp/usermanagement\">Benutzerverwaltung</a></li>";
					echo "</ul>";
				}
			?>
		</nav>
		<!-- Seitentitel -->
		<div id="content_title" ng-bind="title"></div>
		<!-- Seiteninhalt -->
		<div id="content" ng-view></div>
	</body>
</html>
