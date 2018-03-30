<?php

	session_start();
	error_reporting(-1);
	ini_set("display_errors", 1);

	require_once('../config.inc.php');
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);
	$quser_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
	$qposts = $dbh->prepare('SELECT * FROM posts');
	$qposter_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');

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
		    					echo '<a href="settings.php">Settings</a>';
		    					echo '<a href="logout.php">Logout</a>';
	  						echo '</div>';
						echo '</div>';
					}else{
						echo '<a class="right" href="index.php">Login</a>';
					}
					echo '<form action="search.php" method="POST"><input id="search-input" class="right" placeholder="search users & cryptocurrencies" name="search-input"></form>';
				echo '</div>';

				echo '<div class="navbar">';
					echo '<a class="nav-item left" id="current" href="timeline.php">Timeline</a>';
					echo '<a class="nav-item left" href="connections.php">Connections</a>';
					echo '<a class="nav-item right" href="../threads/index.php">Discussions</a>';
					echo '<a class="nav-item right" href="#.php">Portfolio</a>';
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
						echo '<img id="profile-picture-header" src="../profile-pictures/default.jpg"/>';
					}
				echo '</div>';
			}
		?>

		<div class="container-timeline">
			<?php
				if(isset($_SESSION['user_id'])){
					echo '<div class="content-div">';
						echo '<form class="form" action="post_content.php" method="POST">';
							echo '<input class="input" id="content-title" type="text" placeholder="Title" name="content_title"></br>';
							echo '<textarea class="input" id="content" placeholder="Write something.." name="content" wrap="hard" name="content" required></textarea></br>';
							echo '<input class="button wide-button green-button" type="submit" name="signin" value="Post">';
						echo '</form>';
					echo '</div>';

					$qposts->execute();
					$posts = $qposts->fetchAll();

					foreach ($posts as $key => $part) {
						$sort[$key] = strtotime($part['post_date']);
					}
					array_multisort($sort, SORT_DESC, $posts);

					foreach ($posts as $post) {
						$qposter_info->execute(array($post['poster_id']));
						$poster_info = $qposter_info->fetch();

						echo '<div class="content-div">';

							echo '<div class="content-meta-div">';
								$jpgimg = '../profile-pictures/' . $poster_info['user_id'] . '.' . 'jpg';
								$jpegimg = '../profile-pictures/' . $poster_info['user_id'] . '.' . 'jpeg';
								$pngimg = '../profile-pictures/' . $poster_info['user_id'] . '.' . 'png';
								if (file_exists ($jpgimg)){
									echo '<img class="content-poster-profile-picture" src="' . $jpgimg . '"/>';
								} elseif (file_exists ($jpegimg)){
									echo '<img class="content-poster-profile-picture" src="' . $jpegimg . '"/>';
								} elseif (file_exists ($pngimg)){
									echo '<img class="content-poster-profile-picture" src="' . $pngimg . '"/>';
								} else {
									echo '<img class="content-poster-profile-picture" src="../profile-pictures/default.jpg"/>';
								}
								echo '<a class="content-poster-name" href="' . 'profile.php?user_id=' . $poster_info['user_id'] . '">' . $poster_info['user_name'] . '</a></br>';
								echo '<a class="content-date">' . $post['post_date'] . '</a>';
							echo '</div>';
							echo '<h3 class="content-title">' . $post['post_title'] . '</h3></br>';
							echo '<p class="content" href="#">' . $post['post_content'] . '</p>';

						echo '</div>';
					}
				}else{
					echo '<p class="error-message"><a href="../index.php">Login</a> to view or create your timeline.</p>';
				}
			?>
		</div>
		
	</body>
</html>