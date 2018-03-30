<?php

	session_start();
	error_reporting(-1);
	ini_set("display_errors", 1);

	$currency_id = $_GET['currency_id'];

	require_once('../config.inc.php');
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);

	$qcurrency_info = $dbh->prepare('SELECT * FROM currencies WHERE currency_id = ?');
	$quser_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
	$qannouncements = $dbh->prepare('SELECT * FROM announcements WHERE announcer_id = ?');

	#if someone is logged in, retrieve his/her user-information
	if (isset($_SESSION['user_id'])){
		$quser_info->execute(array($_SESSION['user_id']));
		$user_info = $quser_info->fetch();
	}

	$qcurrency_info->execute(array($currency_id));
	$currency_info = $qcurrency_info->fetch();

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
								echo '<a id="current" href="' . '../users/profile.php?user_id=' . $_SESSION['user_id'] . '">My profile</a>';
		    					echo '<a href="../users/settings.php">Settings</a>';
		    					echo '<a href="../users/logout.php">Logout</a>';
	  						echo '</div>';
						echo '</div>';
					}else{
						echo '<a class="right" href="../index.php">Login</a>';
					}
					echo '<form action="search.php" method="POST"><input id="search-input" class="right" placeholder="search users & cryptocurrencies" name="search-input"></form>';
				echo '</div>';

				echo '<div class="navbar">';
					echo '<a class="nav-item left" href="../users/timeline.php">Timeline</a>';
					echo '<a class="nav-item left" href="#">Connections</a>';
					echo '<a class="nav-item right" href="../threads/index.php">Discussions</a>';
					echo '<a class="nav-item right" href="#">Portfolio</a>';
				echo '</div>';

			echo '</div>';

			if(isset($_SESSION['user_id'])){
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
						echo '<img id="profile-picture-header" src="../images/default.jpg"/>';
					}
				echo '</div>';
			}
		
			echo '<div class="container-profile-information">';
				echo '<div class="profile-picture-user-info-div">';
					$img = '../currency-icons/' . $_GET['currency_id'] . '.' . 'png';
					echo '<img id="profile-picture-profile-info" src="' . $img . '"/>';
				echo '</div>';
				echo '<ul>';
					if(isset($_SESSION['user_id'])){
						echo '<div class="dropdown2">';
								echo '<button class="dropbtn2"><h2>' . $currency_info['currency_id'] . '</h2><div class="arrow-down"></div></button>';
								echo '<div class="dropdown-content2">';
									#if this person is not a friend, show 'follow' option
									echo '<a href="#">Follow</a>';
									#else show 'unfollow'
		  						echo '</div>';
						echo '</div>';
					} else {
						echo '<h2>' . $currency_info['currency_id'] . '</h2>';
					}
					echo '<li><p class="profile-info">' . $currency_info['currency_name'] . '</p></li>';
					echo '<li><p class="profile-info">' . $currency_info['currency_description'] . '</p></li>';
					echo '<li><p class="profile-info"><a href="' . $currency_info['currency_url'] . '">' . $currency_info['currency_url'] . '</a></p></li>';
					echo '<li><p class="profile-info">Followers</p></li>';
				echo '</ul>';
			echo '</div>';

		?>

		<div class="container-timeline">
			<?php

				$qannouncements->execute(array($currency_id));
				$announcements = $qannouncements->fetchAll();

				$qcurrency_info->execute(array($announcement['announcer_id']));
				$currency_info = $qcurrency_info->fetch();

				foreach ($announcements as $key => $part) {
					$sort[$key] = strtotime($part['post_date']);
				}
				array_multisort($sort, SORT_DESC, $announcements);

				foreach ($announcements as $announcement) {

					echo '<div class="content-div">';

						echo '<div class="content-meta-div">';
							$img = '../currency-icons/' . $currency_info['currency_id'] . '.' . 'png';
							echo '<img class="content-poster-profile-picture" src="' . $img . '"/>';
							echo '<a class="content-currency-name" href="' . 'profile.php?user_id=' . $currency_info['currency_id'] . '">' . $currency_info['currency_name'] . '</a></br>';
							echo '<a class="content-date">' . $announcement['announcement_date'] . '</a>';
						echo '</div>';
						echo '<h3 class="content-title">' . $announcement['announcement_title'] . '</h3></br>';
						echo '<p class="content" href="#">' . $announcement['announcement_content'] . '</p>';

					echo '</div>';
				}
			?>
		</div>
		
	</body>
</html>