<?php
	session_start();
	error_reporting(-1);
	ini_set("display_errors", 1);

	require_once('../config.inc.php');
	$dbh = new PDO('mysql:host='.$config["db_host"].';dbname='.$config["db_name"], $config['db_user'], $config['db_pass']);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	#create queries
	$quser_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
	$qcurrency = $dbh->prepare('SELECT currency_name FROM currencies WHERE currency_id = ?');
	$qthread = $dbh->prepare('SELECT * FROM threads WHERE thread_id = ?');
	$qreplies = $dbh->prepare('SELECT * FROM replies WHERE thread_id = ?');
	$qthread_starter_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
	$qreply_author_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
	$qall = $dbh->prepare('SELECT * FROM currencies');

	#if someone is logged in, retrieve his/her user-information
	if (isset($_SESSION['user_id'])){
		$quser_info->execute(array($_SESSION['user_id']));
		$user_info = $quser_info->fetch();
		$user_name = $user_info['user_name'];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
		<link rel="stylesheet" href="../css/reset.css">
		<link rel="stylesheet" href="../css/main.css">
		<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<script type='text/javascript' src='../js/jquery.particleground.js'></script>
		<script type='text/javascript' src='../js/background.js'></script>
		<title>Cryptus</title>	
	</head>
	<body id="body">

		<?php
			if(isset($_SESSION['user_id'])){
				echo '<div id="header">';
					echo '<div class="navbar">';
						echo '<img class="left" id="logo-navbar" src="../images/logo.png" href="../index.php">';
						echo '<a class="left" href="../index.php">Cryptus</a>';
							echo '<div class="dropdown right">';
								echo '<button class="dropbtn right">' . $user_info["user_name"] . '<div class="arrow-down"></div></button>';
								echo '<div class="dropdown-content">';
									echo '<a href="' . '../users/profile.php?user_id=' . $_SESSION['user_id'] . '">My profile</a>';
			    					echo '<a href="../users/settings.php">Settings</a>';
			    					echo '<a href="../users/logout.php">Logout</a>';
		  						echo '</div>';
							echo '</div>';
						echo '<form action="../users/search.php" method="POST"><input id="search-input" class="right" placeholder="search users" name="search-input"></form>';
					echo '</div>';

					echo '<div class="navbar">';
						echo '<a class="nav-item left" href="../users/timeline.php">Timeline</a>';
						echo '<a class="nav-item left" href="../users/connections.php">Connections</a>';
						echo '<a class="nav-item right" id="current" href="../index.php">Discussions</a>';
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

				echo '<div class="container-timeline">';
					
					echo '<div class="content-div">';

						$qthread->execute(array($_GET['thread_id']));
						$thread = $qthread->fetch();

						$qthread_starter_info->execute(array($thread['user_id']));
						$thread_starter_info = $qthread_starter_info->fetch();

						$qcurrency->execute(array($thread['currency_id']));
						$thread_currency = $qcurrency->fetch();
						$currency_name = $thread_currency['currency_name'];

						echo '<h3 class="content-title">' . $currency_name . ': ' . $thread['thread_title'] . '</h3></br>';
						echo '<div class="content-meta-div">';
							$jpgimg = '../profile-pictures/' . $thread_starter_info['user_id'] . '.' . 'jpg';
							$jpegimg = '../profile-pictures/' . $thread_starter_info['user_id'] . '.' . 'jpeg';
							$pngimg = '../profile-pictures/' . $thread_starter_info['user_id'] . '.' . 'png';
							if (file_exists ($jpgimg)){
								echo '<img class="content-poster-profile-picture" src="' . $jpgimg . '"/>';
							} elseif (file_exists ($jpegimg)){
								echo '<img class="content-poster-profile-picture" src="' . $jpegimg . '"/>';
							} elseif (file_exists ($pngimg)){
								echo '<img class="content-poster-profile-picture" src="' . $pngimg . '"/>';
							} else {
								echo '<img class="content-poster-profile-picture" src="../profile-pictures/default.jpg"/>';
							}
							echo '<a class="content-poster-name" href="' . '../users/profile.php?user_id=' . $thread_starter_info['user_id'] . '">' . $thread_starter_info['user_name'] . '</a></br>';
							echo '<a class="content-date">' . $thread['thread_date'] . '</a>';
						echo '</div>';
						echo '<p class="reply">' . $thread['thread_content'] . '</p>';

					echo '</div>';
					
					$qreplies->execute(array($_GET['thread_id']));
					$replies = $qreplies->fetchAll();

					foreach ($replies as $key => $part) {
						$sort[$key] = strtotime($part['reply_date']);
					}
					array_multisort($sort, SORT_ASC, $replies);

					foreach ($replies as $reply) {
						$qreply_author_info->execute(array($reply['user_id']));
						$reply_author_info = $qreply_author_info->fetch();

						echo '<div class="reply-div">';

							echo '<div class="content-meta-div">';
								$jpgimg = '../profile-pictures/' . $reply_author_info['user_id'] . '.' . 'jpg';
								$jpegimg = '../profile-pictures/' . $reply_author_info['user_id'] . '.' . 'jpeg';
								$pngimg = '../profile-pictures/' . $reply_author_info['user_id'] . '.' . 'png';
								if (file_exists ($jpgimg)){
									echo '<img class="content-poster-profile-picture" src="' . $jpgimg . '"/>';
								} elseif (file_exists ($jpegimg)){
									echo '<img class="content-poster-profile-picture" src="' . $jpegimg . '"/>';
								} elseif (file_exists ($pngimg)){
									echo '<img class="content-poster-profile-picture" src="' . $pngimg . '"/>';
								} else {
									echo '<img class="content-poster-profile-picture" src="../profile-pictures/default.jpg"/>';
								}
								echo '<a class="content-poster-name" href="' . '../users/profile.php?user_id=' . $reply_author_info['user_id'] . '">' . $reply_author_info['user_name'] . '</a></br>';
								echo '<a class="content-date">' . $reply['reply_date'] . '</a>';
							echo '</div>';
							echo '<p class="reply">' . $reply['reply'] . '</p>';

						echo '</div>';
					}
<<<<<<< HEAD
					echo '<div class="content-div">';
						echo '<form action="create_reply.php" method="POST">';
							echo '<input class="input" id="reply_content" type="text" placeholder="Write a reply" name="reply_content" required></br>';
							echo '<input class="input" id="thread_id" type="hidden" name="thread_id" value="'. $_GET['thread_id'] . '"></br>';
							echo '<input class="button wide-button green-button" type=submit name="submit" value="Post reply">';
						echo '</form>';
					echo '</div>';
				echo '</div>';		
			}
			else{ # when a visitor is not logged in
=======
				echo '</div>';

			}else{ # when a visitor is not logged in
>>>>>>> 3dec21fbf6d5739f731dd75bb2db451c7b7693e1
				echo '<div id="header">';
					echo '<div class="navbar">';
						echo '<form action="search.php" method="POST"><input id="search-input" class="right" placeholder="search users & cryptocurrencies" name="search-input"></form>';
					echo '</div>';
					echo '<div class="navbar">';
						echo '<a class="nav-item right" id="current" href="../index.php">Discussions</a>';
						echo '<a class="nav-item right" href="#">About</a>';
					echo '</div>';

				echo '</div>';

				echo '<img id="logo-header-image" src="../images/logo.png" href="../index.php">';
				echo '<a id="logo-header-word" href="../index.php">Cryptus</a>';

				echo '<div class="container-threads-nologin">';

					echo '<div class="content-div">';

						$qthread->execute(array($_GET['thread_id']));
						$thread = $qthread->fetch();

						$qthread_starter_info->execute(array($thread['user_id']));
						$thread_starter_info = $qthread_starter_info->fetch();

						$qcurrency->execute(array($thread['currency_id']));
						$thread_currency = $qcurrency->fetch();
						$currency_name = $thread_currency['currency_name'];

						echo '<h3 class="content-title">' . $currency_name . ': ' . $thread['thread_title'] . '</h3></br>';
						echo '<div class="content-meta-div">';
							$jpgimg = '../profile-pictures/' . $thread_starter_info['user_id'] . '.' . 'jpg';
							$jpegimg = '../profile-pictures/' . $thread_starter_info['user_id'] . '.' . 'jpeg';
							$pngimg = '../profile-pictures/' . $thread_starter_info['user_id'] . '.' . 'png';
							if (file_exists ($jpgimg)){
								echo '<img class="content-poster-profile-picture" src="' . $jpgimg . '"/>';
							} elseif (file_exists ($jpegimg)){
								echo '<img class="content-poster-profile-picture" src="' . $jpegimg . '"/>';
							} elseif (file_exists ($pngimg)){
								echo '<img class="content-poster-profile-picture" src="' . $pngimg . '"/>';
							} else {
								echo '<img class="content-poster-profile-picture" src="../profile-pictures/default.jpg"/>';
							}
							echo '<a class="content-poster-name" href="' . '../users/profile.php?user_id=' . $thread_starter_info['user_id'] . '">' . $thread_starter_info['user_name'] . '</a></br>';
							echo '<a class="content-date">' . $thread['thread_date'] . '</a>';
						echo '</div>';
						echo '<p class="reply">' . $thread['thread_content'] . '</p>';

					echo '</div>';
					
					$qreplies->execute(array($_GET['thread_id']));
					$replies = $qreplies->fetchAll();

					foreach ($replies as $key => $part) {
						$sort[$key] = strtotime($part['reply_date']);
					}
					array_multisort($sort, SORT_ASC, $replies);

					foreach ($replies as $reply) {
						$qreply_author_info->execute(array($reply['user_id']));
						$reply_author_info = $qreply_author_info->fetch();

						echo '<div class="reply-div">';

							echo '<div class="content-meta-div">';
								$jpgimg = '../profile-pictures/' . $reply_author_info['user_id'] . '.' . 'jpg';
								$jpegimg = '../profile-pictures/' . $reply_author_info['user_id'] . '.' . 'jpeg';
								$pngimg = '../profile-pictures/' . $reply_author_info['user_id'] . '.' . 'png';
								if (file_exists ($jpgimg)){
									echo '<img class="content-poster-profile-picture" src="' . $jpgimg . '"/>';
								} elseif (file_exists ($jpegimg)){
									echo '<img class="content-poster-profile-picture" src="' . $jpegimg . '"/>';
								} elseif (file_exists ($pngimg)){
									echo '<img class="content-poster-profile-picture" src="' . $pngimg . '"/>';
								} else {
									echo '<img class="content-poster-profile-picture" src="../profile-pictures/default.jpg"/>';
								}
								echo '<a class="content-poster-name" href="' . '../users/profile.php?user_id=' . $reply_author_info['user_id'] . '">' . $reply_author_info['user_name'] . '</a></br>';
								echo '<a class="content-date">' . $reply['reply_date'] . '</a>';
							echo '</div>';
							echo '<p class="reply">' . $reply['reply'] . '</p>';

						echo '</div>';
					}
				echo '</div>';

				echo '<div class="container-login-form">';
					echo '<form class="form" action="../users/login.php" method="POST">';
						echo '<input class="input" id="username" type="text" placeholder="Username" name="user_name" required></br>';
						echo '<input class="input" id="password" type="password" placeholder="Password" name="password" required></br>';
						echo '<input class="button wide-button green-button" type="submit" name="signin" value="sign in!">';
						echo '<p id="or-register">or</p></br>';
					echo '</form>';
					echo '<button onclick=location.href="../users/register.php" class="button wide-button green-button" type="submit" name="register" value="register">register</button>';
				echo '</div>';
			}
		?>
	</body>
</html>
