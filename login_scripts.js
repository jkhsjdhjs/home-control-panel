function logIn() {
	var username = $('#username').val();
	var password = $('#password').val();
	if($("#remember_me").is(":checked")) {
		var remember_me = 1;
	}
	else {
		var remember_me = 0;
	}
	if(username && password) {
		password = md5(password);
		$.ajax({
			type: "POST",
			url: "scripts/check_login.php",
			data: { 'user': username, 'pw': password, 'remember_me': remember_me },
			success: function callback(returnvalue){
				if(returnvalue) {
					window.location = "index.php";
				}
				else {
					alert("Login fehlgeschlagen!");
				}
			}
		})
	}
}

$(document).ready(function() {
	$('nav ul a').on('click', function(e) {
		e.preventDefault();
	});
	$('#button').on('click', function(e) {
		logIn();
	});
	$('input').keypress(function(e) {
		if(e.which == 13) {
			logIn();
		}
	});
});