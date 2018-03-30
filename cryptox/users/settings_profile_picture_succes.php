<?php

	session_start();
	error_reporting(-1);
	ini_set("display_errors", 1);

	require_once('../config.inc.php');
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);
	$quser_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');

	#if someone is logged in, retrieve his/her user-information
	if (isset($_SESSION['user_id'])){
		$quser_info->execute(array($_SESSION['user_id']));
		$user_info = $quser_info->fetch();
	}

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width"/>
		<link rel="stylesheet" href="../css/reset.css" />
		<link rel="stylesheet" href="../css/main.css">
		<script type='text/javascript' src='../js/jquery.particleground.js'></script>
		<script type='text/javascript' src='../js/background.js'></script>
		<title>Cryptus</title>
	</head>
	<body id="body">
		<?php
			echo '<div id="header">';
				
				echo '<div class="navbar">';
					echo '<img class="left" id="logo-navbar" src="../images/logo.png" href="../index.php">';
					echo '<a class="left" href="../index.php">Cryptus</a>';
					if (isset($_SESSION['user_id'])){
						echo '<div class="dropdown right">';
							echo '<button class="dropbtn right">' . $user_info["user_name"] . '<div class="arrow-down"></div></button>';
							echo '<div class="dropdown-content">';
								echo '<a href="' . 'profile.php?user_id=' . $_SESSION['user_id'] . '">My profile</a>';
		    					echo '<a id="current" href="settings.php">Settings</a>';
		    					echo '<a href="logout.php">Logout</a>';
	  						echo '</div>';
						echo '</div>';
					}else{
						echo '<a class="right" href="../index.php">Login</a>';
					}
					echo '<form action="search.php" method="POST"><input id="search-input" class="right" placeholder="search users & cryptocurrencies" name="search-input"></form>';
				echo '</div>';

				echo '<div class="navbar">';
					echo '<a class="nav-item left" href="timeline.php">Timeline</a>';
					echo '<a class="nav-item left" href="connections.php">Connections</a>';
					echo '<a class="nav-item right" href="../threads/index.php">Discussions</a>';
					echo '<a class="nav-item right" href="#">Portfolio</a>';
				echo '</div>';

			echo '</div>';

			echo '<div class="profile-picture-header-div">';
				$jpgimg = '../profile-pictures/' . $_SESSION['user_id'] . '.' . 'jpg';
				$jpegimg = '../profile-pictures/' . $_SESSION['user_id'] . '.' . 'jpeg';
				$pngimg = '../profile-pictures/' . $_SESSION['user_id'] . '.' . 'png';
				if (file_exists ($jpgimg)){
					echo '<img id="profile-picture-header" src="' . $jpgimg . '"/>';
				} elseif (file_exists ($jpegimg)){
					echo '<img id="profile-picture-header" src="' . $jpegimg . '"/>';
				} elseif (file_exists ($pngimg)){
					echo '<img id="profile-picture-header" src="' . $pngimg . '"/>';
				} else {
					echo '<img id="profile-picture-header" src="../profile-pictures/default.jpg"/>';
				}
			echo '</div>';

			echo '<div class="container-settings-nav">';
				echo '<ul>';
					echo '<li><a href="settings.php">Profile information</a></li>';
					echo '<li><a id="current-setting" href="settings_profile_picture.php">Profile picture</a></li>';
					echo '<li><a href="settings_change_password.php">Change password</a></li>';
					echo '<li><a href="settings_delete_account.php">Delete your account</a></li>';
				echo '</ul>';
			echo '</div>';

			echo '<div class="container-settings-form">';

				echo '<h2 id="upload-succes">Successfully uploaded your image!</h2>';

			echo '</div>';

		?>
	</body>
</html>