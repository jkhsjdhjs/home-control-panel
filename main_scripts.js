var hcpApp = angular.module('hcpApp', ['ngRoute']);
var hardReload = true, back_or_forward = false;

hcpApp.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
	$routeProvider
		.when('/', {
			title: 'Startseite',
			templateUrl: 'pages/home.php',
			id: 1,
			sub_id: 0,
		})
		.when('/change_username', {
			title: 'Benutzername ändern',
			templateUrl: 'pages/change_username.php',
			id: 2,
			sub_id: 1
		})
		.when('/change_email', {
			title: 'E-Mail Adresse ändern',
			templateUrl: 'pages/change_email.php',
			id: 2,
			sub_id: 2
		})
		.when('/change_pw', {
			title: 'Passwort ändern',
			templateUrl: 'pages/change_pw.php',
			id: 2,
			sub_id: 3
		})
		.when('/additional_infos', {
			title: 'Sonstige Informationen',
			templateUrl: 'pages/additional_infos.php',
			id: 2,
			sub_id: 4
		})
		.when('/admin_cp/settings', {
			title: 'Allgemeine Einstellungen',
			templateUrl: 'pages/general_settings.php',
			id: 3,
			sub_id: 1
		})
		.when('/admin_cp/usermanagement', {
			title: 'Benutzerverwaltung',
			templateUrl: 'pages/usermanagement.php',
			id: 3,
			sub_id: 2
		})
		.otherwise({
			redirectTo: '/'
		});
	if(window.history && window.history.pushState){
		$locationProvider.html5Mode(true);
	}
}]);

hcpApp.run(['$location', '$rootScope', '$templateCache', function($location, $rootScope, $templateCache) {
	$rootScope.$on('$viewContentLoaded', function() {
		$templateCache.removeAll();
	});
	$rootScope.$on('$locationChangeSuccess', function() {
		$rootScope.actualLocation = $location.path();
	});
	$rootScope.$watch(function () {return $location.path()}, function (newLocation, oldLocation) {
		if($rootScope.actualLocation === newLocation) {
			back_or_forward = true;
		}
	});
    $rootScope.$on('$routeChangeSuccess', function(event, current, previous) {
        $rootScope.title = current.$$route.title;
		if(hardReload) {
			hardReload = false;
			init_menu(current.$$route.id, current.$$route.sub_id);
		}
		if(back_or_forward) {
			back_or_forward = false;
			change_menu(current.$$route.id, current.$$route.sub_id);
		}
    });
}]);

function init_menu(id, sub_id) {
    $('nav ul#menu #' + id.toString()).toggleClass('passive active');
    if(id != 1) {
        $('nav ul#sub' + id.toString()).css('visibility', 'visible');
        $('nav ul#sub' + id.toString() + ' #' + id.toString() + '-' + sub_id.toString()).toggleClass('sub-passive sub-active');
    }
}

function change_menu(id, sub_id) {
	if($('nav ul#menu #' + id.toString()).hasClass('passive')) {
		$('nav ul#menu #' + id.toString()).toggleClass('passive active');
	}
	$('nav ul#menu a.active').not('nav ul#menu #' + id.toString()).toggleClass('active passive');
	if(id != 1) {
		$('nav ul#sub2 a.sub-active, nav ul#sub3 a.sub-active').not('nav ul#sub' + id.toString() + ' a#' + id.toString() + '-' + sub_id.toString()).toggleClass('sub-active sub-passive');
		$('nav ul#sub' + id.toString() + ' a#' + id.toString() + '-' + sub_id.toString()).toggleClass('sub-passive sub-active');
		if($('nav ul#sub' + id.toString()).css('visibility') != 'visible') {
			$('nav ul#sub' + id.toString()).css('visibility', 'visible');
		}
		if(id == 2) {
			id = 3;
		}
		else {
			id = 2;
		}
		if($('nav ul#sub' + id.toString()).css('visibility') == 'visible') {
			$('nav ul#sub' + id.toString()).css('visibility', 'hidden');
		}
	}
	else {
		$('nav ul#sub2, nav ul#sub3').css('visibility', 'hidden');
		$('nav ul#sub2 a.sub-active, nav ul#sub3 a.sub-active').toggleClass('sub-active sub-passive');
	}
}

$(document).ready(function() {
	// Bedienung des Hauptmenüs und Öffnen der Untermenüs
	$('nav ul#menu a').on('click', function(e) {
		var id = $(this).attr('id');
		if(id != '4') {
			$('nav ul#menu a.active').toggleClass('active passive');
			$(this).toggleClass('passive active');
			if(id != '1') {
				if($('nav ul#sub' + id).css('visibility') != 'visible') {
					$('nav ul#sub' + id).css('visibility', 'visible');
				}
				if(id == '2') {
					id = '3';
				}
				else {
					id = '2';
				}
				$('nav ul#sub' + id).css('visibility', 'hidden');
			}
			else {
				$('nav ul#sub2, nav ul#sub3').css('visibility', 'hidden');
				$('nav ul#sub2 a.sub-active, nav ul#sub3 a.sub-active').toggleClass('sub-active sub-passive');
			}
		}
	});
	// Bedienung der Untermenüs
	$('nav ul#sub2 a, nav ul#sub3 a').on('click', function(e) {
		$('nav ul#sub2 a.sub-active, nav ul#sub3 a.sub-active').toggleClass('sub-active sub-passive');
		$(this).toggleClass('sub-passive sub-active');
	});
});

function logout() {
	if(confirm('Möchten Sie sich wirklich abmelden?')) {
		$.ajax({
			url: "scripts/logout.php",
			success: function callback(returnvalue){
				if(returnvalue) {
					alert('Logout erfolgreich! Alle Cookies wurden gelöscht! Sie werden zum Login weitergeleitet!');
				}
				else {
					alert('Logout erfolgreich! Sie werden zum Login weitergeleitet!');
				}
				window.location = "login.php";
			}
		})
	}
}