<?php
	require_once 'config.php';
	require_once("mysql_functions.php");
	if(!empty($_GET['UserID']) && !empty($_GET['ChallengeID']) && isset($_GET['UserID']) && isset($_GET['ChallengeID'])) {
		$challenge = $_GET['ChallengeID'];
		$userid = $_GET['UserID'];
		$connection = db_connect();
		$result = mysqli_query($connection, "SELECT * FROM reset_pw WHERE userid LIKE $userid");
		$numberofrows = mysqli_num_rows($result);
		if ($numberofrows == 1) {
			$row = mysqli_fetch_object($result);
			$timestamp = $row->timestamp;
			$valid = $row->valid;
			$challengeDB = $row->challenge_id;
			if ($valid == true) {
				if ($challengeDB == $challenge) {
					if (time() - strtotime($timestamp) <= 86400) {
						if (isset($_POST['pw']) && !empty($_POST['pw']) && isset($_POST['repeat_pw']) && !empty($_POST['repeat_pw'])) {
							$pw = md5($_POST['pw']);
							$repeat_pw = md5($_POST['repeat_pw']);
							if ($pw == $repeat_pw) {
								mysqli_query($connection, "UPDATE user SET password = '$pw' WHERE userid LIKE $userid");
								mysqli_query($connection, "UPDATE reset_pw SET valid = 0 WHERE userid LIKE $userid");
								$error = false;
							}
							else {
								$error = "pw_no_match";
							}
						}
					}
					else {
						$error = "time_ran_out";
					}
				}
				else {
					$error = "invalid_link";
				}
			}
			else {
				$error = "invalid_link";
			}
		}
		else {
			$error = "unknown_exception";
		}
	}
	else {
		$error = "invalid_link";
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=<?php echo "$website_charset"; ?>">
		<?php
			if($error === false) {
				echo "<meta http-equiv=\"refresh\" content=\"3; url=http://moeller.mx/hcp/\">";
			}
		?>
		<link rel="stylesheet" href="style.css">
		<title>
			<?php
				echo $website_title;
			?>
			 - Passwort zurücksetzen
		</title>
	</head>
	<body>
		<div id="header">
			<?php
				echo strtolower($website_title);
			?>
		</div>
		<div id="body_content">
			<form action="newpw.php?UserID=<?php echo $userid; ?>&ChallengeID=<?php echo $challenge; ?>" method="post" class="login-form">
				<table class="login-table">
					<tr>
						<td>
							<span class="fontawesome-key" style="color: green; font-size: 18px"></span><span class="login">Passwort zurücksetzen</span>
						</td>
					</tr>
					<?php
						if ($error == "time_ran_out") {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-error small-box\">";
							echo "Aktivierungslink ist bereits abgelaufen!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
						else if ($error == "invalid_link") {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-error small-box\">";
							echo "Ungültiger Link!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
						else if ($error == "pw_no_match") {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-error small-box\">";
							echo "Passwörter stimmen nicht überein!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
						if ((!isset($error) || $error !== false) && $error != "invalid_link" && $error != "time_ran_out" && $error != "no_pw_entered" && $error != "no_pw_repeated") {
							echo "<tr>";
							echo "<td>";
							echo "<a id=\"forgot_pw_link\">Hier können Sie ein neues Passwort<br>festlegen:</a>";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"password\" placeholder=\"Passwort\" name=\"pw\" class=\"login-input animate\">";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"password\" placeholder=\"Passwort wiederholen\" name=\"repeat_pw\" class=\"login-input animate\">";
							echo "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "<td>";
							echo "<input type=\"submit\" value=\"Passwort speichern\" class=\"login-button animate pw-reset-button\">";
							echo "</td>";
							echo "</tr>";
						}
						else if ($error == false) {
							echo "<tr>";
							echo "<td>";
							echo "<div class=\"box box-success small-box\">";
							echo "Passwort wurde erfolgreich geändert!<br>Sie werden in 3 Sekunden zum Login bereich weitergeleitet!";
							echo "</div>";
							echo "</td>";
							echo "</tr>";
						}
					?>
				</table>
			</form>
		</div>
	</body>
</html>
