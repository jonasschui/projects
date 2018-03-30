<?php

	session_start();
	error_reporting(-1);
	ini_set("display_errors", 1);

	require_once('../config.inc.php');
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);
	$quser_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
	$qprofile_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
	$qposts = $dbh->prepare('SELECT * FROM posts');
	$qposter_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
	$qprofile_connections_info = $dbh->prepare('SELECT * FROM relationships WHERE (user_one_id = ? OR user_two_id=?) AND status=?');

	#if someone is logged in, retrieve his/her user-information
	if (isset($_SESSION['user_id'])){
		$quser_info->execute(array($_SESSION['user_id']));
		$user_info = $quser_info->fetch();
	}
	$qprofile_info->execute(array($_GET['user_id']));
	$profile_info = $qprofile_info->fetch();

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
								echo '<a id="current" href="' . 'profile.php?user_id=' . $_SESSION['user_id'] . '">My profile</a>';
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
					echo '<a class="nav-item left" href="timeline.php">Timeline</a>';
					echo '<a class="nav-item left" href="connections.php">Connections</a>';
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
						echo '<img id="profile-picture-header" src="../profile-pictures/default.jpg"/>';
					}
				echo '</div>';
			}
		
			echo '<div class="container-profile-information">';
				echo '<div class="profile-picture-user-info-div">';
					$jpgimg = '../profile-pictures/' . $_GET['user_id'] . '.' . 'jpg';
					$jpegimg = '../profile-pictures/' . $_GET['user_id'] . '.' . 'jpeg';
					$pngimg = '../profile-pictures/' . $_GET['user_id'] . '.' . 'png';
					if (file_exists ($jpgimg)){
						echo '<img id="profile-picture-profile-info" src="' . $jpgimg . '"/>';
					} elseif (file_exists ($jpegimg)){
						echo '<img id="profile-picture-profile-info" src="' . $jpegimg . '"/>';
					} elseif (file_exists ($pngimg)){
						echo '<img id="profile-picture-profile-info" src="' . $pngimg . '"/>';
					} else {
						echo '<img id="profile-picture-profile-info" src="../profile-pictures/default.jpg"/>';
					}
				echo '</div>';
				echo '<ul>';
					if($_SESSION['user_id'] != $profile_info['user_id']){
						echo '<div class="dropdown2">';
								echo '<button class="dropbtn2"><h2>' . $profile_info['user_name'] . '</h2><div class="arrow-down"></div></button>';
								echo '<div class="dropdown-content2">';
									if (isset($_SESSION['user_id'])) {
										if ($_GET['user_id'] < $_SESSION['user_id']) {
											$u1_id = $_GET['user_id'];
											$u2_id = $_SESSION['user_id'];
										} else {
											$u2_id = $_GET['user_id'];
											$u1_id = $_SESSION['user_id'];
										}
										$query = $dbh->prepare("SELECT * FROM relationships WHERE user_one_id = ? AND user_two_id = ?");
										$query->execute(array($u1_id, $u2_id));
										$row = $query->fetch();
                                        //var_dump($row); --> om te checken 
										if (empty($row)) {
											//Send friendship request
											echo "<a href='send_friend.php?user_id=".$_GET['user_id']."'>Send friend</a>";
										}
										if ($row['status'] == 0 && $row['action_user_id'] == $_GET['user_id']) {
											//Accept friendship request
											echo "<a href='accept_friend.php?user_id=".$_GET['user_id']."'>Accept friend</a>";
											echo "<a href='deny_friend.php?user_id=".$_GET['user_id']."'>Deny friend</a>";
										} else if ($row['status'] == 0 && $row['action_user_id'] == $_SESSION['user_id']) {
                                            //Friend request already sent, is pending for confirmation
											echo "<a href='delete_request_friend.php?user_id=".$_GET['user_id']."'>Remove friend request</a>";
										} else if ($row['status'] == 1 && $row['action_user_id'] == $_GET['user_id']) {
											//Friend request accepted by other
											echo "<a href='delete_friend.php?user_id=".$_GET['user_id']."'>Unfriend</a>";
										} else if ($row['status'] == 1 && $row['action_user_id'] == $_SESSION['user_id']) {
                                            //Friend request accepter by session user
											echo "<a href='delete_friend.php?user_id=".$_GET['user_id']."'>Unfriend</a>";
										} else if ($row['status'] == 2 && $row['action_user_id'] == $_GET['user_id']) {
                                            //Friend request denied by other
											echo "<a>Friend request denied</a>";
										} else if ($row['status'] == 2 && $row['action_user_id'] == $_SESSION['user_id']) {
                                            //Friend request denied by session user
											echo "<a href='deny_send_friend.php?user_id=".$_GET['user_id']."'>Send friend</a>";
										}
									}		  						
								echo '</div>';
						echo '</div>';
					} else {
						echo '<h2>' . $profile_info['user_name'] . '</h2>';
					}
					$qprofile_connections_info->execute(array($_GET['user_id'],$_GET['user_id'],0));
					$profile_connection_info = $qprofile_connections_info->fetchAll();
					echo '<li><p class="profile-info">' . $profile_info['first_name'] . ' ' . $profile_info['last_name'] . '</p></li>';
					echo '<li><p class="profile-info">' . $profile_info['bio'] . '</p></li>';
					echo '<li><p class="profile-info">' . count($profile_connection_info) . ' Connections</p></li>';
				echo '</ul>';
			echo '</div>';

		?>

		<div class="container-timeline">
			<?php
				if(isset($_SESSION['user_id'])){

					if($_SESSION['user_id'] == $profile_info['user_id']){

						echo '<div class="content-div">';
							echo '<form class="form" action="post_content.php" method="POST">';
								echo '<input class="input" id="content-title" type="text" placeholder="Title" name="content_title"></br>';
								echo '<textarea class="input" id="content" placeholder="Write something.." name="content" wrap="hard" name="content" required></textarea></br>';
								echo '<input class="button wide-button green-button" type="submit" name="signin" value="Post">';
							echo '</form>';
						echo '</div>';

					}

					$qposts->execute();
					$posts = $qposts->fetchAll();

					foreach ($posts as $key => $part) {
						$sort[$key] = strtotime($part['post_date']);
					}
					array_multisort($sort, SORT_DESC, $posts);

					foreach ($posts as $post) {
						$qposter_info->execute(array($post['poster_id']));
						$poster_info = $qposter_info->fetch();

						if($_GET['user_id'] == $poster_info['user_id']){
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
					}
				}
			?>
		</div>
		
	</body>
</html>