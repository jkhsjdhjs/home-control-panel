<?php
	require_once('auth.php');
	require_once("config.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=<?php echo "$website_charset"; ?>">
		<title>
			<?php
				echo $website_title;
			?>
		</title>
		<link rel="stylesheet" href="style.css">
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/md5.js"></script>
		<script type="text/javascript">
			var hidden = true;
			(function($) {
				$.fn.showHtml = function(file, speed, callback) {
					return this.each(function() {
						$.get(file, function(returnvalue) {
							var content = returnvalue;
						});
						console.log(content);
						// The element to be modified
						var el = $(this);

						// Preserve the original values of width and height - they'll need 
						// to be modified during the animation, but can be restored once
						// the animation has completed.
						var finish = {width: this.style.width, height: this.style.height};

						// The original width and height represented as pixel values.
						// These will only be the same as `finish` if this element had its
						// dimensions specified explicitly and in pixels. Of course, if that 
						// was done then this entire routine is pointless, as the dimensions 
						// won't change when the content is changed.
						var cur = {width: el.width()+'px', height: el.height()+'px'};

						// Modify the element's contents. Element will resize.
						el.html(content);

						// Capture the final dimensions of the element 
						// (with initial style settings still in effect)
						var next = {width: el.width()+'px', height: el.height()+'px'};

						el .css(cur) // restore initial dimensions
							.animate(next, speed, function()  // animate to final dimensions
							{
							   el.css(finish); // restore initial style settings
							   if ( $.isFunction(callback) ) callback();
							});
						}
					);
				};
			})(jQuery);
			function executePHP(command) {
				$.get("phpExec.php", { command: command }, function(command){}, "json");
			}
			function logoutQuestion() {
				if (confirm('Wollen Sie sich wirklich abmelden?')) {
					window.location = "logout.php";
				}
			}
			function loadPWChange() {
				if (hidden == true) {
					$('#pwchange').load('html/changePW.html');
					hidden = false;
				}
			}
			function hideFields() {
				if (hidden == false) {
					$('#info').text("");
					$('#pwchange').text("");
					hidden = true;
				}
			}
			function changePW() {
				var oldpw = md5(document.getElementById("oldpw").value);
				var newpw = md5(document.getElementById("newpw").value);
				var newpwcheck = md5(document.getElementById("newpwcheck").value);
				if (newpw != newpwcheck) {
					$('#info').load('html/changePW_notmatch.html');
				}
				else {
					jQuery.ajax({
						type: "POST",
						url: "passwordchange.php",
						data: "oldpw=" + oldpw + "&newpw=" + newpw,
						success: function callback(returnvalue){
							if(returnvalue == true) {
								$('#pwchange').text("");
								$('#info').load('html/changePW_success');
								window.setTimeout(hideFields, 3000);
							}
							else {
								$('#info').load('html/changePW_oldincorrect.html');
							}
						}
					})
				}
			}
			document.onkeydown = function(event) {
				if(event.keyCode == 13 && hidden == false) {
					changePW();
				}
			}
		</script>
	</head>
	<body>
		<div id="header">
			<?php
				echo strtolower($website_title);
			?>
		</div>
		<div id="body_content">
			<div id="welcome">
				<?php
					echo "Hallo <span class=\"welcome-highlighted\">" . $_SESSION['username'] . "</span>! Sie haben sich bisher <span class=\"welcome-highlighted\">" . $_SESSION['NumberOfLogins'] . " Mal</span> eingeloggt.";
					if($_SESSION['LastLoginDate'] != "00.00.0000" && $_SESSION['LastLoginTime'] != "00:00:00") {
						echo "<br>Ihr letzter Login war am <span class=\"welcome-highlighted\">" . $_SESSION['LastLoginDate'] . "</span> um <span class=\"welcome-highlighted\">" . $_SESSION['LastLoginTime'] . " Uhr</span>.";
					}
				?>
			</div>
			<?php
				if($_SESSION['admin'] == true) {
					echo "<div id=\"admin_cp_link\">";
					echo "<a href=\"admin_cp.php\">Administration</a>";
					echo "</div>";
				}
			?>
			<span style="clear: both;"></span>
			<br>
			<br>
			<br>
			<br>
			<?php
				if($_SESSION['socket1'] == true) {
					echo "<div id=\"title\">";
					echo "Nachtlicht";
					echo "</div>";
					echo "<br>";
					echo "<a href=\"#\" class=\"action-button animate green\" onClick=\"executePHP('1on');\"><div id=\"button_text\">AN</div></a>";
					echo "<a href=\"#\" class=\"action-button animate red\" onClick=\"executePHP('1off');\"><div id=\"button_text\">AUS</div></a>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
				}
				if($_SESSION['socket2'] == true) {
					echo "<div id=\"title\">";
					echo "Schreibtischlampe";
					echo "</div>";
					echo "<br>";
					echo "<a href=\"#\" class=\"action-button shadow animate green\" onClick=\"executePHP('2on');\"><div id=\"button_text\">AN</div></a>";
					echo "<a href=\"#\" class=\"action-button shadow animate red\" onClick=\"executePHP('2off');\"><div id=\"button_text\">AUS</div></a>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
				}
				if($_SESSION['socket3'] == true) {
					echo "<div id=\"title\">";
					echo "- zurzeit unbenutzt -";
					echo "</div>";
					echo "<br>";
					echo "<a href=\"#\" class=\"action-button shadow animate green\" onClick=\"executePHP('3on');\"><div id=\"button_text\">AN</div></a>";
					echo "<a href=\"#\" class=\"action-button shadow animate red\" onClick=\"executePHP('3off');\"><div id=\"button_text\">AUS</div></a>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
					echo "<br>";
				}
			?>
			<button onClick="loadPWChange();">Passwort ändern</button>
			<br>
			<div id="info">
			</div>
			<div id="pwchange">
			</div>
			<br>
			<button onClick="logoutQuestion();">Abmelden</button>
		</div>
	</body>
</html>
