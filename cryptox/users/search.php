<?php
session_start();
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
			// For debugging:
			error_reporting(-1);
			ini_set("display_errors", 1);

			require_once('../config.inc.php');

			$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);

			#create query
			$quser_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');
			$qposts = $dbh->prepare('SELECT * FROM posts');
			$qposter_info = $dbh->prepare('SELECT * FROM Users WHERE user_id = ?');

			#if someone is logged in, retrieve his/her user-information
			if (isset($_SESSION['user_id'])){
				$quser_info->execute(array($_SESSION['user_id']));
				$user_info = $quser_info->fetch();
			}
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
		?>
					<form action="" method="POST">
						<input type="text" id="search-input" class="right" placeholder="search users & cryptocurrencies" name="search-input">
						<select name="search-type" class="right">
							<option value="user">user</option>
							<option value="currency">currency</option>
						</select>
					</form>
		<?php
		
				echo '</div>';

				echo '<div class="navbar">';
					echo '<a class="nav-item left" id="current" href="timeline.php">Timeline</a>';
					echo '<a class="nav-item left" href="connections.php">Connections</a>';
					echo '<a class="nav-item right" href="../threads/index.php">Discussions</a>';
					echo '<a class="nav-item right" href="portfolio.php">Portfolio</a>';
				echo '</div>';

			echo '</div>';
		?>
		<div class="container-timeline">
		<?php

			
			$search_input = htmlspecialchars($_POST['search-input']);
			$search_type = htmlspecialchars($_POST['search-type']);
			echo $search_type;

			// split search input
			$splitted_search_input = explode(" " , $search_input);
			$num_of_words = count($splitted_search_input);
			

			# search input is now in array, select als rows in which there is one input the same as the search word
			if ($search_type = 'user') {
				$stack = array();
				for($i = 0; $i < $num_of_words; $i++) {
					$qh = $dbh->prepare('SELECT * FROM Users
										WHERE first_name LIKE ? 
										OR last_name LIKE ? 
										OR user_name LIKE ? ;');
					# iterate over array
					$input = $splitted_search_input[$i];
					$qh -> execute(array("%".$input."%","%".$input."%","%".$input."%"));
					#supposed to print proposed rows
					$result = $qh -> fetchAll();
					foreach ($result as $fetch){
						array_push($stack, "".$fetch['user_id']." ".$fetch['first_name']." ".$fetch['last_name']."");
					}

				};
				
				$results = array_unique($stack);
				foreach ($results as $item) {
					echo "<a href=". 'profile.php?user_id=' . $item[0] . '">'.substr($item, 2)."</a>";
					echo "<br>";
				}
			} 
			
			if ($search_type = 'currency') {
				$stack = array();
				for($i = 0; $i < $num_of_words; $i++) {
					$qh = $dbh->prepare('SELECT * FROM currencies
										WHERE currency_name LIKE ? ;');
					# iterate over array
					$input = $splitted_search_input[$i];
					$qh -> execute(array("%".$input."%"));
					#supposed to print proposed rows
					$result = $qh -> fetchAll();
					foreach ($result as $fetch){
						array_push($stack, "".$fetch['currency_id']." ".$fetch['currency_name']."");
					}

				};
				
				$results = array_unique($stack);
				foreach ($results as $item) {
					echo "<a href=". 'profile.php?user_id=' .substr($item, 0,1,2). '">'.substr($item, 3)."</a>";
					echo "<br>";
				}
			} 

			// Terminate current script 
			exit();

		?>
		</div>
		
	</body>
</html>
